<?php
!defined('IN_SUPU') && exit('Forbidden');
//选择单个产品标准 
$set=load('set');
$set->tbl_name='ver';
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
/*if($_GET['name']){
	$where.=" AND scope like '%$_GET[scope]%'"; 
} */
if($_GET['prod_code'] and $_GET['prod_code']!=''){ 
	$where.=" AND prod_id='$_GET[prod_code]'";	 
}
if($_GET['type'] and $_GET['type']!=''){ 
	$where.=" AND type='$_GET[type]'";	 
}
$where.=" AND is_stop!=1";

$total=$set->count_set($where);
 
$pages = numfpage( $total);
//读取配置内容
$list_datas=$db->find_results('settings_ver',$where,'*'," vieworder desc,",$pages[limit]);   
 
 
tpl();
 