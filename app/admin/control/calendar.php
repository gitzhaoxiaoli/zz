<?php
!defined('IN_SUPU') && exit('Forbidden');
if ($_POST) {
	$uid = current_user('uid');
	$new_date_arr = array(
		'uid' => $uid,
		'audit_date' => join("|" , $_POST['day']),
		'year_month' => $_POST['year'],
		);
	if ($id = getgp('id')) {
		$db->update('auditor_date' , $new_date_arr , array("id" => $id));
	}else{
		$db->insert('auditor_date' , $new_date_arr);
		
	}	
	$month_num = mysql2date("t",$_POST['year'] . "-01");
	$month_array = array();
	for ($i=1; $i <= $month_num; $i++) { 
		$month_array[] = $i;
	}
	$s_date_array = array_diff($month_array, $_POST['day']);
	$s = $e = '';
	foreach ($s_date_array as $v) {
		if (!$s) {
			$s = $e = $v;
		}else{
			$t = $e + 1;
			if ($t == $v ) {
				$e = $t;
			}else{
				$date['s'][] = $s;
				$date['e'][] = $e;
				$s = $e = $v ;
			}
		}
	}
	$date['s'][] = $s;
	$date['e'][] = $e;
	$t_date = array();
	$uid = current_user('uid');
	$name = current_user('name');
	foreach ($date['s'] as $key => $value) {
		$new_array = array(
			'uid' => $uid,
			'data_for' => 7,
			'name' => $name,
			'taskBeginDate' => $_POST['year'] . "-" . $value . ' 08:00',
			'taskEndDate' => $_POST['year'] . "-" . $date['e'][$key] . ' 17:00',
			'note' => '兼职审核员'
			);
		p($new_array);
	}
	// p($s_date_array);
	// p($date);
	exit(p($_POST));
}
$year = getgp('year');
$month = getgp('month');
!$year && $year = date("Y");
!$month && $month = date("m");
$current_month = $year . "-" . $month;

$out_dayzjs = array('日','一','二','三','四','五','六');

$months = date("t" , strtotime($current_month));
$month_array = array();
$month_zjf = mysql2date("w",$current_month . "-01");
for ($i=0; $i < $month_zjf; $i++) { 
	$_data[0][] = '';
}
$_k = 0;
for ($the_day=1; $the_day <= $months; $the_day++) { 
	$_data[$_k][] = $the_day;
	if (count($_data[$_k]) % 7 == 0) {
		$_k ++ ;
	}
	// $month_zjs[mysql2date("w",$current_month . "-" . $the_day)][] = $the_day;
}
// ksort($month_zjs);
// p($_data);
// exit;
// foreach ($_data as $key => $value) {
// 	foreach ($value as $_key => $_value) {
// 		echo $_value;
// 	}
// 	echo "<br/>";
// }
// exit;
// str 数组是选中的那天
/*$str = array(10,11,3,15,16,17,18);
// res 数组 就是没空的时间
$res = array_diff( $month_array , $str);
$data = array();
$temp = '';
$i = 0 ; 
foreach ($res as $key => $value) {
	if ($temp + 1 == $value) {
		$temp = $data[$i][] = $value;
	}else{
		$i++;
		$temp = $data[$i][] = $value;
	}
}
$date_array = array();
foreach ($data as $key => $value) {
	$date_array[s_date][] = $current_month . '-' . $value[0];
	$date_array[e_date][] = $current_month . '-' . end($value);

}
p($date_array);*/
tpl('calendar');
?>