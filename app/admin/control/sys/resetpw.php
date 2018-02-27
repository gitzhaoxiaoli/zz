<?php
!defined('IN_SUPU') && exit('Forbidden');

$uid = current_user('uid');
$hr_info = $user->get($uid);

tpl('sys/resetpw');