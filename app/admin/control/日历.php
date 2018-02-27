<?php
!defined('IN_SUPU') && exit('Forbidden');

$current_month = date("Y-m");

$out_dayzjs = array('日','一','二','三','四','五','六');

$months = date("t");
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
tpl('u');
?>