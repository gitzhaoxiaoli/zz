<?php
//见证
return array(
	'0' => array( //第一个为默认值
		'code' => '0',
		'name' => '', //三方认证的时候使用
 		'type' => 'witness', //暂时未使用
		'is_stop' => '0', //下拉是否启用
	), 
	'1' => array(
		'code' => '0',
		'name' => '注册见证', //三方认证的时候使用
 		'type' => 'witness', //暂时未使用
		'is_stop' => '0', //下拉是否启用
	),
 	'2' => array(
		'code' => '1',
		'name' => '内部见证',
		'type' => 'witness',
		'is_stop' => '0',
	), 
	'3' => array(
		'code' => '2',
		'name' => '专业见证',
		'type' => 'witness',
		'is_stop' => '0',
	),
	/* '4' => array(
		'code' => '3',
		'name' => '见证',
		'type' => 'witness',
		'is_stop' => '0',
	), */
	'5' => array(
		'code' => '4',
		'name' => '注册见证+内部见证',
		'type' => 'witness',
		'is_stop' => '0',
	),
	
	'6' => array(
		'code' => '5',
		'name' => '内部见证+专业见证',
		'type' => 'witness',
		'is_stop' => '0',
	),
	'7' => array(
		'code' => '6',
		'name' => '注册见证+专业见证',
		'type' => 'witness',
		'is_stop' => '0',
	),
	'8' => array(
		'code' => '7',
		'name' => '注册见证+内部见证+专业见证',
		'type' => 'witness',
		'is_stop' => '0',
	),
);?>