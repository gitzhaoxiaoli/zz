<?php
!defined('IN_SUPU') && exit('Forbidden');

$uid		= current_user('uid');
$wstatus	= $db->getField("hr","wstatus",array('id'=>$uid));


tpl('sys/wechat');