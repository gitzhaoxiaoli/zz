<?php
//查询单个企业信息挂件： 弹窗
!defined('IN_SUPU') && exit('Forbidden');
$eid = $args['eid']; //父窗口
if (!$eid) { //子窗口
    $eid = $_GET['eid'];
}
//企业基本信息+附表信息
$epInfo   = load('ep')->get(array(
    'eid' => $eid
));
//企业文档信息
$ep_files = load('attachment')->gets(array(
    'type' => 'ep',
    'key_val' => $eid
));
tpl();//输出模板