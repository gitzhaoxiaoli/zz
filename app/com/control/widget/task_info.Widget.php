<?php
!defined('IN_SUPU') && exit('Forbidden');
//查看任务书信息：查看任务信息
$tid=$args['tid'];
if(!$tid){
	$tid=$_GET['tid'];	
}

//查看任务基本信息
$task_info=load('task')->get(array('id'=>$tid));

//查看任务派人列表信息
$teamLists=$db->find_results('task_auditor',array('tid'=>$_GET['tid'])); 
 
//查看检查组长安排的检查计划 
$task_plan=load('task_plan')->get(array('task_id'=>$tid));
 
//任务检查结论附件信息 t任务 、ep企业、cti合同项目、product_photo受理上传图片
$task_files=load('attachment')->gets(array('type'=>'t','key_val'=>$tid,'audit_ver'=>$task_info['task_type']));//下面三个是后来加的

$task_files2=load('attachment')->gets(array('type'=>'ep','key_val'=>$tid,'audit_ver'=>$task_info['task_type']));

$task_files3=load('attachment')->gets(array('type'=>'cti','key_val'=>$tid,'audit_ver'=>$task_info['task_type']));

$task_files4=load('attachment')->gets(array('type'=>'product_photo','key_val'=>$tid,'audit_ver'=>$task_info['task_type']));
//弹窗使用
if($_GET['type']=='iframe'){
  tpl();//输出模板
}
 


