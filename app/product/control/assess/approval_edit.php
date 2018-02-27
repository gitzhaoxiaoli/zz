<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 * 评定模块认证决定
 */
$pd_type_arr=array("","通过","","未通过");
$pd_id = ( int ) getgp ( 'pd_id' );
$step = getgp ( 'step' );
$id = getgp ( 'id' );
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
		showmsg ( 'success', 'success', "?m=product&c=assess&a=edit&pd_id=$pd_id#tab-edit" );
	 
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
	$where = " AND p.id = '$pd_id'";
	$where .= " AND p.deleted = 0 "; 
	$sql = "SELECT p.*,t.tb_date ,t.te_date FROM sp_project p $join WHERE 1 $where"; 
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
		$pds[$rt ['id']] = $rt;
		$tb_date > '2010-01-01' && $tb_date = mysql2date("Y年m月d日",$rt[tb_date]);
		$te_date > '2010-01-01' && $te_date = mysql2date("Y年m月d日",$rt[te_date]);
	}
	
 
	tpl ();
}
// 评定结束

?>