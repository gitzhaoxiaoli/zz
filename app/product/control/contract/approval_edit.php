<?php
!defined('IN_SUPU') && exit('Forbidden');
$step = getgp('step');
$eid = getgp('eid');
$cti_id = getgp('cti_id');
if($step){
	if($_POST[is_samp]){
		$test_org_id = getgp("test_org_id");
		$test_org_name = getgp("test_org_name");
	}
	unset($_POST['step'],$_POST['ct_id'],$_POST['test_org_id'],$_POST['test_org_name']);
	$_POST['accept_user'] = current_user('uid');
	$_POST['accept_date'] = date("Y-m-d");
	$af = $db->find_one("contract_item",array('cti_id' => $cti_id));
	$db->update("contract_item",$_POST,array('cti_id'=>$cti_id));
	$db->update("contract",array('status'=>3),array('ct_id'=>$ct_id));
	$cti = $db->find_one("contract_item",array('cti_id' => $cti_id));
	extract($cti);
	if($app_type == '0'){
		$audit_type = $db->getField("enterprises","audit_type",array("eid"=>$ep_prod_id));
		!$audit_type && $audit_type ='1001';
	}elseif($app_type == '1'){
		$audit_type ='1011';
	}elseif($app_type == '2'){
		$audit_type ='1010';
	}
	$p_array=array(
		'eid' => $eid,
		'ep_manu_id' => $ep_manu_id,
		'ep_prod_id' => $ep_prod_id,
		'ct_id' => $ct_id,
		'cti_id' => $cti_id,
		'cti_code' => $cti_code,
		'iso' => $iso,
		'audit_ver' => $audit_ver,
		'prod_ver' => $prod_ver,
		'audit_code' => $audit_code,
		'use_code' => $use_code,
		'audit_type' => $audit_type,
		'ctfrom' => $ctfrom,
		'total' => $total,
		'iso_prod_type' => 1,
		'ct_code'=>$db->getField("contract","ct_code",array('ct_id'=>$ct_id)),
		'prod_id' => $prod_id,
		'fac_code' => $fac_code,
		'scope'=>$scope,
		'scope_e'=>$scope_e,
		'test_org_id'=>$test_org_id,
		'test_org_name'=>$test_org_name,
		'is_samp'=>$_POST['is_samp'],
		'is_check'=>$_POST['is_check'],
	);
	if($p_array['is_check'])
		$p_array['status'] = 0;
	else{
		$p_array['status'] = 3;
		$p_array['redata_status'] = 1;
	}
	$pid = $db->insert("project",$p_array);
	$db->update("progress",array(	"pid" => $pid,
									"step2"	=> date("Y-m-d H:i:s"),
									"status"	=> "2",
							),array("cti_id"=>$cti_id));
	log_add($af[eid],"","产品受理通过 认证申请编号:" . $cti['cti_code'],serialize($af), serialize($cti ));
	//处理 企业表工厂编码
	$fac_code = $db->get_var("SELECT fac_code FROM `sp_enterprises` WHERE `eid` = '$ep_prod_id'");
	if(!$fac_code){
		if($_POST[iso] == 'B01'){
			$_type = "Q";
			$sort = $db->get_var("SELECT MAX(fac_sort) FROM `sp_enterprises` WHERE `deleted` = '0' ");
			$sort += 1;
			$sort = sprintf("%06d",$sort);
			$fac_code = $_type.$sort;
			$db->update("enterprises",array("fac_code"=>$fac_code,"fac_sort"=>$sort),array("eid"=>$ep_prod_id));
		}else{
			$_type = "P";
			$sort_p = $db->get_var("SELECT MAX(fac_sort_p) FROM `sp_enterprises` WHERE `deleted` = '0' ");
			$sort_p += 1;
			$sort_p = sprintf("%06d",$sort_p);
			$fac_code = $_type.$sort_p;
			$db->update("enterprises",array("fac_code_p"=>$fac_code,"fac_sort_p"=>$sort_p),array("eid"=>$ep_prod_id));
		}
	}
	showmsg('success', 'success', "?m=product&c=contract&a=approval&status=1");
}else{
 //合同附件上传类型
  $allow_types = array( '1001', '1002');
   $contract = array();
	$contract['ep_name'] = $db->getField("enterprises", "ep_name", "eid = '$eid' and deleted = 0 ");
	$ct_archives = array();
	$sql = "SELECT * FROM `sp_attachments` WHERE `cti_id` = '$cti_id' AND `iso_prod_type` = '1' AND `deleted` = 0  and ftype IN ('" . implode("','", $allow_types) . "') ORDER BY id ASC";
	$query = $db->query($sql);
	while ($rt = $db->fetch_array($query)) {
		$rt['ftype_V'] = f_arctype($rt['ftype']);
		$ct_archives[$rt['id']] = $rt;
	}
	$cti=$db->find_one("contract_item",array("cti_id"=> $cti_id));
	$approval_date=$cti['approval_date'];
	$approval_note=$cti['approval_note'];
	$approval_user_select = str_replace("value=\"$cti[approval_user]\"","value=\"$cti[approval_user]\" selected",$approval_user_select);
	$status=$cti['status'];
	$pid = $db->get_var("SELECT id FROM `sp_project` WHERE `cti_id` = '$cti_id' AND `audit_type` = '1001' AND `iso_prod_type` = '1' AND `deleted` = '0'");
	
	tpl('contract/approval_edit');
}