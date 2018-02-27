<?php
!defined('IN_SUPU') && exit('Forbidden');
//选择产品
//读取配置内容 
$iso = getgp("iso");
$list_datas=$db->get_results("SELECT * FROM `sp_settings_audit_code` WHERE `iso` = '$iso' AND `is_stop` = '0' AND `deleted` = '0'");  
 //p($list_datas);
 //echo $where;
tpl();
 