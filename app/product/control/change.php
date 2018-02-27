<?php
!defined('IN_SUPU') && exit('Forbidden');
     
require_once( ROOT . '/data/cache/b01001_certchange.cache.php' );//变更类别
require_once( ROOT . '/data/cache/b01001_certLogout.cache.php' );//注销原因
require_once( ROOT . '/data/cache/b01001_certpasue.cache.php' );//暂停原因
require_once( ROOT . '/data/cache/b01001_certrecall.cache.php' );//撤销原因
require_once( ROOT . '/data/cache/b01001_certchange_report.cache.php' );//变更原因
require_once( ROOT . '/data/cache/mark.cache.php' );

$certificate = load('certificate'); //加载证书模型
$enterprise = load('enterprise'); //加载企业模型
$contract = load('contract');
$change = load('change');

$zsid = getgp('zsid');
$cgid = getgp('cgid');
$step = getgp('step');

$province_select = f_province_select();//省分下拉 (搜索用)


//引入模块控制下的方法
$action = CP_CTL_DIR . $c . '/' . $a . '.php';
if (file_exists($action)) {
    include_once ($action);
    exit;
}
 