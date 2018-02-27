<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*任务审批（批量）
*/

$approval_date = getgp('approval_date');
    $tids = explode(',', getgp('tids'));
    $tids = array_unique($tids);
    if (!$tids) print_json(array(
        'state' => 'no',
        'msg' => '请选择要审批的项目！'
    ));
    if (!$approval_date) print_json(array(
        'state' => 'no',
        'msg' => '请填写审批日期！'
    ));
    $audit = load('audit');
    //审核项目 状态变更
    $pids = array();
    $query = $db->query("SELECT id FROM sp_project WHERE tid IN (" . implode(',', $tids) . ")");
    while ($rt = $db->fetch_array($query)) {
        $pids[] = $rt['id'];
        $audit->edit($rt['id'], array(
            'status' => 3
        ));
    }
    //审核任务 状态更更
	foreach($tids as $tid){
    $db->update('task', array(
			'status' => 3,//已审批
			'approval_uid' => current_user('uid'),
			'approval_user' => current_user('name'),
            'approval_date' => $approval_date,
            // 'approval_note' => $approval_note,
    ) , array(
        'id' => $tid
    ));
	$eid=$db->get_var("SELECT eid FROM `sp_task` WHERE `id` = '$tid' ");
	$sms_arr=array("eid"=>$eid,
						"temp_id"=>$tid,
						"is_sms"=>'0',
						"flag"=>4);
	$sms=load("sms");
	$sms_info=$sms->get(array("temp_id"=>$sms_arr[temp_id],"flag"=>4));
	if($sms_info[id])
		$sms->edit($sms_info[id],$sms_arr);
	else
		$sms->add($sms_arr);
	}
    print_json(array(
        'state' => 'ok',
        'msg' => 'success'
    ));