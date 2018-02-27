<?php
!defined('IN_SUPU') && exit('Forbidden');
//选择产品
//读取配置内容 
$set=load('set');
$set->tbl_name='prod_xiaolei';
$where='';
if($_GET['name']){
	$where.=" AND name like '%$_GET[name]%'"; 
}
if($_GET['code']){
	$where.=" AND code like '%$_GET[code]%'"; 
}
if($_GET['fac_code']){
	$where.=" AND msg like '%$_GET[fac_code]%'"; 
}
 //echo $where;exit;
 if($_GET['type']){
     
     $where.="AND prod_type='$_GET[type]'";
     
 }
 
$where .= " AND deleted = 0";
$total=$set->count_set($where);

$pages = numfpage( $total,10);
//读取配置内容
$list_datas=$db->get_results("select * from sp_settings_prod_xiaolei WHERE 1 $where $pages[limit]");  
 
tpl();
 