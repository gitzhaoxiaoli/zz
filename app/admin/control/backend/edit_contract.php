<?php
!defined('IN_SUPU') && exit('Forbidden');

$ct_id = getgp('ct_id');

if ($_POST) {
	
	$db->update('contract' , $_POST , array("ct_id" => $ct_id));
	showmsg("success" ,"success" , "?c=backend&a=list_contract");
	
	    
}

$ct = $db->find_one('contract' , array("ct_id" => $ct_id));
extract($ct);
tpl();