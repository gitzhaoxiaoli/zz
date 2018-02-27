<?php
!defined('IN_SUPU') && exit('Forbidden');
//根据组织代码传递企业信息 
if ($_GET['work_code']) {
 	 $where['work_code'] =str_replace('-','',$_GET['work_code']);
	
	//$where['work_code'] =$_GET['work_code'];
}
//同委托人，同生产企业
if ($_GET['eid']) {
    $where['eid'] = $_GET['eid'];
}
//检测企业用户注册
if (getgp('ep_name')) {
    $where['ep_name'] = getgp('ep_name');
}
$where['deleted']='0';
$epInfo = load('ep')->get($where,false);
//print_json(array('state'=>load('ep')->sql));
//判断是否获取数据
if ($epInfo) {
    //    print_json(array('state'=>'ok'));
    print_json($epInfo);
} else {
    print_json(array(
        'state' => 'no'
    ));
}
 