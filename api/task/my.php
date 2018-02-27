<?php
ini_set("display_errors", "Off");
require '../mmysql.class.php';
$conf = require '../../data/db_config.php';
$model = new MMysql($conf);
$uid = 22;
$month = $_GET['month'];
!$month && $month = date("Y-m");
// $month = '2015-03';
$role_array = array('1001' => '组长','1002'=>'组员');
$where = "uid = $uid AND LEFT(taskBeginDate , 7 ) = '$month' AND deleted = 0";
// var_dump($where);
// exit;
$tids = $model->field(array('tid'))->where($where)->select('sp_task_audit_team');
$data = array();
foreach ($tids as $v) {
	$tid = $v['tid'];
	if (isset($data[$tid])) {
		continue;
	}
	// 任务
	$task_info = $model->doSql("SELECT t.tb_date , t.te_date , e.ep_name FROM sp_task t LEFT JOIN sp_enterprises e ON e.eid = t.eid WHERE t.id = $tid");
	$task_info[0]['tb_date'] = format_date($task_info[0]['tb_date']);
	$task_info[0]['te_date'] = format_date($task_info[0]['te_date']);
	$team = $model->doSql("SELECT `name` , role  FROM sp_task_audit_team WHERE tid = $tid AND deleted = 0 ORDER BY role");
	$auditor = array();
	foreach ($team as $_v) {
		$auditor[] = $_v['name'];
	}
	$data[$tid] = array(
		'task' => $task_info[0],
		'team' => join(";" , array_unique($auditor))
		);
}
// echo "<pre>";
// print_r($data);
// echo "</pre>";
require 'my.htm';



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