<?php
!defined('IN_SUPU') && exit('Forbidden');

 $id = getgp('id');
 
	if($id){
		$res = $auditcode->get($id,"1");
		 log_add(0, $res[uid], "删除业务代码-".$res[use_code], '', '');
		$auditcode->del($id);
	}
	$REQUEST_URI='?c=hr_code&a=list';
	showmsg( 'success', 'success', $REQUEST_URI );

?>