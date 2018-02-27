<?php
//证书状态
return array(
	'n' => array(
		'code' => 'n',
		'name' => '未登记',
		'type' => 'is_check', //暂时未使用-业务类型有关-应用
		'is_stop' => '0', //下拉是否启用-目的：兼容旧版本
	),
 	'y' => array(
		'code' => '1',
		'name' => '已登记',
		'type' => 'is_check',
		'is_stop' => '0',
	),
	'e' => array(
		'code' => '2',
		'name' => '未登记完',
		'type' => 'is_check',
		'is_stop' => '0',
	),
);?>