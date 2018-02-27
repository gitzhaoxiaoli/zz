<?php
!defined('IN_SUPU') && exit('Forbidden');
	
	$checkeds=getgp('checkeds');
	//p($checkeds);
	$res = $db->update("certificate",array('is_check'=>'e'),array('id'=>$checkeds));
	$REQUEST_URI="?c=certificate&a=approval_list";

	showmsg( 'success', 'success', $REQUEST_URI );