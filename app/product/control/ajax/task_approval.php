<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 * 任务审批
 */
 $tid = (int)getgp('tid');
    $approval_date = getgp('approval_date');
    $approval_note = getgp('approval_note');
    if (!$approval_date) print_json(array(
        'state' => 'no',
        'msg' => '请填写审批日期！'
    ));
    $task = load('task');
    $row = $task->get(array(
        'id' => $tid
    ));
    $audit = load('audit');
    if ($row) {
        $pids = array();
        $query = $db->query("SELECT id FROM sp_project WHERE tid = '$tid' and iso_prod_type = 1 and deleted = 0");
        while ($rt = $db->fetch_array($query)) {
            $pids[] = $rt['id'];
            $audit->edit($rt['id'], array(
	            'status' => 3,//已审批
                'redata_status' => '0',
				'redata_uid'=>'',
				'redata_date'=>'',
				'to_jwh_date'=>'',
				'redata_note'=>'',
				'comment_a_uid'=>'',
				'comment_a_name'=>'',
				'comment_b_uid'=>'',
				'comment_b_name'=>'',
				'pd_type'=>0,
				'assess_date'=>'',
				'sp_date'=>'',
				
            ));
        }
        $task->edit($tid, array(
            'status' => 3,//已审批
			'jh_sp_status'=>0,
			'upload_plan_date'=>'',
			'upload_file_date'=>'',
			'jh_sp_date'=>'',
			'jh_sp_name'=>'',
			'jh_sp_status'=>0,
			'rect_finish'=>0,
			'approval_uid' => current_user('uid'),
			'approval_user' => current_user('name'),
            'approval_date' => $approval_date,
            'approval_note' => $approval_note,
        ));
		
		//任务审批通过 通知审核组
	$mails=$auditors=array();
	$_query=$db->query("SELECT name,uid FROM sp_task_audit_team  WHERE tid='$tid' AND deleted=0 and role<>'' order by role");
	while($_rt=$db->fetch_array($_query)){
		$mail=$db->meta($_rt[uid],"mail","","user");
		$mail && $mails[]=$mail;
		$auditors[]=$_rt[name];
		
	}
	if($mails){
		$ep_name=$db->get_var("SELECT ep_name FROM `sp_enterprises` WHERE `eid` = '$row[eid]'");
		$row[tb_date]=substr($row[tb_date],0,10);
		$row[te_date]=substr($row[te_date],0,10);
		$email_to=$mails;
		$email_title="新任务：".$ep_name;
		// $email_title = "=?UTF-8?B?".base64_encode($email_title)."?=";
		$email_cotent="您好，您有新的审核任务请注意查收<br/>";
		$email_cotent.=$ep_name;
		$email_cotent.="<br/>";
		$email_cotent.="审核时间：";
		$email_cotent.=$row[tb_date]."至".$row[te_date];
		$email_cotent.="<br/>";
		$email_cotent.="审核组成员：".join("；",$auditors);
		$email_cotent.="<br/>";
		$email_cotent.="计划审批人：".current_user("name");
		$email_cotent.="<br/>";
		$email_cotent.="<br/>";
		$email_cotent.="请及时到erp 中查看 ";
		$email_from=current_user("mail");
		// $headers　= "MIME-Version: 1.0<br/>";  
		// $headers .= 'Content-type:text/html; charset=utf-8' . "<br/>";  
		// $headers .= "Content-Transfer-Encoding: 8bit<br/>";
		// $headers .= $email_from;
		mailTo($email_to,$email_title,$email_cotent);
	}
	
	
	/* 
	
	// 此处是添加报告邮寄 
		$sms_arr=array("eid"=>$row['eid'],
						"temp_id"=>$tid,
						"is_sms"=>'0',
						"flag"=>4);
		$sms=load("sms");
		$sms_info=$sms->get(array("temp_id"=>$sms_arr[temp_id],"flag"=>4));
		if($sms_info[id])
			$sms->edit($sms_info[id],$sms_arr);
		else
			$sms->add($sms_arr); */
    }
	log_add($row['eid'],0,'任务审批，任务ID：'.$tid);
    print_json(array(
        'state' => 'ok',
        'msg' => 'success'
    ));