<?php
echo 11;
!defined('IN_SUPU') && exit('Forbidden');
//选择产品
//读取配置内容 
$set=load('set');
$set->tbl_name='prod_rule';
 $where='';
if($_GET['name']){
	$where.=" AND name like '%$_GET[name]%'"; 
}
if($_GET['code']){
	$where.=" AND code like '%$_GET[code]%'"; 
}
if($_GET['msg']){
	$where.=" AND msg like '%$_GET[msg]%'"; 
}
if($_GET['name']){
	$where.=" AND scope like '%$_GET[scope]%'"; 
} 
$where.=" AND type='$_GET[type]'";
$total=$set->count_set($where);
$pages = numfpage( $total);
//读取配置内容
$list_datas=$db->find_results('settings_prod_rule',$where,'*'," vieworder desc,",$pages[limit]);  
p($list_datas);
tpl();
 