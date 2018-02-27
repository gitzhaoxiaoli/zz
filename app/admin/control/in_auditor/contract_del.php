<?php
!defined('IN_SUPU') && exit('Forbidden');
//删除经销商信息
$db->update('ot_contract',array('deleted'=>'1'),array('id'=>$_GET['contractId']));
//添加日志
$af=$db->get_row("select * from sp_ot_contract where id=$_GET[contractId]");


log_add($_GET['contractId'],'','',serialize($af),'');
//跳转列表页面
$REQUEST_URI='?c=in_auditor&a=contract_list';
 showmsg( 'success', 'success', $REQUEST_URI );