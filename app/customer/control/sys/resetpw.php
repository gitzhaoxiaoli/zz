<?php
!defined('IN_SUPU') && exit('Forbidden');

$eid = current_user('eid');
$e_info = $db->find_one("enterprises",array("eid"=>$eid),"ep_name,username");
tpl('sys/resetpw');