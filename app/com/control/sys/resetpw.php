<?php
!defined('IN_SUPU') && exit('Forbidden');


$uid = nowUsr('uid');
	$hr_info = $user->get($uid);
	tpl();