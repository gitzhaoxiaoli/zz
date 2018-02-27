<?php
!defined('IN_SUPU') && exit('Forbidden');

$wenshen_status = (int)getgp("wenshen_status") == 0 ? 2 : (int)getgp("wenshen_status");
$fields = $where = $join = $page_str = '';
/******************************
 #			搜   索			  #
 ******************************/


//@wangp 传递过来的参数加 trim 去除两侧的空格 2013-09-25 9:22
$ep_name		= trim( getgp( 'ep_name' ) ); //企业名称
$ctfrom			= getgp( 'ctfrom' ); //合同来源
$audit_type		= getgp( 'audit_type' ); //审核类型
$ct_code		= trim( getgp( 'ct_code' ) ); //合同编号
$cti_code		= trim( getgp( 'cti_code' ) ); //合同项目编号
$audit_code		= trim( getgp( 'audit_code' ) ); //审核代码
$is_first		= getgp( 'is_first' ); //是否初次
$iso			= getgp( 'iso' ); //认证体系
$audit_ver = getgp( 'audit_ver' );
$audit_start_start	= trim( getgp('audit_start_start') ); //审核开始时间 起
$audit_start_end= trim( getgp('audit_start_end') ); //审核开始时间 止
$audit_end_start= trim( getgp('audit_end_start') ); //审核结束时间 起
$audit_end_end	= trim( getgp('audit_end_end') ); //审核结束时间 止

$export			= getgp( 'export' );


//企业名称
if( $ep_name ){
	$_eids = array();
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%".str_replace('%','\%',$ep_name)."%'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	if( $_eids ){
		$where .= " AND t.eid IN (".implode(',',$_eids).")";
	} else {
		$where .= " AND t.id < -1";
	}
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
//合同编号
if( $ct_code ){
	$_tids=array();
	$sql="SELECT tid FROM sp_project WHERE ct_code = '$ct_code'";
	$query=$db->query($sql);
	while( $rt = $db->fetch_array( $query ) ){
		$_tids[]=$rt[tid];
	}
	if($_tids){
		$where .= " AND t.id in (".implode(',',$_tids).")";
	} else {
		$where .= " AND t.id < -1";
	}
	unset( $_tids, $query, $rt );
}
//项目编号
if( $cti_code ){
	$_tids=array();
	$sql="SELECT tid FROM sp_project WHERE cti_code like '%$cti_code%' and deleted=0";
	$query=$db->query($sql);
	while( $rt = $db->fetch_array( $query ) ){
		$_tids[]=$rt[tid];
	}
	if($_tids){
		$where .= " AND t.id in (".implode(',',$_tids).")";
	} else {
		$where .= " AND t.id < -1";
	}
	unset( $_tids, $query, $rt );
}

 
//认证体系
if( $iso ){
	$where .= " AND t.iso LIKE '%$iso%'";
	$iso_select = str_replace( "value=\"$iso\">", "value=\"$iso\" selected>", $iso_select);
}
if($audit_ver) { //标准版本
	$_tids = array();
	$query = $db->query("SELECT tid FROM sp_project WHERE audit_ver = '$audit_ver'");
	while( $rt = $db->fetch_array( $query ) ){
		$_tids[] = $rt['tid'];
	}
	if( $_tids ){
		$where .= " AND t.id IN (".implode(',',$_tids).")";
	}else {
		$where .= " AND t.id < -1";
	}
	unset( $_tids, $query, $rt );
}

//审核类型
if( $audit_type ){
	$where .= " AND t.audit_type like '%$audit_type%'";
	$audit_type_select = str_replace( "value=\"$audit_type\">", "value=\"$audit_type\" selected>", $audit_type_select);
}
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


$where .= " AND t.deleted = '0'";


$state_total = array(0,0,0,0);

//计算数量
if( !$export ){
	$query = $db->query("SELECT t.wenshen_status,COUNT(*) total FROM sp_task t  WHERE 1 $where AND t.wenshen_status IN (2,3) GROUP BY t.wenshen_status");
	while( $rt = $db->fetch_array( $query ) ){
		$state_total[$rt['wenshen_status']] = $rt['total'];
	}
	$pages = numfpage( $state_total[$wenshen_status]);
}


