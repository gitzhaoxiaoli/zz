<?php
!defined('IN_SUPU') && exit('Forbidden');  
if($_POST){
	//添加配置内容
	if($_POST['new']['test_id'] and $_POST['new']['prod_id']){
		$prods=explode('；',$_POST['new']['prod_id']);
 		foreach($prods as $prod_id){
			$db->insert('settings_prod_test',array('test_id'=>$_POST['new']['test_id'],'prod_id'=>$prod_id));	
 		}
  	}  
	//更新配置内容
	unset($_POST['new']); 
 	if($_POST['old']){ 
		foreach($_POST['old'] as $k=>$v){ 
			$db->update('settings_prod_test',$v,array('id'=>$k));  
		} 
	}   
	showmsg( 'success', 'success', "?m=com&c=setting&a=$_GET[a]" ); 
}
//加载模型
$set=load('set');
$set->tbl_name='prod_test prod_test'; //初始化操作表
//删除配置
if($_GET['del']){ 
	$set->del_set($_GET['del']); 
	showmsg( 'success', 'success', "?m=com&c=setting&a=$_GET[a]" ); 
}

//读取配置内容  
$where='';
if($_GET['prod_id']){
	$where.=" AND prod_id like '%$_GET[prod_id]%'"; 
}
if($_GET['test_name']){
	$where.=" AND test_org.name like '%$_GET[test_name]%'"; 
}
if($_GET['code']){
	$where.=" AND prod_test.code like '%$_GET[code]%'"; 
}
if($_GET['msg']){
	$where.=" AND prod_test.msg like '%$_GET[msg]%'"; 
}  
$where.=" AND prod_test.deleted='0'";

$fields="prod_test.*,test_org.name as test_name";


$joins="LEFT JOIN sp_settings_prod_rule rule ON rule.code=prod_test.prod_id";
$joins.=" LEFT JOIN sp_settings_test_org test_org ON prod_test.test_id=test_org.code"; 

$total=$set->count_set("$where",$joins);
$pages = numfpage( $total);
//读取配置内容  
$sql=" SELECT $fields FROM sp_settings_prod_test prod_test $joins  WHERE 1 $where order by prod_test.vieworder desc,id desc $pages[limit]";
$resdb=$db->get_results($sql,"id");  

tpl();