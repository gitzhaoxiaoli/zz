<?php
!defined('IN_SUPU') && exit('Forbidden');
//修改送样信息
if ($_GET['pid']) {
    //过去简单项目查询，没有链表，或者关联其他模型
    $proj_info                  = load('audit')->get(array(
        'id' => $_GET['pid']
    ));
    //实验室名称
    $proj_info['test_org_name'] = load('set')->get_set_name_by_id('test_org', $proj_info['test_org_id']);
    //合同项目信息
    $cti_info                   = load('cti')->get(array(
        'cti_id' => $proj_info['cti_id']
    ));
    if ($cti_info['audit_ver'] == 'b02001') { //有机产品
        //获取基地产品信息
        $ogas      = $db->getCol('oga', 'oga_id', array(
            'cti_id' => $cti_info['cti_id']
        ));
        $oga_infos = load('oga_info')->get_oga_infos(array(
            'oga_id' => $ogas
        ));
    }
}
//功能：安排实验室
if ($_POST) {
    $_POST['is_notice'] = $_POST['is_notice'];
    if ($_POST['oga_info']) { //有机+gap
        foreach ($_POST['oga_info'] as $oga_info_id => $oga_info) {
            $db->update('oga_info', $oga_info, array(
                'oga_info_id' => $oga_info_id
            ));
        }
    }	
	
    //检验状态
    $_POST['samp_status'] = '4'; //未检验
    unset($_POST['oga_info']);
    load('audit')->edit($_GET['pid'], $_POST);
    //showmsg('success', 'success', '?app=cqm&m=test&a=prod_edit&pid=' . $_GET['pid']);
	 showmsg('success', 'success', "?app=cqm&m=test&a=prod_cti_list&type=$cti_info[audit_ver]");
	
}
;
tpl();
 