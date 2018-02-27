<?php
!defined('IN_SUPU') && exit('Forbidden');
$cti_id = getgp("cti_id");
$pid = getgp("pid");
$p_info = $db->find_one("project",array("id"=>$pid),"cti_id,test_org_id,test_org_name,tid");
!$cti_id && $cti_id = $p_info[cti_id];
$cti = $db->find_one("contract_item",array("cti_id"=>$cti_id),"eid,ep_manu_id,ep_prod_id,prod_name_chinese,prod_id,prod_ver,cti_code,scope");
if($cti)extract($cti);
$ep_name = $db->getField("enterprises","ep_name",array("eid"=>$eid));
$ep_manu_name = $db->getField("enterprises","ep_name",array("eid"=>$ep_manu_id));
$ep_prod_name = $db->getField("enterprises","ep_name",array("eid"=>$ep_prod_id));

$wordlist = array(
		array(
			"url"		=> "070401-08&type=view&pid=$pid",
			"filename"	=> "送样通知和型式试验方案",
			"status"	=> "0"
			),
		array(
			"url"		=> "070401-09&pid=$pid",
			"filename"	=> " 样品真实性审查结果报告",
			"status"	=> "0"
			),
		array(
			"url"		=> "070501-05&type=view&pid=$pid",
			"filename"	=> "样品问题报告",
			"status"	=> "0"
			),
		array(
			"url"		=> "070501-09&type=view&pid=$pid",
			"filename"	=> "产品检测整改通知",
			"status"	=> "0"
			),
		array(
			"url"		=> "070801-02&pid=$pid",
			"filename"	=> "认证证书保持通知",
			"status"	=> "0"
			),
		);
$width = "950";
$title = "WORD列表";
tpl();