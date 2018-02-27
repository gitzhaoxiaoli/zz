<?php
!defined('IN_SUPU') && exit('Forbidden');
//弹窗产品标准列表查询
$set=load('set');
$set->tbl_name='prod_ver';
 $where='';
/*if($_GET['name']){
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
} */
if($_GET['prod_code']){ 
	$where.=" AND prod_id='$_GET[prod_code]'";	
	
}
 
$total=$db->get_var(" select COUNT(*) from sp_settings_prod_ver pord_ver where 1 $where");
$pages = numfpage( $total);


//读取配置内容 
$list_datas=$db->get_results(" select * from sp_settings_prod_ver prod_ver WHERE 1 AND prod_ver.deleted='0' $where ORDER BY prod_ver.vieworder desc, prod_ver.id DESC $pages[limit] ");
 
tpl();
 