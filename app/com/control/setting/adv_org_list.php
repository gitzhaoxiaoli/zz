<?php
!defined('IN_SUPU') && exit('Forbidden');  
if($_POST){
	//添加配置内容
	if($_POST['new']['name']){
		$db->insert('settings_adv_org',$_POST['new']);	
 	}
	//更新配置内容
	unset($_POST['new']); 
 	if($_POST['old']){ 
		foreach($_POST['old'] as $k=>$v){ 
			$db->update('settings_adv_org',$v,array('id'=>$k)); 
		} 
	}  
	update_adv_org();
	showmsg( 'success', 'success', "?m=com&c=setting&a=$_GET[a]" ); 
}
//加载模型
$set=load('set');
$set->tbl_name='adv_org'; //初始化操作表
//删除配置
if($_GET['del']){ 
	$set->del_set($_GET['del']);
	update_adv_org();
	showmsg( 'success', 'success', "?m=com&c=setting&a=$_GET[a]" ); 
}
$where='';
if($_GET['name']){
	$where.=" AND name like '%$_GET[name]%'"; 
}
if($_GET['code']){
	$where.=" AND code like '%$_GET[code]%'"; 
}
if($_GET['person']){
	$where.=" AND person like '%$_GET[person]%'"; 
}
if($_GET['msg']){
	$where.=" AND msg like '%$_GET[msg]%'"; 
} 
if($_GET['addr']){
	$where.=" AND addr like '%$_GET[addr]%'"; 
} 
$total =$set->count_set($where);

$pages = numfpage( $total);
//读取配置内容
$resdb=$db->find_results('settings_adv_org',$where,'*'," vieworder desc,",$pages[limit]);  
 
tpl();