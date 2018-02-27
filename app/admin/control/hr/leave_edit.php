<?php
!defined('IN_SUPU') && exit('Forbidden');
if($_GET['uid']) //添加编辑信息
$hrInfo=$db->get_row( " SELECT * FROM sp_hr WHERE id=$_GET[uid]");

if($_GET['auditorId']){
	//获取需要编辑的信息
	$sql=" SELECT auditor.*,hr.name FROM sp_task_audit_team auditor LEFT JOIN sp_hr hr ON hr.id=auditor.uid WHERE auditor.id=$_GET[auditorId] ";
	$auditorInfo=$db->get_row($sql);
	$hrInfo['name']=$auditorInfo['name']; 
	if($_POST){ 	
	$db->update('task_audit_team',$_POST,array('id'=>"$_GET[auditorId]"));
	$REQUEST_URI='?c=hr&a=leave_list';
	showmsg( 'success', 'success', $REQUEST_URI );  
 	exit;	
	} 
} 
if($_POST){
	$_POST['uid']=$hrInfo['id'];
	$_POST['data_for']='6';
	$db->insert('task_audit_team',$_POST);
	$REQUEST_URI='?c=hr&a=leave_list';
	showmsg( 'success', 'success', $REQUEST_URI ); 
}
 
tpl( );
 