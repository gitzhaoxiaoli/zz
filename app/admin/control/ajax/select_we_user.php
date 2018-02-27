<?php
/**
 * 选择人员 绑定微信的
 */

$users = $db->get_results("SELECT * FROM `sp_hr` WHERE `wstatus` = '1' AND `deleted` = '0' AND `is_hire` = '1' AND `id` <> '1'");
tpl();
// p($users);