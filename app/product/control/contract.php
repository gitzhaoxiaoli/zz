<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *合同管理 控制文件
 *
 *
 */
require_once( ROOT . '/data/cache/nature.cache.php' );
 
require_once( ROOT . '/data/cache/statecode.cache.php' );
require_once( ROOT . '/data/cache/industry.cache.php' );
require_once( ROOT . '/data/cache/currency.cache.php' );
require_once (ROOT .'/data/sys_cache/region.cache.php'); //省份
require_once( ROOT . '/data/cache/iso.cache.php' );
require_once( ROOT . '/data/cache/audit_type.cache.php' );
require_once( ROOT . '/data/cache/mark.cache.php' );
require_once( ROOT . '/data/cache/audit_ver.cache.php' );
require_once( ROOT . '/data/cache/risk_level.cache.php' ); 
 
$step	= getgp('step'); 
$et		= load( 'enterprise' );
$ct		= load( 'contract' );
$cti	= load( 'contract.item' ); 
 
$ctfrom_select = f_ctfrom_select();//合同来源
$province_select = f_province_select();//省分下拉 (搜索用)

//审核类型
$audit_type_select = '';
if( $audit_type_array ){
	foreach( $audit_type_array as $code => $item ){
		if( in_array( $code, array( '1001', '1004-1','1004-2' ,'1007') ) )
		$audit_type_select .= "<option value=\"$code\">$item[name]</option>";
	}
}

$iso_select=f_select('iso');//体系

unset( $code, $item );
$hr=$db->get_results("SELECT name,id FROM `sp_hr` WHERE `job_type` like '%1001%'");
$approval_user_select = '';
foreach($hr as $val){
	$approval_user_select.="<option value=\"$val[id]\">$val[name]</option>"; 
}
//引入模块控制下的方法
$action = CP_CTL_DIR . $c . '/' . $a . '.php';
if (file_exists($action)) {
    include_once ($action);
    exit;
} 
 
?>