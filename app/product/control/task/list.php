<?php
!defined('IN_SUPU') && exit('Forbidden');

$status_1 = '待派人';
$status_2 = '待审批';
$status_3 = '已审批';
$status_4 = '已安排';

$status = (int)getgp('status');

$fields = $where = $join = $page_str = '';
/******************************
 #			搜   索			  #
 ******************************/


//@wangp 传递过来的参数加 trim 去除两侧的空格 2013-09-25 9:22
extract($_GET);
$status	= (int)$status;

${"tab_".$status} = "ui-tabs-active ui-state-active";
//生产企业
if( $ep_name = trim($ep_name)){
	$where .= " AND e.ep_name like '%$ep_name%'";
}
if($fac_code=trim($fac_code)){
    $res = $db->find_one("enterprises",array('fac_code'=>$fac_code),"eid");
    $where .= "AND t.eid = '$res[eid]'";
}

//省份
if( $areacode=getgp("areacode") ){
	$pcode = substr($areacode,0,2) . '0000';
	$_eids = array(-1);
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '".substr($areacode,0,2)."'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	$where .= " AND t.eid IN (".implode(',',$_eids).")";
	unset( $_eids, $_query, $rt, $_eids );
	
	$province_select = str_replace( "value=\"$pcode\">", "value=\"$pcode\" selected>" , $province_select );
}
//
if( $task_code = trim($task_code)){
	$where .= " AND t.task_code like '%$task_code%'";
}

//审核类型  任务表没有存audit_type（在项目表）
/*
if( $audit_type ){
	$where .= " AND p.audit_type = '%$audit_type%'";
	$audit_type_select = str_replace( "value=\"$audit_type\">", "value=\"$audit_type\" selected>", $audit_type_select);
}
*/

//审核开始时间 起
if( $audit_start_start ){
	$where .= " AND t.tb_date >= '$audit_start_start 00:00:00'";
}
//审核开始时间 止
if( $audit_start_end ){
	$where .= " AND t.tb_date <= '$audit_start_end 23:00:00'";
}
//审核结束时间 起
if( $audit_end_start ){
	$where .= " AND t.te_date >= '$audit_end_start 00:00:00'";
}
//审核结束时间 止
if( $audit_end_end ){
	$where .= " AND t.te_date <= '$audit_end_end 23:00:00'";
}


$where .= " AND t.deleted = '0' AND t.iso_prod_type = 1 ";


$state_total = array(0,0,0,0);

//计算数量
if( !$export ){
	$query = $db->query("SELECT t.status,COUNT(*) total FROM sp_task t LEFT JOIN sp_enterprises e ON e.eid = t.eid WHERE 1 $where AND t.status IN (1,2,3,4)  GROUP BY t.status");
	while( $rt = $db->fetch_array( $query ) ){
		$state_total[$rt['status']] = $rt['total'];
	}
	$pages = numfpage( $state_total[$status]);
}
$where .= " AND t.status = '$status'";

//审核任务
$tasks = array();
// $query = $db->query( "SELECT t.*,e.ep_name,e.areacode,hr.name FROM sp_task t LEFT JOIN sp_enterprises e ON e.eid = t.eid LEFT JOIN sp_hr hr ON hr.id = t.create_uid  WHERE 1 $where  ORDER BY t.te_date DESC $pages[limit]" );

$query = $db->query( "SELECT t.*,e.ep_name,e.areacode,hr.name,p.audit_type FROM sp_task t LEFT JOIN sp_enterprises e ON e.eid = t.eid LEFT JOIN sp_project p ON p.tid = t.id LEFT JOIN sp_hr hr ON hr.id = t.create_uid  WHERE 1 $where  ORDER BY t.te_date DESC $pages[limit]" );

while( $rt = $db->fetch_array( $query ) ){
	$rt['tb_date'] = mysql2date( 'Y-m-d', $rt['tb_date'] );
	$rt['te_date'] = mysql2date( 'Y-m-d', $rt['te_date'] );
	$rt['province']		= f_region_province( $rt['areacode'] );
	$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
	$rt['create_date'] = mysql2date( 'Y-m-d', $rt['create_date'] );
	$_query = $db->query("SELECT role,name FROM `sp_task_audit_team` WHERE `tid` = '$rt[id]' AND deleted = 0");

	while( $_rt = $db->fetch_array( $_query ) ){
		if($_rt[role] == '1001')
			$rt[leader] = $_rt[name];
		else
			$rt[auditor][] = $_rt[name];
	}
	$rt[auditor] && $rt[auditor] = join("；",$rt[auditor]);
	$tasks[$rt['id']] = chk_arr($rt);
}
// p($tasks);die;
if( !$export ){
	tpl( "task/list" );
} else {
	ob_start();
	tpl( 'xls/list_task' );
	$data = ob_get_contents();
	ob_end_clean();

	export_xls( '检查派人', $data );
}
?>