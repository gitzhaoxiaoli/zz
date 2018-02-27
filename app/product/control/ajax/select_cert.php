<?php
!defined('IN_SUPU') && exit('Forbidden');
$step = getgp("step");
if($step == '1'){
$query = $db->query("SELECT c.certno,cert_name,id FROM `sp_certificate` c  WHERE c.`iso_prod_type` = '1' AND c.`deleted` = '0'");
$data = array();
while($rt = $db->fetch_array($query)){
	
	
	$data[] = $rt;
}

tpl();
}else{
	$id = getgp("id");
	$rt = $db->get_row("SELECT c.certno,cert_name,cti.total,cti.prod_id,cti.fac_code ,cti.prod_ver FROM `sp_certificate` c LEFT JOIN sp_contract_item cti  ON cti .cti_id = c.id WHERE c.`iso_prod_type` = '1' AND c.`id` = '$id' ");
	$rt[prod_name] = $db->get_var("SELECT name FROM `sp_settings_prod_xiaolei` WHERE `code` = '$rt[prod_id]' AND `fac_code` = '$rt[fac_code]'");
	if($prod_ver = $rt[prod_ver]){
		$prod_arr=explode("；",$prod_ver);
		$body="";
		foreach($prod_arr as $value){
			$s=$db->getField("settings_ver","name",array("code"=>$value,"type"=>"b01001"));
			$body.="<p>";
			$body.=$value."  ".$s;
			$body.="<a attr='$value'  class='icon-del del_prod_ver_code'></a></p>";
			
		}
	}
	$rt['body'] = $body;
	print_json($rt);
	
}