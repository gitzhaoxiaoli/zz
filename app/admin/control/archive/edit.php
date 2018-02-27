<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *资料回收
 */
$eid = (int) getgp('eid');
$tid = (int) getgp('tid');
if ($_POST) {
    
    $new_project = array(
                    'redata_date' => date("Y-m-d"),
                    'to_jwh_date' => date("Y-m-d"),
                    'redata_uid' => current_user('uid'),
                    'redata_note' => $_POST['redata_note'],
                    'redata_status' => $_POST['redata_status'],
                );
    $db->update('project' , $new_project , array("tid" => $tid));
    
    if ($new_project[redata_status]) {
        log_add($eid, 0, "资料回收", NULL, NULL);
        }    
    $url = getgp("url");
    showmsg('success', 'success', $url);
} else {
    $url           = $_SERVER['HTTP_REFERER'];
    $task_projects = array();
    $sql           = "SELECT * FROM sp_project WHERE 1 AND tid = '$tid'";
    $query         = $db->query($sql);
    while ($rt = $db->fetch_array($query)) {
        $rt['audit_type'] = f_audit_type($rt['audit_type']);
        $rt['audit_ver']  = read_cache( 'audit_ver',$rt['audit_ver']);
        $task_projects[] = $rt;
        $redata_note = $rt[redata_note];
        $redata_status = $rt[redata_status];
    }
    //审核文档
    $sql = "select * from sp_attachments where tid='$tid' ORDER BY `sort`";
    $res = $db->query($sql);
    while ($rt = $db->fetch_array($res)) {
        $rt['uid']              = f_username($rt['create_uid']);
        $enterprises_archives[] = $rt;
    }
    $task_info = $db->get_row("SELECT jh_sp_date,jh_sp_note,tb_date,te_date FROM `sp_task` WHERE `id` = '$tid'");
    extract($task_info);
    $tb_date = trim($tb_date, ":00:00");
    $te_date = trim($te_date, ":00:00");
    tpl();
}