<?php
// 获取证书信息
ini_set("display_errors", "Off");
if (!$_POST) {
	echo json_encode(['status' => 0 , 'msg' => '提交方式错误']);
	exit;
}
$ep_name = trim($_POST[name]);
$certno = trim($_POST[certno]);
if (!$ep_name || !$certno) {
	echo json_encode(['status' => 0 , 'msg' => '参数错误']);
	exit;
}

$where = ['deleted' => 0 , 'cert_name' => $ep_name , 'certno' => $certno];
$field = 'cert_name , certno , s_date , e_date , status , cert_scope , cert_scope_e,iso,audit_ver';
$db_config = require "./../../data/db_config.php";
require '../MMysql.class.php';

$db = new MMysql($db_config);

$cert_data = $db->field($field)->where($where)->order('id DESC')->select('sp_certificate');
$status_arr = ['01' => '有效' , '02' => '暂停' , '03' => '撤销' , '04' => '注销' , '05' => '过期失效'];
if ($cert = $cert_data[0]) {
	require './../../data/cache/audit_ver.cache.php';
	$cert['status'] = $status_arr[$cert['status']];
	$cert['iso'] = $audit_ver_array[$cert['audit_ver']]['note'].'管理体系';
	$cert['audit_ver'] = $audit_ver_array[$cert['audit_ver']]['audit_basis'];
	echo json_encode(['status' => 1 , 'msg' => 'success' , 'data' => $cert]);
} else {
	echo json_encode(['status' => 0 , 'msg' => '没有找到记录']);
}
exit;
?>