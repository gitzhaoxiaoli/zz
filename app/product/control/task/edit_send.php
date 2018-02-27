<?php
!defined('IN_SUPU') && exit('Forbidden');
//////////////////派人////////////////
//
$tid        = getgp("tid");
$ep_prod_id = getgp("ep_prod_id");
$task_info  = $db->get_row(" SELECT * FROM sp_task WHERE id='$_GET[tid]' AND iso_prod_type = 1"); //取任务信息
////////////删除派人信息////////////
if ($_GET['type'] == 'del') {
    //删除派人明细表
    $db->update('task_audit_team', array(
        'deleted' => 1
    ), array(
        'id' => $_GET['id']
    ));
    showmsg("success", "success", "?m=product&c=task&a=edit_send&tid=$tid");
}

if ($_POST) { //保存新增派人信息 
    if ($uid = $_POST['uid'] and $_POST['role']) {
        /*
        去掉 @zxl 2016-5-23 10:35:43 
        if ($_POST['role'] == '1001') {
            // 组长要判断他的级别  如是AB才可以当组长
            $m_separate = $db->getField("hr", "m_separate", array(
                "id" => $uid
            ));
            if (!in_array($m_separate, array(
                "A",
                "B"
            ))) {
                //showmsg("error","组长要求级别为A或B","?m=product&c=task&a=edit_send&tid=$tid");
            }
            
        }*/
        //派人明细表 
        $new_item = array(
            'eid' => $task_info['eid'],
            'tid' => $tid,
            'uid' => $uid,
            // 'pid' => $pid,
            'name' => $_POST[name],
            'ctfrom' => $task_info[ctfrom],
            'taskBeginDate' => $task_info['tb_date'],
            'taskEndDate' => $task_info['te_date'],
            'role' => $_POST['role'],
            'witness' => $_POST['witness'],
            'witness_person' => $_POST['witness_person'],
            'qua_type' => $_POST['qua_type'],
            'audit_code' => $_POST['audit_code'],
            'use_code' => $_POST['use_code'],
            'iso_prod_type' => 1,
            // 'iso' => 'B01'
        );
        if ($id = $_GET['id'])
            $db->update("task_audit_team", $new_item, array(
                "id" => $id
            ));
        else
            $db->insert('task_audit_team', $new_item);
    }
    //1待派人 2已派人
    if ($_POST['status']) {
        $db->update('task', array(
            'status' => '3',
            'task_status' => '3',
            'approval_uid' => current_user("uid"),
            'approval_user' => current_user("name"),
            'approval_date' => date("Y-m-d")
        ), array(
            'id' => $tid
        ));
        $db->update("project", array(
            'status' => '3'
        ), array(
            "tid" => $tid
        ));
        $pids = $db->getCol("project", "id", array(
            "tid" => $tid,
            "iso_prod_type" => '1'
        ));
        $db->update("progress", array(
            "step6" => date("Y-m-d H:i:s"),
            "status" => "6"
        ), array(
            "pid" => $pids
        ));
    }
    $task_info['status'] = $_POST['status'];
    if ($task_info['status'] == 3)
        log_add($task_info['eid'], '', '已派人，项目号：' . $log_cti_codes);
    showmsg("success", "success", "?m=product&c=task&a=edit_send&tid=$tid");
}

////////编辑派人信息///////////////
if ($id = $_GET['id']) { //显示需要编辑的信息
    $auditor_info = $db->find_one('task_audit_team', array(
        'id' => $id
    ));
    
    
}


$action       = $_GET['id'] ? '修改' : '新增';
//任务已派人列表
$auditor_list = $db->get_results(" SELECT * FROM sp_task_audit_team  WHERE tid='$tid' AND deleted=0 and iso_prod_type = 1 order by uid");

$ct_projects = array();
$query       = $db->query("SELECT * FROM sp_project WHERE   deleted=0 and iso_prod_type = 1 AND tid = '$tid' ORDER BY id DESC");
while ($rt = $db->fetch_array($query)) {
    $rt['ep_name']      = $db->getField('enterprises', 'ep_name', array(
        'eid' => $rt['eid']
    ));
    $rt['ep_manu_name'] = $db->getField('enterprises', 'ep_name', array(
        'eid' => $rt['ep_manu_id']
    ));
    $cert               = $db->get_row("SELECT certno,s_date,e_date FROM `sp_certificate` WHERE `cti_id` = '$rt[cti_id]' AND `deleted` = '0' ORDER BY `s_date` DESC");
    if ($cert)
        $rt = array_merge($rt, $cert);
    $ct_projects[$rt['id']] = $rt;
}


tpl();
?>
