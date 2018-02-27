<?php
!defined('IN_SUPU') && exit('Forbidden');

$project_status = array( '未安排', '待派人', '待审批', '已审批' );

$status = (int)getgp( 'status' );


/******************************
 #			搜   索			  #
 ******************************/

$fields = $join = $where = '';

$ep_name		= getgp( 'ep_name' ); //委托人名称
$ctfrom			= getgp( 'ctfrom' ); //项目所属单位
$audit_type		= getgp( 'audit_type' ); //检查类型
$ct_code		= getgp( 'ct_code' ); //申请编码
$cti_code		= getgp( 'cti_code' ); //批次申请编号
$audit_code		= getgp( 'audit_code' ); //检查代码
$is_first		= getgp( 'is_first' ); //是否初次
$iso			= getgp( 'iso' ); //认证体系
$pre_date_start	= getgp('pre_date_start'); //计划时间
$pre_date_end	= getgp('pre_date_end'); //计划时间
$final_date_start	= getgp( 'final_date_start' ); //最后监察时间 起
$final_date_end	= getgp( 'final_date_end' ); //最后监察时间 止


//委托人名称
if( $ep_name ){
	$_eids = array();
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%".str_replace('%','\%',$ep_name)."%'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	if( $_eids ){
		$where .= " AND p.eid IN (".implode(',',$_eids).")";
	} else {
		$where .= " AND p.id < -1";
	}
	unset( $_eids, $_query, $rt );
}

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
	$cti_id = $db->get_var("SELECT ct_id FROM sp_contract_item WHERE cti_code like '%$cti_code%'");
	if( $cti_id ){
		$where .= " AND ". $db->sqls(array('p.cti_id'=>$cti_id));
	} else {
		$where .= " AND p.id < -1";
	}
	$page_str .= '&cti_code='.$cti_code;
}

//认证体系
if( $iso ){
	$where .= " AND p.iso = '$iso'";
}

//检查类型
if( $audit_type ){
	$where .= " AND p.audit_type = '$audit_type'";
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

//计划开始时间 开始
if( $pre_date_start ){
	$where .= " AND p.pre_date >= '$pre_date_start'";
}
//计划开始时间 结束
if( $pre_date_end ){
	$where .= " AND p.pre_date <= '$pre_date_end'";
}
//最后监察日 开始
if( $final_date_start ){
	$where .= " AND p.final_date >= '$final_date_start'";
}
//最后监察日 结束
if( $final_date_end ){
	$where .= " AND p.final_date <= '$final_date_end'";
}


//要获取的字段
$fields .= "p.*,e.ep_name,e.ctfrom,ct.ct_code,ct.pre_date,ci.cti_code";

//要关联的表
$join .= " LEFT JOIN sp_enterprises e ON e.eid = p.eid";
$join .= " LEFT JOIN sp_contract ct ON ct.ct_id = p.ct_id";
$join .= " LEFT JOIN sp_contract_item ci ON ci.cti_id = p.cti_id";

$where .= " AND p.is_drop = '0' AND p.deleted = '0'";

$where .= " AND p.status = '0'";
$sql = "SELECT $fields FROM sp_project p $join WHERE 1 $where ORDER BY p.id DESC $pages[limit]";

$auditLists = array();

$query = $db->query( $sql );
while( $rt = $db->fetch_array( $query ) ){
	$rt['audit_ver'] = f_audit_ver( $rt['audit_ver'] );
	$rt['audit_type'] = read_cache('audit_type', $rt['audit_type'] );
	$rt['ctfrom'] = f_ctfrom( $rt['ctfrom'] );
	$rt['final_date'] == '0000-00-00' && $rt['final_date'] = '';
	$rt['status'] = $project_status[$rt['status']];
	$auditLists[$rt['id']] = $rt;
}





//输出Execl文件
$filename = iconv( 'UTF-8', 'GB2312', '未安排项目').mysql2date( "Y-m-d", nowTime( 'mysql' ) ).".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename );
header("Pragma: no-cache");
header("Expires: 0");

template( 'xls/list_project' );

?>