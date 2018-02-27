<?php
!defined('IN_SUPU') && exit('Forbidden');
//企业登记区划
//
$set=load('set');
$set->tbl_name='region';
 $where='';
if($name = $_GET['name']){
	  $where.=" AND (full_name like '%".trim($name)."%' or frist_letter like '%".$name."%')";		
}
if($_GET['code']){
	$where.=" AND code like '%$_GET[code]%'"; 
}

//不能直接选择省、市,但是包括 台湾、香港、澳门
//暂时取消，部分地区需要选00  2016-07-01
//$where .= " AND (!(code like '%00')  or code>='710000') ";	

$total=$set->count_set($where);
$pages = numfpage( $total,12);
//
$list_datas=array();
$query=$db->query("SELECT * from sp_settings_region WHERE 1 $where AND deleted='0' $pages[limit]");
while($rt=$db->fetch_array($query)){
	$list_datas[] = $rt;
}
 // p($list_datas);
 // echo $where;
tpl();
 