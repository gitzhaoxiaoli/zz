<?php
//审核项目状态 
return array(
	'0' => array(
		'code' => '0',
		'name' => '未安排',
		'type' => 'project_status',
		'is_stop' => '0',
	),
	'1' => array(
		'code' => '1',
		'name' => '待派人', //已安排
		'type' => 'project_status',
		'is_stop' => '0',
	),
	'2' => array(
		'code' => '2',
		'name' => '待审批',
		'type' => 'project_status',
		'is_stop' => '0',
	),
	'3' => array(
		'code' => '3',
		'name' => '已审批',
		'type' => 'project_status',
		'is_stop' => '0',
	),
	'5' => array(
		'code' => '5',
		'name' => '维护',
		'type' => 'project_status',
		'is_stop' => '0',
	),
	'6' => array(
		'code' => '6',
		'name' => '退回',
		'type' => 'project_status',
		'is_stop' => '0',
	),
);?>