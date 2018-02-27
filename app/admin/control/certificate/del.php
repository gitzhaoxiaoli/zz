<?php
!defined('IN_SUPU') && exit('Forbidden'); 
//删除证书 
	$zid=getgp('zsid'); 
	$pid=getgp('pid'); 
	$type = getgp("type");
	if($type=='alist'){//证书登记，项目 在未登记  状态下删除，ifchangecert是否换证  状态 修改为2（已登记）
		
		$db->update("project",array("ifchangecert"=>2),array("id"=>$pid));
		
	}else{
		if($zid){
			$zsid = $certificate->del($zid);
		}  
		$cert_info=$certificate->get($zid);
		log_add($cert_info['eid'], 0, "[说明:删除证书].编号：".$cert_info['certno'],'','');
	}
	showmsg( 'success', 'success', $_SERVER['HTTP_REFERER']);