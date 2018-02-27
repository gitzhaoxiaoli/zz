<?php
!defined('IN_SUPU') && exit('Forbidden');
//删除经销商信息
$db->update('ot_basedata',array('deleted'=>'1'),array('id'=>$_GET['dealerId']));
//添加日志
$af=$db->get_row("select * from sp_ot_basedata where id=$_GET[dealerId]");
log_add($_GET['dealerId'],'','',serialize($af));
//跳转列表页面
$REQUEST_URI='?c=in_auditor&a=dealer_list';
showmsg( 'success', 'success', $REQUEST_URI );