<?php
!defined('IN_SUPU') && exit('Forbidden');
$tid = getgp("tid");
$cti_ids = $db->get_col("SELECT cti_id FROM `sp_project` WHERE `deleted` = '0' AND `tid` = '$tid' AND `iso_prod_type` = '1'");
array_push($cti_ids,-1);
$query = $db->query("SELECT c.certno,id,cert_name,manu_name FROM `sp_certificate` c  WHERE c.`iso_prod_type` = '1' AND c.`deleted` = '0' AND cti_id IN (".join(",",$cti_ids).")");
$data = array();
while($rt = $db->fetch_array($query)){
	
	
	$data[] = $rt;
}

tpl();
