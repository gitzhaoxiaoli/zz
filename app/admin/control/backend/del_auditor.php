<?php
!defined('IN_SUPU') && exit('Forbidden');

$tid = getgp('tid');
$uid = getgp('uid');
if ($tid && $uid) {
	$db->delete('task_audit_team' , ['uid' => $uid , 'tid' => $tid]);
}

showmsg("success", "success", "?c=backend&a=edit_task&tid=$tid");

