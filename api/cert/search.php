<?php
// 获取证书信息
ini_set("display_errors", "Off");
$ep_name = trim($_POST[ep_name]);
$certno = trim($_POST[certno]);
if (!$ep_name OR !$certno) {
	header("Content-type:text/html;charset=utf-8");
	exit("ERROR!!<a href=\"search.html\">点击返回</a>");
}
$where .= " AND cert_name = '$ep_name' AND certno = '$certno'";

$db_config = require "./../../data/db_config.php";

//创建对象并打开连接，最后一个参数是选择的数据库名称
$mysqli = new mysqli($db_config[db_host],$db_config[db_user],$db_config[db_pwd],$db_config[db_name]);
//检查连接是否成功
if (mysqli_connect_errno()){
	//注意mysqli_connect_error()新特性
	die('Unable to connect!'). mysqli_connect_error();
}
$mysqli->set_charset("utf8");
$sql = "select * from sp_certificate c LEFT JOIN sp_enterprises e ON e.eid = c.eid where 1 $where";
// $sql = "select * from sp_certificate where id = 0";
//执行sql语句，完全面向对象的
require "./../../data/cache/certstate.cache.php";
$result = $mysqli->query($sql);
$cert_data = array();
while($row = $result->fetch_array()){
	$row['status'] = $certstate_array[$row['status']][name];
	$cert_data[] = $row;
}

// var_dump($cert_data);
require 'results.html';
?>