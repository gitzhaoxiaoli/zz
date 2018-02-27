<?php
//查询单个企业信息挂件： 弹窗
!defined('IN_SUPU') && exit('Forbidden');
$epid = $args['epid']; //父窗口
if (!$eid) { //子窗口
    $epid = $_GET['epid'];
}
 $projectLists = load('audit')->gets(array(
        'cti.ep_prod_id' => $epid             
    ));

tpl();//输出模板