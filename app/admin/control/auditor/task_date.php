<?php 
!defined('IN_SUPU') && exit('Forbidden');
/*
	说明:由审核员来计划开始结束时间
	@zys 2016-8-3
*/
$eid = getgp('eid');
$tid = getgp('tid');
$ct_id = getgp('ct_id');

$tb_date = getgp('tb_date').' '.getgp('tb_time'); // 计划起始日期
$te_date = getgp('te_date').' '.getgp('te_time');
$wsqs_date = getgp('wsqs_date'); // 文审时间


$new_task = array(
    'tb_date' => $tb_date, // 计划起始日期
	'te_date' => $te_date,
	'wsqs_date' => $wsqs_date // 文审时间
);
$db->update("task",$new_task, array("id" => $tid));
$db->update("task_audit_team",array("taskBeginDate"=>$tb_date,"taskEndDate"=>$te_date), array("tid" => $tid));
showmsg('success', 'success', "?c=auditor&a=task_edit&tid=$tid");


 ?>