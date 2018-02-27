<?php
//审核任务状态0：待派人 1：待审批 2：已审批 
return array(
	'1' => array(
		'code' => '1',
		'name' => '待派人',
		'type' => 'task_status',
		'is_stop' => '0',
	),
	'2' => array(
		'code' => '2',
		'name' => '待审批', //已安排
		'type' => 'task_status',
		'is_stop' => '0',
	),
	'3' => array(
		'code' => '3',
		'name' => '已审批',
		'type' => 'task_status',
		'is_stop' => '0',
	),
	 
);?>