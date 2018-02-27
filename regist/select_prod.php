<?php
require_once('../framework/models/db/db_mysql.class.php');
require_once('../framework/function.php');
require_once('../framework/page.fun.php');
$t=require_once('../data/db_config.php');
$db = new db_mysql;
$db->connect($t['db_host'], $t['db_user'] ,$t['db_pwd'], $t['db_name']);
//选择产品
//读取配置内容 
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
 

// $total=$db->get_var("SELECT COUNT(*) FROM sp_settings_prod_xiaolei WHERE 1 AND deleted='0' $where");

// $pages = numfpage( $total,10);
//读取配置内容
$list_datas=$db->get_results("select * from sp_settings_prod_xiaolei WHERE 1 $where $pages[limit]");  
require "select_prod.htm";
 