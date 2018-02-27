<?php
ini_set("display_errors", "Off");
require '../mmysql.class.php';
$conf = require '../../data/db_config.php';
require '../../data/cache/iso.cache.php';
require '../../data/cache/qualification.cache.php';
// print_r($iso_array);
// exit;
$model = new MMysql($conf);
$openid = $_GET['openid'];
$hr = $model->field('id')->where("openid = '$openid' AND deleted = 0 AND is_hire = 1")->select('sp_hr');
$uid = $hr[0]['id'];
$date = date("Y-m-d");
$where = "uid = $uid AND  `status` = '1' AND `deleted` = '0'";
$qua = $model->field(array('iso','qua_type','qua_no','s_date','e_date'))->where($where)->order(array('iso'=>'ASC'))->select('sp_hr_qualification');
$data = array();
if ($qua) {
	foreach ($qua as $v) {
		$data[] = array(
			'iso' => $iso_array[$v['iso']]['name'],
			'qua_type' => $qualification_array[$v['qua_type']]['name'],
			'qua_no' => $v['qua_no'],
			's_date' => $v['s_date'],
			'e_date' => $v['e_date'],
			);
		
	}
}else{
	$data = array('status' => 0 , 'msg' => '您未注册资格！');
}

echo json_encode($data);
// echo "<pre>";
// print_r($data);
// echo "</pre>";
// require 'my.htm';



function format_date($date)
{
    $str = explode(' ', $date);
    $arr = explode('-', $str[0]);
    $res = $arr[0] . "年" . $arr[1] . "月" . $arr[2] . "日";
    if ($str[1])
        if (strtotime($str[1]) < strtotime("13:00:00"))
            $res .= " 上午";
        else
            $res .= " 下午";
    return $res;
}
?>