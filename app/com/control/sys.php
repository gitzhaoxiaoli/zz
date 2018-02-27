<?php
!defined('IN_SUPU') && exit('Forbidden');
require_once( CACHE_PATH.'job_type.cache.php' );		//人员性质 多项
require_once( CACHE_PATH.'ct_type.cache.php' );		//人员 批次类型

$user = load('hr');
$step = getgp('step');
$ctfrom_select=f_select('ctfrom');//项目所属单位(登记用 搜索用)
$province_select =f_select('region');//省份下拉(登记用 搜索用)
$department_select =f_select('department');//部门
$political_select =f_select('political');//政治面貌
$card_type_select =f_select('card_type');//证件类型
$choose_type_select =f_select('choose_type');//选用类型
$insurance_select =f_select('insurance');//社保登记
$audit_job_select =f_select('audit_job');//是否专职

//人员性质
$job_type_checkbox ='';
if( $job_type_array ){
	foreach( $job_type_array as $code => $item ){
		$job_type_checkbox .= "<input type='checkbox' name='job_type[$code]' value=\"$item[code]\">".$item[name].'&nbsp;';
	}
}

//批次类型
$ct_type_checkbox ='';
if( $ct_type_array ){
	foreach( $ct_type_array as $code => $item ){
		$ct_type_checkbox .= "<input type='checkbox' name='meta[ct_type][$code]' value=\"$item[code]\">".$item[name].'&nbsp;&nbsp;';
	}
}
//计划任务二级调度【分发】
$loop_type_array = array(
	'month'	=> '每月',
	'week'	=> '每周',
	'day'	=> '每天',
	'hour'	=> '每小时',
	'now'	=> '每隔'
); 
 

$week_day_array = array('天', '一', '二', '三', '四', '五', '六' ); 
$out_format = array(
	'month_day'	=> "%d日%d时%02d分",
	'week_day'	=> "周%s%d时%02d分",
	'day'		=> "%d点%02d分",
	'hour'		=> "%d分",
	'now'		=> "%d%s"
); 
$now_type_array = array(
	'day'	=> '天',
	'hour'	=> '小时',
	'minute'=> '分钟',
);
unset( $code, $item );

//引入模块控制下的方法
$action=CTL_DIR.$m.'/'.$a.'.php';
if(file_exists($action)){
	include_once($action);
}else{
	echo '该方法不存在，请检查对应程序';
	echo '<br />方法名称：'.$a;
} 
?>