<?php
!defined('IN_SUPU') && exit('Forbidden');

//同委托人，同生产企业
if ($_GET['eid']) {
    $where['eid'] = $_GET['eid'];
}
//检测企业用户注册
if (getgp('ep_name')) {
    $where['ep_name'] = getgp('ep_name');
}
$where['deleted']='0';
$epInfo = load('enterprise')->get($where,false);
//判断是否获取数据
if ($epInfo) {
    //    print_json(array('state'=>'ok'));
    print_json($epInfo);
} else {
    print_json(array(
        'state' => 'no'
    ));
}
 