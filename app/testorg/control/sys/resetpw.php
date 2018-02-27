<?php
!defined('IN_SUPU') && exit('Forbidden');

$id = current_user('id');
$e_info = $db->find_one("settings_test_org",array("id"=>$id),"name,username");
tpl('sys/resetpw');