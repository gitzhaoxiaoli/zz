<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
* 通过查询看每个人的关注状态更新人员表
*/

// 将数据库 更新为 0
$db->query("UPDATE sp_hr SET wstatus = 0");
require_once ("framework/fun.function.php");
$groupid = get_option("groupid");
// 查出组内所有的人员 更新状态为1 已申请
$list = getUserList($groupid);
// p($list);
foreach ($list as $key => $value) {
    $db->update("hr",array("wstatus"=>1),array("tel"=>$value['userid']));
}
// 查出组内所有已关注的人员 更新状态为2 已关注
$userList = getUserList($groupid, 1);
foreach ($userList as $key => $value) {
    $db->update("hr",array("wstatus"=>2),array("tel"=>$value['userid']));
}
showmsg("success","success","?c=wchat&a=wlist");