$where .= " AND t.wenshen_status = '$wenshen_status'";

//审核任务
$tasks = array();
$query = $db->query( "SELECT t.*,e.ep_name,e.areacode,e.person,e.person_tel,e.ep_phone,e.ep_fax,e.ep_level,hr.name FROM sp_task t LEFT JOIN sp_enterprises e ON e.eid = t.eid LEFT JOIN sp_hr hr ON hr.id = t.create_uid WHERE 1 $where ORDER BY t.te_date DESC $pages[limit]" );
while( $rt = $db->fetch_array( $query ) ){
	$rt[num]=mkdate($rt['tb_date'],$rt['te_date']);
	$rt['tb_date'] = mysql2date( 'Y-m-d H:i', $rt['tb_date'] );
	$rt['te_date'] = mysql2date( 'Y-m-d H:i', $rt['te_date'] );
	$rt['province']		= f_region_province( $rt['areacode'] );
	$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
	$rt['create_date'] = mysql2date( 'Y-m-d', $rt['create_date'] );
	if($export)
		$rt[last_date]=$db->get_var("SELECT te_date FROM `sp_task` WHERE `eid` = '$rt[eid]' AND `deleted` = '0' AND `wenshen_status` = '3' AND `id` < '$rt[id]' ORDER BY `id` DESC ");
	$tasks[$rt['id']] = chk_arr($rt);
}



if( $tasks){


	$fields .= "p.tid,p.ct_id,p.id,p.audit_ver,p.audit_type,p.cti_code,p.use_code,p.audit_code,p.iso";


	$sql = "SELECT $fields FROM sp_project p $join WHERE 1 AND p.tid IN (".implode(',',array_keys($tasks)).") AND p.deleted = 0 ORDER BY p.iso " ;
	$query = $db->query( $sql);
	while( $rt = $db->fetch_array( $query ) ){
		isset( $tasks[$rt['tid']]['audit_vers'] ) or $tasks[$rt['tid']]['audit_vers'] = array();
		isset( $tasks[$rt['tid']]['audit_types'] ) or $tasks[$rt['tid']]['audit_types'] = array();
		isset( $tasks[$rt['tid']]['cti_codes'] ) or $tasks[$rt['tid']]['cti_codes'] = array();
		isset( $tasks[$rt['tid']]['use_codes'] ) or $tasks[$rt['tid']]['use_codes'] = array();
		isset( $tasks[$rt['tid']]['style'] ) or $tasks[$rt['tid']]['style'] = array();
		$rt[use_code]=explode("；",$rt[use_code]);
		$rt[use_code]=array_unique($rt[use_code]);
		$rt[use_code]=join("；",$rt[use_code]);
		$tasks[$rt['tid']]['audit_vers'][] = f_audit_ver( $rt['audit_ver'] );
		$tasks[$rt['tid']]['audit_types'][] = f_audit_type( $rt['audit_type'] );
		$tasks[$rt['tid']]['cti_codes'][] = $rt['cti_code'];
		$tasks[$rt['tid']]['use_codes'][] = $rt['use_code'];
		$tasks[$rt['tid']]['audit_codes'][] = $rt['audit_code'];
	}


//审核组信息
	 $team_fields = $team_join = $team_where = '';

	$team_fields .= "tat.iso,tat.role,tat.qua_type,tat.tid,tat.name,tat.witness,tat.witness_person,tat.use_code";


	$team_where = " AND tat.tid IN (".implode(',',array_keys($tasks)).")";
    
	
	$sql1 = "SELECT $team_fields FROM sp_task_audit_team tat WHERE 1 AND tat.deleted = 0 $team_where  order by iso";

	$query1 = $db->query( $sql1 );
	
	$temp="";
	while( $row = $db->fetch_array( $query1 ) ){
	$tasks[$row['tid']]['audit'][]=$row;
		
	}
	 
} 

if( !$export ){
	tpl();
} else {
	ob_start();
	tpl( 'xls/list_task' );
	$data = ob_get_contents();
	ob_end_clean();

	export_xls( '项目计划列表', $data );
}
?>