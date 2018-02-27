<?php 
  !defined('IN_SUPU') && exit('Forbidden');
	$type = trim( getgp( 'type' ) );
	$gro_id = (int)getgp('gro_id');
	$left_nav = array_reverse($left_nav);

  //@lyh 2016-3-15  编辑权限组
  $gro_info = $db->find_one('settings',array('id'=>$gro_id));

  tpl();
?>