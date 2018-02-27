<?php
/*
** 
**
*/
require "../framework/function.php";
$data = $_GET[data];
$res_data = array();
if($data){
	$key = file_get_contents("../data/orgcode.log");	//获取动态密钥
	$s = ecryptdString($data,$key);
	$res_data[data] = $s;
	$res_data[status] = "ok";
}else{
	$res_data[status] = "error";
}
print_json($res_data);