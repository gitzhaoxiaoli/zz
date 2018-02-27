<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 * 评定模块认证决定
 */
require CONF.'/cache/iso.cache.php'; //子证书
require CONF.'/cache/fl_question.cache.php'; //问题标题
$pd_type_arr=array("","通过","待定","未通过");
$fl_select = f_select('fl_question');//问题标题
$pd_id = ( int ) getgp ( 'pd_id' );
$step = getgp ( 'step' );
$id = getgp ( 'id' );
$tid = getgp ( 'tid' );
$ct_id = ( int ) getgp ( 'ct_id' );
$type = getgp ( 'type' );

if ($id and $type == 'del') {

	$db->update("assess_notes",array("deleted"=>1),array("id"=>$id));
	showmsg ( 'success', 'success', "?m=product&c=assess&a=edit&pd_id=$pd_id&ct_id=$ct_id#tab-question" );
} 



if ($step) {
	$scopes = array_map ( 'trim', getgp ( 'scope' ) );
	$assess_dates = array_map ( 'trim', getgp ( 'assess_date' ) );
	$sp_dates = array_map ( 'trim', getgp ( 'sp_date' ) ); // 总经理审批时间
	$pd_types = array_map ( 'intval', getgp ( 'pd_type' ) ); // 通过 待定 不通过
	
	$notes = array_map ( 'trim', getgp ( 'note' ) );
	
	$if_cert = array_map ( 'trim', getgp ( 'ifchangecert' ) ); // 是否发证，是否在证书登记中显示
	if ($pd_types) { 
		$audit = load ( 'audit' );
		$cert = load ( 'certificate' );
		foreach ( $pd_types as $pd_id => $audit_code ) {
			
			$new_pd = array (
					'comment_date' => $assess_dates[$pd_id],
					'assess_date' => $assess_dates[$pd_id],
					'sp_date' => $sp_dates [$pd_id],
					'pd_type' => $pd_types [$pd_id],//评定通过
					'ifchangecert' => $if_cert [$pd_id],
					'keep_decide'  => $decides[$pd_id],
					'comment_note' => $notes [$pd_id],
			);
			$scopes && $new_pd[scope]=$scopes [$pd_id];
			$audit->edit ( $pd_id, $new_pd );
			
			$p_info = $db->find_one("project",array("id"=>$pd_id));
			// 如果是监督 则一起
			$t_info = $db->find_one("task",array("id"=>$p_info[tid]));
			if(strpos($t_info[audit_type],"1004" !== false)){
				$db->update("project",$new_project,array("tid"=>$p_info[tid]));
			}
			$db->update("progress",array(	
								"step11"	=> date("Y-m-d H:i:s"),
								"status"	=> "11",
							),array("pid"=>$pd_id));
			
			/************提醒组长评定状态*********************/
			/* 
			$auditor=$db->get_row("SELECT name,uid FROM sp_task_audit_team  WHERE tid='$tid' AND deleted=0 and role='1001'");
			$mail=$db->meta($auditor[uid],"mail","","user");
			$mail && $mails[]=$mail;
			$auditors=$auditor[name];
			if($mails){
				$p_info=$db->find_one("project",array("id"=>$pd_id));
				$ep_name=$db->get_var("SELECT ep_name FROM `sp_enterprises` WHERE `eid` = '$p_info[eid]'");
				$email_to=$mails;
				$email_title="评定结果：".$ep_name;
				// $email_title = "=?UTF-8?B?".base64_encode($email_title)."?=";
				$email_cotent="您好，您的审核任务评定结果如下<br/>";
				$email_cotent.=$ep_name;
				$email_cotent.="<br/>";
				$email_cotent.="体系类别：".f_iso($p_info[iso]);
				$email_cotent.="<br/>";
				$email_cotent.="决定时间：";
				$email_cotent.=$row[assess_date];
				$email_cotent.="<br/>";
				$email_cotent.="决定状态：".$pd_type_arr[$p_info[pd_type]];
				$email_cotent.="<br/>";
				$email_cotent.="决定人员：".current_user("name");
				$email_cotent.="<br/>";
				$email_cotent.="<br/>";
				$email_cotent.="请及时到erp 中查看 ";
				// $email_from=current_user("mail");
				// $headers　= "MIME-Version: 1.0<br/>";  
				// $headers .= 'Content-type:text/html; charset=utf-8' . "<br/>";  
				// $headers .= "Content-Transfer-Encoding: 8bit<br/>";
				// $headers .= $email_from;
				mailTo($email_to,$email_title,$email_cotent);
			} */
		}
		showmsg ( 'success', 'success', "?m=product&c=assess&a=approval_edit&pd_id=$pd_id#tab-edit" );
	 
	}
} else { // 显示信息
	$_uids=$db->get_col("SELECT uid FROM `sp_task_audit_team` WHERE `tid` = '$tid' AND `deleted` = '0'");
	$_uids=array_merge($_uids,array(-1));
	$_query=$db->query("SELECT id,name FROM `sp_hr` WHERE `job_type` like '%1006%' AND `deleted` = '0' AND `is_hire` = '1' and id not in (".join(",",$_uids).")");
	$_select="";
	while ( $_rt = $db->fetch_array ( $_query ) ) {
		$_select.="<option value=\"$_rt[id]\">$_rt[name]</option>";
	}

	// 认证决定 
	$pds = array (); 
	$join .= " LEFT JOIN sp_task t ON t.id = p.tid"; 
	$join .= " LEFT JOIN sp_task_audit_team a ON a.tid = p.tid"; 
	$where = " AND p.id = '$pd_id'";
	$where .= " AND p.deleted = 0 "; 
	$sql = "SELECT p.*,t.tb_date ,t.te_date,t.task_code FROM sp_project p $join WHERE 1 $where"; 
	$query = $db->query ( $sql );
	$zy_select = array ();
	$iso_arr=array();


	while ( $rt = $db->fetch_array ( $query ) ) {
		$rt[comment_a_select]=str_replace ( "value=\"" . $rt ['comment_a_uid'] . "\"", "value=\"" . $rt ['comment_a_uid'] . "\" selected", $_select );
		$rt ['audit_type_V'] = f_audit_type ( $rt ['audit_type'] );
		if ($rt[ifchangecert] or $rt ['audit_type'] == '1001') {
			$checks [$rt ['id']] ['y'] = 'checked';
			$checks [$rt ['id']] ['n'] = '';
		}else{
			$checks [$rt ['id']] ['y'] = '';
			$checks [$rt ['id']] ['n'] = 'checked';
		}
				
		$rt[prod_id] = $db->getField('settings_prod_xiaolei','name',array('code'=>$rt['prod_id']));
		$rt[prod_name] = $db->getField('contract_item','prod_name_chinese',array('cti_id'=>$rt['cti_id']));
		$rt['pd_type_'.$rt[pd_type]] = 'selected';
		$rt = chk_arr($rt);
		$pds[$rt ['id']] = $rt;
		$rt[tb_date] && $tb_date = mysql2date("Y年m月d日",$rt[tb_date]);
		$rt[te_date] && $te_date = mysql2date("Y年m月d日",$rt[te_date]);
		$p_tid = $rt['tid'];
	}

	/* 说明:添加评定问题 */

	// 认证决定 
	while ( $rt = $db->fetch_array ( $query ) ) {
		$use_codes=explode("；",$rt['use_code']);
		$use_codes=array_unique($use_codes);
		$rt['use_code']=JOIN("；",$use_codes);
		$rt['comment_b_select'] = str_replace ( "value=\"" . $rt ['comment_b_uid'] . "\"", "value=\"" . $rt ['comment_b_uid'] . "\" selected", $zy_select );
		// $rt ['audit_type_V'] = f_audit_type ( $rt ['audit_type'] );
		$rt ['audit_ver_V'] = f_audit_ver ( $rt ['audit_ver'] );
		!$rt ['mark'] && $rt ['mark']='01';
		$checkbox=str_replace("name=\"\"","name=\"marks[$rt[id]]\"",$mark_checkbox);
		$rt ['mark_checkbox'] = str_replace ( "value=\"$rt[mark]\"", "value=\"$rt[mark]\" checked", $checkbox);
		// 默认时间为当前日期
		if (! $rt ['assess_date'] || $rt ['assess_date'] == '0000-00-00') {
			$rt ['assess_date'] = date ( 'Y-m-d' );
		}
		if (! $rt ['sp_date'] || $rt ['sp_date'] == '0000-00-00') {
			$rt ['sp_date'] = date ( 'Y-m-d' );
		}
		// 审核类型是一阶段 二阶段 再认证 标记是否发证 为是
		if (in_array ( $rt ['audit_type'], array (
				'1001',
				'1002',
				'1003',
				'1007' 
		) )) {
			$rt ['if_cert'] = 1;
		}

		if ($rt ['if_cert']) {
			$checks [$rt ['id']] ['y'] = 'checked';
			$checks [$rt ['id']] ['n'] = '';
		} else {
			$checks [$rt ['id']] ['y'] = '';
			$checks [$rt ['id']] ['n'] = 'checked';
		}
		//是否保持通知书
		if($rt['keep_decide'] == 0){
			$decide[$rt['id']] ['y'] = '';
			$decide[$rt['id']] ['n'] = 'checked';
		}else{
			$decide[$rt['id']] ['y'] = 'checked';
			$decide[$rt['id']] ['n'] = '';
		}
		
		
		$rt ['pd_type_1'] = $rt ['pd_type_4'] = $rt ['pd_type_3'] = $rt ['pd_type_5'] = '';
		$rt ['pd_type_' . $rt ['pd_type']] = ' selected';
		
		$eid = $rt ['eid'];
		$e_ct_id = $rt ['ct_id'];
 
		$rt ['comment_a_pass_1'] = $rt ['comment_a_pass_2'] = $rt ['comment_b_pass_1'] = $rt ['comment_b_pass_2'] = '';
		$rt ['comment_a_pass_' . $rt ['comment_pass']] = $rt ['comment_b_pass_' . $rt ['comment_b_pass']] = ' checked';
		
		$rt [cert_scope] = $db->get_var ( "select cert_scope from sp_certificate where cti_id='{$rt[cti_id]}' and deleted=0" );
		$pds [$rt ['id']] = $rt;
		$iso_arr[$rt[cti_id]]=f_iso($rt[iso]);
	}
	//审核组成员,用于下拉选择
	$shzcy_arr = $db->find_results('task_audit_team'," AND tid = $p_tid","name,uid");
		$shzcy_select = "";
	if($shzcy_arr){
	    foreach($shzcy_arr as $val)
		{
			$shzcy_select .= "<option value=".$val['uid'].">".$val['name']."</option>"; 
		}
	}
	//评定问题明细
	$result=$db->get_results("select * from sp_assess_notes WHERE 1 AND tid='$tid' AND deleted='0' ORDER BY id");
	$que_result = array();
	foreach($result as $val)
	{
		//$val['shzcy_name'] = $db->getField("hr",'name',array('id'=>$val['shzcy_uid']));
	    $que_result[$val['id']] = $val;
	}
	//子公司，子证书范围
	$child_query=$db->query("SELECT ep_name,eid FROM `sp_enterprises` WHERE `parent_id` = '$eid' AND `deleted` = '0' ORDER BY `eid` ");
	$scope_childs=array();
	while($r=$db->fetch_array($child_query)){
		$r[num]=$db->get_results("SELECT * FROM `sp_contract_num` WHERE `eid` = '$r[eid]' and type='1'",'cti_id');
		$r[num] && $scope_childs[]=$r;
	}
	// $scope_childs=$db->get_results("SELECT * FROM `sp_contract_num` WHERE `eid` in (".join(",",$child_ids).") AND `type` = '1'");
	$task_info=$db->get_row("SELECT * FROM `sp_task` WHERE `id` = '$tid'");
	$task_info[tb_date]=trim($task_info[tb_date],":00:00");
	$task_info[te_date]=trim($task_info[te_date],":00:00");

	/* 评定问题结束 */

	tpl ();
}
// 评定结束

?>