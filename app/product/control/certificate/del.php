<?php
!defined('IN_SUPU') && exit('Forbidden'); 
//删除证书 
	$zid=getgp('zsid'); 
	$pid=getgp('pid'); 
	$type = getgp("type");
	if($type=='alist'){
		
		$db->update("project",array("ifchangecert"=>2),array("id"=>$pid));
		
	}else{

	if($zid){
		$zsid = $certificate->del($zid);
	}  
	$cert_info=$certificate->get($zid);
	log_add($cert_info['eid'], 0, "[说明:删除证书].编号：".$cert_info['certno'],'','');
	}
	showmsg( 'success', 'success', $_SERVER['HTTP_REFERER']);