<?php 
   !defined('IN_SUPU') && exit('Forbidden');
   $old_tel = getgp('tel');
   $uid = getgp('uid');
   if($uid){
   if($old_tel){
	   $tmp_tel = $db->find_num('hr'," AND tel = $old_tel AND id != $uid");
	   if((int)$tmp_tel){
		   echo 11;
	   }else{
		   echo 0;
	   }
   }   
   }else{
   if($old_tel){
	   $tmp_tel = $db->find_num('hr'," AND tel = $old_tel");
	   if((int)$tmp_tel){
		   echo 11;
	   }else{
		   echo 0;
	   }
   }
   }
?>