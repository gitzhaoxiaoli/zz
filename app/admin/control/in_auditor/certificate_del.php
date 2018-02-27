<?php
!defined('IN_SUPU') && exit('Forbidden');
$db->update('ot_traincert',array('deleted'=>'1'),array('id'=>$_GET['certificateId']));

//跳转列表页面
$REQUEST_URI='?c=in_auditor&a=certificate_list';
 showmsg( 'success', 'success', $REQUEST_URI );