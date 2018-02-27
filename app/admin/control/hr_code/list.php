<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*人员业务代码查询
*/

$code = '';
$fields = $join = $where = $hr_where = $str_r = '';
$url_param = '?';
$qualification_select=f_select('qualification');

extract( $_GET, EXTR_SKIP );
foreach( $_GET as $k => $v ){
	if( 'paged' == $k ) continue;
	$url_param .= "$k=$v&";
}
$url_param = substr( $url_param, 0, -1 );
$name= trim($name);
$easycode = trim($easycode);
$h_code = trim($h_code);
$qua_no = trim($qua_no);
if( $name ){
	$uids = array();
	$query = $db->query( "SELECT id FROM sp_hr WHERE 1 AND name like '%$name%'");
	while( $rt = $db->fetch_array( $query ) ){
		$uids[] = $rt['id'];
	}
	if( $uids ){
		$where .= " AND hac.uid IN (".implode(',',$uids).")";
	} else {
		$where .= " AND hac.id < -1";
	}
}
if( $easycode ){
	$uids = array();
	$query = $db->query("SELECT id FROM sp_hr WHERE 1 AND easycode like '%$easycode%'");
	while( $rt = $db->fetch_array( $query ) ){
		$uids[] = $rt['id'];
	}
	if( $uids ){
		$where .= " AND hac.uid IN (".implode(',',$uids).")";
	} else {
		$where .= " AND hac.id < -1";
	}
}
$qualification=getgp('qualification');
if($qualification){
	$where .= " AND hq.qua_type = '$qualification' ";
	$qualification_select = str_replace( "value=\"$qualification\">", "value=\"$qualification\" selected>" , $qualification_select );
}

//合同来源限制
$len = get_ctfrom_level( current_user( 'ctfrom' ) );
if( $ctfrom && substr( $ctfrom, 0, $len ) == substr( current_user( 'ctfrom' ), 0, $len ) ){
	$_len = get_ctfrom_level( $ctfrom );
	$len = $_len;
} else {
	$ctfrom = current_user( 'ctfrom' );
}


switch( $len ){
	case 2	: $add = 1000000; break;
	case 4	: $add = 10000; break;
	case 6	: $add = 100; break;
	case 8	: $add = 1; break;
}

$ctfrom_e = sprintf("%08d",$ctfrom+$add);
if( '01000000' != $ctfrom ){
	$where .= " AND hac.ctfrom >= '$ctfrom' AND hac.ctfrom < '$ctfrom_e'";
}
$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );

if($iso){
	$where .= " AND hac.iso = '$iso' ";
	$iso_select = str_replace( "value=\"$iso\">", "value=\"$iso\" selected>" , $iso_select );
}
if($audit_code= trim($audit_code)){
	$where .= " AND hac.audit_code like '$audit_code%' ";
}
if($use_code= trim($use_code)){
	$where .= " AND hac.use_code like '$use_code%' ";
}
if($pass_date_s){
	$where .= " AND hac.pass_date >= '$pass_date_s' ";
}
if($pass_date_e){
	$where .= " AND hac.pass_date <= '$pass_date_e' ";
}
//人员在职 且 注册资格有效
$is_hire=getgp('is_hire');
if($is_hire){ //搜索用
	 $where.=" AND hr.is_hire=$is_hire";
	$f_is_hire = str_replace( "value=$is_hire>", "value=$is_hire selected>" , $f_is_hire );
}else{
	$where.=" AND (hr.is_hire = 1 or hr.is_hire = 3)";
}
$where .= "  AND hr.deleted = 0  AND hac.deleted = 0";

$join .= " LEFT JOIN sp_hr_qualification hq ON hq.id = hac.qua_id";
$join .= " LEFT JOIN sp_hr hr ON hr.id = hac.uid";

$sql = "SELECT COUNT(*) FROM sp_hr_audit_code hac $join WHERE 1 $where";
if( !$export){
	$total = $db->get_var($sql);
	$pages = numfpage( $total, 20, $url_param );
	
}/*
if($export){
	$where.=" AND length(hac.use_code)=7";
}
*/
$hacs = array();
$sql = "SELECT hac.*,hr.code,hr.name,hr.ctfrom,hr.audit_job,hr.is_hire FROM sp_hr_audit_code hac $join WHERE 1 $where order by hac.use_code asc $pages[limit]";

$query = $db->query($sql);
$data_codes=array();
while( $rt = $db->fetch_array( $query ) ){
	
	$rt['qua_type_V'] = f_qua_type( $rt['qua_type'] );
	$rt['iso_V'] = f_iso( $rt['iso'] );
	$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
	$rt['source'] = f_source( $rt['source'] );
	$rt['audit_job'] = f_audit_job( $rt['audit_job'] );
	$rt['status_V'] = $status_array[$rt['status']];
	$rt['is_assess_V'] = ($rt['is_assess'])?'是':'否';
	$rt['is_hire_V']=$hr_is_hire[$rt['is_hire']];

	$hacs[$rt['id']] = chk_arr($rt);
	
}

if( !$export ){

	tpl('hr/hr_code_list');
} else {

    ob_start();
	tpl( 'xls/list_hr_code' );
	$data = ob_get_contents();
	ob_end_clean();
	export_xls( '人员专业代码列表', $data );
}


?>