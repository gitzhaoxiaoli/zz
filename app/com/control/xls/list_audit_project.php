<?php
!defined('IN_SUPU') && exit('Forbidden');


$project_status = array('未安排','待派人','待审批','已审批','','维护');

$audit_pids = $assess_pids = array();

$fields = $join = $where = $page_str = '';
$ep_name = getgp( 'ep_name' );
$ctfrom = getgp( 'ctfrom' );
$ct_code = getgp( 'ct_code' );
$cti_code = getgp( 'cti_code' );
$iso = getgp( 'iso' );
$audit_type = getgp( 'audit_type' );

$assess_date_start = getgp( 'assess_date_start' );
$assess_date_end = getgp( 'assess_date_end' );

$audit_start_start = getgp( 'audit_start_start' );
$audit_start_end = getgp( 'audit_start_end' );
$audit_end_start = getgp( 'audit_end_start' );
$audit_end_end = getgp( 'audit_end_end' );

if( $ep_name ){
	$_eids = array();
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%".str_replace('%','\%',$ep_name)."%'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	if( $_eids ){
		$where .= " AND p.eid IN (".implode(',',$_eids).")";
	}
	$page_str .='&ep_name='.$ep_name;
}

//项目所属单位限制
$len = get_ctfrom_level( nowUsr( 'ctfrom' ) );

if( $ctfrom && substr( $ctfrom, 0, $len ) == substr( nowUsr( 'ctfrom' ), 0, $len ) ){
	$_len = get_ctfrom_level( $ctfrom );
	$len = $_len;
} else {
	$ctfrom = nowUsr( 'ctfrom' );
}
$last = substr($ctfrom,$len - 1,1);
$ctfrom_e = substr( $ctfrom, 0, $len -1 ).($last+1);
$_i = 8 - $len;
for( $i = 0; $i < $_i; $i++ ){
	$ctfrom_e .= '0';
}
$where .= " AND p.ctfrom >= '$ctfrom' AND p.ctfrom < '$ctfrom_e'";

$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );


//申请编码
if( $ct_code ){
	$ct_id = $db->get_var("SELECT ct_id FROM sp_contract WHERE ct_code = '$ct_code'");
	if( $ct_id ){
		$where .= " AND p.ct_id = '$ct_id'";
	} else {
		$where .= " AND p.id < -1 ";
	}
	unset( $ct_id );
}

//批次申请编号
if( $cti_code ){
	$cti_id = $db->get_var("SELECT cti_id FROM sp_contract_item WHERE cti_code like '%$cti_code%'");
	if( $cti_id ){
		$where .= " AND ". $db->sqls(array('p.cti_id'=>$cti_id));
		
	} else {
		$where .= " AND p.id < -1";
	}
}

//认证体系
if( $iso ){
	$where .= " AND p.iso = '$iso'";
	$iso_select = str_replace( "value=\"$iso\">", "value=\"$iso\" selected>", $iso_select);
}

//检查类型
if( $audit_type ){
	$where .= " AND p.audit_type = '$audit_type'";
	$audit_type_select = str_replace( "value=\"$audit_type\">", "value=\"$audit_type\" selected>", $audit_type_select);
}

$task_where = '';
//检查开始时间 起
if( $audit_start_start ){
	$task_where .= " AND tb_date >= '$audit_start_start'";
}
//检查开始时间 止
if( $audit_start_end ){
	$task_where .= " AND tb_date <= '$audit_start_end'";
}
//检查结束时间 起
if( $audit_end_start ){
	$task_where .= " AND te_date >= '$audit_end_start'";
}
//检查结束时间 止
if( $audit_end_end ){
	$task_where .= " AND te_date <= '$audit_end_end'";
}
if( $task_where ){
	$query = $db->query("SELECT tv.pid WHERE sp_task_vice tv INNER JOIN sp_task t ON t.id = tv.tid WHERE $task_where");
	while( $rt = $db->fetch_array( $query ) ){
		$audit_pids[] = $rt['pid'];
	}
}
//审定时间 起
if( $assess_date_start ){
	$assess_where .= " AND assess_date >= '$assess_date_start'";
}
 
if( $audit_pids && $assess_pids ){
	$where .= " AND p.id IN (".implode( ',', array_intersect( $audit_pids, $assess_pids ) ).")";
} elseif( $audit_pids ){
	$where .= " AND p.id IN (".implode(',',$audit_pids).")";
} elseif( $assess_pids ){
	$where .= " AND p.id IN (".implode(',',$assess_pids).")";
}



$where .= " AND p.deleted = '0'";

$fields .= "p.*,ct.ct_code,cti.cti_code,e.ep_name,e.ctfrom,t.tb_date,t.te_date";

/* 关联表 */
$join .= " LEFT JOIN sp_enterprises e ON e.eid = p.eid";
$join .= " LEFT JOIN sp_contract ct ON ct.ct_id = p.ct_id";
$join .= " LEFT JOIN sp_contract_item cti ON cti.cti_id = p.cti_id"; 
$join .= " LEFT JOIN sp_task_vice tv ON tv.pid = p.id";
$join .= " LEFT JOIN sp_task t ON t.id = tv.tid";

$sql = "SELECT $fields FROM sp_project p $join WHERE 1 $where ORDER BY p.id DESC" ;

$query = $db->query( $sql );

while( $rt = $db->fetch_array( $query ) ){
	$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
	$rt['audit_type_V'] = read_cache('audit_type', $rt['audit_type'] );
	$rt['iso_V'] = read_cache('iso', $rt['iso'] );
	$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
	$rt['cert_states'] = array();
	$rt['tb_date'] = mysql2date( 'Y-m-d', $rt['tb_date'] );
	$rt['te_date'] = mysql2date( 'Y-m-d', $rt['te_date'] );
	$rt['status_V'] = $project_status[$rt['status']];
	$auditLists[$rt['id']] = $rt;
}
//取证书状态
if( $auditLists ){
	$query = $db->query("SELECT pid,mark,status FROM sp_certificate WHERE pid IN ('".implode("','",array_keys( $auditLists ) )."')");
	while( $rt = $db->fetch_array( $query ) ){
		isset( $auditLists[$rt['pid']]['cert_states'] ) or $auditLists[$rt['pid']]['cert_states'] = array();
		if( !$rt['status'] ) continue;
		$auditLists[$rt['pid']]['cert_states'][] = f_mark( $rt['mark'] ).'：'.f_certstate( $rt['status'] );
	}
}



//输出Execl文件
$filename = iconv( 'UTF-8', 'GB2312', '检查项目列表_').mysql2date( "Y-m-d", nowTime( 'mysql' ) ).".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename );
header("Pragma: no-cache");
header("Expires: 0");

template( 'xls/list_audit_project' );

?>