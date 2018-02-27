<?php
!defined('IN_SUPU') && exit('Forbidden');
if($s_date=getgp("s_date"))
	$where .=" AND `taskBeginDate` >= '$s_date'";
if($e_date=getgp("e_date"))
	$where .=" AND `taskEndDate` <= '$e_date' ";
$uid=current_user("uid");
if($uid!='1')
	$uids[]=$uid;
else
	$uids=$db->get_col("SELECT id FROM `sp_hr` WHERE `job_type` = '1004-1' AND `deleted` = '0' AND `is_hire` = '1'");
$data=array();
foreach($uids as $_uid){
	$query=$db->query("SELECT * FROM `sp_task_audit_team` WHERE `deleted` = '0' and iso_prod_type = 0 and uid='$_uid' $where");
	$leader=$auditor=$zj=0;
	while($rt=$db->fetch_array($query)){
	$num=mkdate($rt[taskBeginDate],$rt[taskEndDate]);
		if($rt[role]=='1001')
			$leader+=$num;
		elseif($rt[role]=='1002')
			$auditor+=$num;
		elseif($rt[role]=='1003')
			$zj+=$num;

		$name=$rt[name];
	}
	$data[]=$name."&nbsp;&nbsp;&nbsp;组长：".$leader."天，组员：".$auditor."天，专家：".$zj."天";
}
tpl();

?>