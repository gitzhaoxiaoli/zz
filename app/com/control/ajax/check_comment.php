<?php
!defined('IN_SUPU') && exit('Forbidden');
//项目工厂检验评价 
if ($_GET['test_private']) {
    $test_private = $_GET['test_private'];
}
$pid = $_GET['pid'];
//查询检验项目信息
//if ($pid) {
    $project_info = load('audit')->get(array(
        'id' => $pid
    ));
 
    $cti_info     = load('cti')->get(array(
        'cti_id' => $project_info['cti_id']
    ));
    $ep_manu_info = load('ep')->get(array(
        'eid' => $cti_info['ep_manu_id']
    )); //生产者
    $ep_pro_info  = load('ep')->get(array(
        'eid' => $cti_info['ep_prod_id']
    )); //生产企业
    $ep_eq_info   = load('ep')->get(array(
        'eid' => $cti_info['eid']
    )); //委托人 
    $pro_info     = $db->find_one('settings_prod', array(
        'code' => $cti_info['prod_id']
    )); //认证
    $org_info     = $db->find_one('settings_test_org', array(
        'code' => $project_info['test_org_id']
    )); //检测
//}
if ($_POST) { //检验评价   
    load('audit')->edit($pid, $_POST['project']);
    if ($_REQUEST['baogao'] == '13') { //计算报告退回时间
        load('audit')->edit($pid, array(
            'report_back_date' => nowTime('mysql')
        ));
    }
	//?app=cqm&m=test&a=prod_list&audit_ver=b01001
     showmsg('success', 'success', '?app=cqm&m=test&a=prod_list&audit_ver='.$cti_info['audit_ver']);
}
;
tpl();
 