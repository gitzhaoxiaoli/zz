<?php
!defined('IN_SUPU') && exit('Forbidden');
//选择产品
//读取配置内容 
$set=load('set');

$where='';
//检测机构名称
if($_GET['test_org_name']){
	$where.=" AND name like '%$_GET[test_org_name]%'"; 
}
//检测机构编码
if($_GET['code']){
	$where.=" AND code like '%$_GET[code]%'"; 
} 
//根据产品名称搜索检测机构
if($_GET['prod_name']){
	//转成产品编码
	$prod_ids=$db->get_Col(" select code from sp_settings_prod where name like '%$_GET[prod_name]%'");
	if($prod_ids){
		$test_codes=$db->getCol('settings_prod_test','test_id',array('prod_id'=>$prod_ids));
	 	$where.=' AND '.$db->sqls(array('code'=>$test_codes));
	}
}
 

if($_GET['prod_id']){
/*
//根据产品过滤检测机构,暂时未启用，以后可能会用到 2014-03-13
$testIds=$db->find_results('settings_prod_test',array('prod_id'=>$_GET['prod_id']),'test_id'); 
 foreach(array_values($testIds) as $v){
	$testId[]=$v['test_id']; 
}
$testIn=@implode(',',array_values( $testId));
$where=" AND code in ('$testIn')";*/

//根据产品计算认证领域 
$prod_type=load('set')->get_set_name_by_id('prod',$_GET['prod_id'],'prod_type');
if($prod_type)
$where =" AND is_{$prod_type}=1";
} 
$set->tbl_name='test_org';

$total=$set->count_set($where);
$pages = numfpage( $total);
 
//查询检测机构列表
$list_datas=$set->get_set_datas('settings_test_org',$where,'*',"",$pages[limit]);
/* if($list_datas)foreach($list_datas as $k=>$v){
	//获取产品编码
	 $sql=" select prod_id,test_id from sp_settings_prod_test where test_id='$v[code]' ";
	 $q=$db->query($sql);
	 while($rt=$db->fetch_array($q)){  
		$list_datas[$rt['test_id']]['prod_name'].=$set->get_set_name_by_id('prod',$rt['prod_id']).'<br>';
 	}  
} 
 */
 tpl();
 