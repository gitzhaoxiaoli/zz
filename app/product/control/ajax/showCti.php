<?php
!defined('IN_SUPU') && exit('Forbidden');
$cti_id=getgp("cti_id");
$ctiInfo=$db->get_row("SELECT * FROM `sp_contract_item` WHERE `cti_id` = '$cti_id'");
// p($ctiInfo);
extract($ctiInfo);
$ep_name = $db->getField("enterprises","ep_name",array("eid" => $eid));
$ep_manu = $db->getField("enterprises","ep_name",array("eid" => $ep_manu_id));
$ep_prod = $db->getField("enterprises","ep_name",array("eid" => $ep_prod_id));
$prod_name = $db->getField("settings_prod_xiaolei","name",array("code" => $prod_id));
$archives = $db->get_results("SELECT * FROM sp_attachments  WHERE cti_id='$cti_id' order by id desc ");
// echo $cti_code;
tpl();
 