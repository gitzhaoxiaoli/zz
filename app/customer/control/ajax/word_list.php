<?php
!defined('IN_SUPU') && exit('Forbidden');
$cti_id = getgp("cti_id");
$p_info = $db->get_row("SELECT id pid,tid FROM `sp_project` WHERE `cti_id` = '$cti_id' AND `audit_type` = '1001' AND `iso_prod_type` = '1' AND `deleted` = '0'");
extract($p_info);
$cti = $db->find_one("contract_item",array("cti_id"=>$cti_id));
$wordlist = array();
if($cti[status] == '1'){
	$wordlist[] = array(
				"url"		=> "070401-04&cti_id=$cti_id",
				"filename"	=> "产品认证申请受理通知书",
				"status"	=> "0"
				);

}elseif($cti[status] == '2'){
	$wordlist[] = array(
				"url"		=> "070401-03&cti_id=$cti_id",
				"filename"	=> "认证申请不予以受理通知书",
				"status"	=> "0"
				);
		
	
}

$list = array(
		array(
			"url"		=> "070401-07&type=view&pid=$pid",
			"filename"	=> " 样品测试通知",
			"status"	=> "0"
			),
		array(
			"url"		=> "070401-08&type=view&pid=$pid",
			"filename"	=> " 送样通知和型式试验方案",
			"status"	=> "0"
			),
		array(
			"url"		=> "070401-09&type=view&pid=$pid",
			"filename"	=> " 样品真实性审查结果报告",
			"status"	=> "0"
			),
		array(
			"url"		=> "070406-02&type=view&pid=$pid",
			"filename"	=> "转换证书资料交接单",
			"status"	=> "0"
			),
		array(
			"url"		=> "070501.09&pid=$pid",
			"filename"	=> "产品检测整改通知",
			"status"	=> "0"
			),
		array(
			"url"		=> "070501-05&type=view&pid=$pid",
			"filename"	=> "样品问题报告",
			"status"	=> "0"
			),
		array(
			"url"		=> "070406-01&type=view&cti_id=$cti_id",
			"filename"	=> "自愿转换证书和质量保证声明",
			"status"	=> "0"
			),
		array(
			"url"		=> "070501-01&type=view&pid=$pid",
			"filename"	=> "补充资料与样品清单",
			"status"	=> "0"
			),
		);
$wordlist = array_merge($wordlist , $list);
tpl();


 