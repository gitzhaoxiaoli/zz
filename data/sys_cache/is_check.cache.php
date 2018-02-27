<?php
//是否登记
return array(
	'n' => array(
		'code' => 'n',
		'name' => '未登记', //三方认证的时候使用
		
		'type' => 'data_for', //暂时未使用
		'is_stop' => '0', //下拉是否启用
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