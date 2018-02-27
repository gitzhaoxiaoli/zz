<?php
!defined('IN_SUPU') && exit('Forbidden');
 

$id=$_GET['id'];

$res=$db->find_one('change_app',array(
       'id'=>$id
));

if($_POST){
	unset($_POST[id]);
	$_POST[sp_user] = current_user("name");
	$_POST[sp_date] = date("Y-m-d");
	$db->update("change_app",$_POST,array('id'=>$id));
	if($_POST[status] == '1'){
		$zs_info = $db->find_one("certificate",array("id"=>$res[zsid]));
		$cg_type = join("|",unserialize($res[type]));
		$cg_pid = $db->get_var("SELECT id FROM `sp_project` WHERE `cti_id` = '$zs_info[cti_id]' AND `iso_prod_type` = '1' AND `deleted` = '0' order by id desc");
		$default   = array(
			'zsid' => $res[zsid], //证书id
			'cg_pid' => $cg_pid,	//变更关联项目id
			'iso' => $zs_info['iso'], //
			'audit_type' => $cg_pinfo['audit_type'], //
			'ctfrom' => $zs_info['ctfrom'], //合同来源
			'cg_type' => $cg_type, //变更类型
			'cg_reason' => "0299", //
			'cg_af' => $res[cg_af], //变更前
			'cg_bf' => $res[cg_bf], //变更后
			'cg_date' => date("Y-m-d"), //变更日期
			'status' => '1', //状态
			'note' => getgp('sp_note')
		);
		$db->insert("certificate_change",$default);
	}
	showmsg("success","success","?m=product&c=change&a=app_list");
}else{







	tpl();

}



?>