<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*证书查询列表
*/
$mark_select=f_select('mark');
$iso_select = f_select('iso','',array("B01","B05")); //认证体系
$audit_ver_select = f_select('audit_ver');//体系版本
$audit_type_select=f_select('audit_type','',array('1001','1004-1','1004-2','1004-3','1004-4'));
$certstate_select=f_select('certstate');
$fields = $join = $where = '';
extract( $_GET, EXTR_SKIP );
//合同来源
$ctfrom_select = f_ctfrom_select();

//企业名称
$ep_name = trim($ep_name);
if( $ep_name ){
	$where .=" AND cert.cert_name like '%$ep_name%'";
}

//生产者名称
if($ep_manu_id = trim($ep_manu_id)){
    $where .=" AND cert.manu_name like '%$ep_manu_id%'";
}
//生产企业名称
if($ep_prod_id = trim($ep_prod_id)){
    $where .=" AND cert.pro_name like '%$ep_prod_id%'";
}
if($fac_code=trim($fac_code)){
    $res = $db->find_one("enterprises",array('fac_code'=>$fac_code),"eid");
    $where .= "AND ep_prod_id = '$res[eid]'";
}

//省份
if( $areacode=getgp("areacode") ){
	$pcode = substr($areacode,0,2) . '0000';
	$_eids = array(-1);
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '".substr($areacode,0,2)."'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	$where .= " AND cert.eid IN (".implode(',',$_eids).")";
	unset( $_eids, $_query, $rt, $_eids );
	
	$province_select = str_replace( "value=\"$pcode\">", "value=\"$pcode\" selected>" , $province_select );
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
$where .= " AND cert.ctfrom >= '$ctfrom' AND cert.ctfrom < '$ctfrom_e'";
$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );
unset( $len, $_len );



if( $s_dates ){ //注册时间 起
	$where .= " AND cert.s_date >= '$s_dates'";
}
if( $s_datee ){ //注册时间 止
	$where .= " AND cert.s_date <= '$s_datee'";
}

if( $e_dates ){ //到期时间 起
	$where .= " AND cert.e_date >= '$e_dates'";
}
if( $e_datee ){ //到期时间 止
	$where .= " AND cert.e_date <= '$e_datee'";
}
if( $first_dates ){ //到期时间 起
	$where .= " AND cert.e_date >= '$first_dates'";
}
if( $first_datee ){ //到期时间 止
	$where .= " AND cert.e_date <= '$first_datee'";
}
if( $cti_code=trim($cti_code) ){ //合同项目编码
	$where .= " AND cert.cti_code like '%$cti_code%'";
}

if( $note=trim($note) ){ //合同项目编码
	$where .= " AND cert.note like '%$note%'";
}
if( $iso = getgp("iso") ){ //认证体系
	$where .= " AND cert.iso = '$iso'";
	//$iso_select = str_replace("value=\"$iso\">","value=\"$iso\" selected>",$iso_select);
}

$scope = trim($scope);
if( $scope ){
	$where .= " AND cert_scope LIKE '%$scope%'";
}


if( $certno=trim($certno) ){
	$where .= " AND cert.certno like '%$certno%'";

}

if( $prod_id=trim($prod_id) ){
	$where .= " AND cert.prod_id = '$prod_id'";
}

if( $certstate ){
	$where .= " AND cert.status = '$certstate'";
	$certstate_select = str_replace("value=\"$certstate\">","value=\"$certstate\" selected>",$certstate_select);

}
$where .= " AND cert.deleted = 0";

$where .= " AND cert.status <> ''";


	$join .= " LEFT JOIN sp_enterprises  e ON e.eid = cert.eid ";
    // $join .= " LEFT JOIN sp_project p ON p.id = cert.pid";

if( !$export and !$export1){
	$total = $db->get_var("SELECT COUNT(*) FROM sp_certificate cert $join WHERE 1 $where and cert.iso_prod_type = 1");
	$en_total = $db->get_var("SELECT COUNT(distinct(ep_prod_id)) FROM sp_certificate cert $join WHERE 1 $where and cert.iso_prod_type = 1");
	$pages = numfpage( $total);
}

$sql = "SELECT cert.* FROM sp_certificate cert $join WHERE 1 $where and cert.iso_prod_type = 1 ORDER BY cert.id DESC $pages[limit]";

$query = $db->query( $sql );

while( $rt = $db->fetch_array( $query ) ){
	$rt['status'] = f_certstate($rt['status']);
	$rt['ctfrom'] = f_ctfrom( $rt['ctfrom'] );

	// 暂停时间
	$rt['time1'] = $db->get_var( "SELECT cgs_date FROM sp_certificate_change WHERE zsid = '{$rt['id']}' AND cg_type = '97_01' AND status=1 and deleted=0 ORDER BY id DESC " );
	// 暂停到期
	$rt['time2'] = $db->get_var( "SELECT cge_date FROM sp_certificate_change WHERE zsid = '{$rt['id']}' AND cg_type = '97_01' AND status=1 and deleted=0 ORDER BY id DESC " );
	// 撤销时间
	$rt['time3'] = $db->get_var( "SELECT cgs_date FROM sp_certificate_change WHERE zsid = '{$rt['id']}' AND cg_type = '97_03' AND status=1 and deleted=0 ORDER BY id DESC " );
	// 恢复时间
	$rt['time4'] = $db->get_var( "SELECT cgs_date FROM sp_certificate_change WHERE zsid = '{$rt['id']}' AND cg_type = '97_02' AND status=1 and deleted=0 ORDER BY id DESC " );
	$datas[] = chk_arr($rt);
}
if( !$export ){
	tpl();
}else{

	ob_start();
	tpl( 'xls/list_certificate' );
	$data = ob_get_contents();
	ob_end_clean();

	export_xls( '产品证书查询', $data );
}




