<?php
!defined('IN_SUPU') && exit('Forbidden'); 
 if('add' == $a){
	 	require_once( CTL_DIR. '/cooperation/add.php' );
 }elseif('list_attach' == $a){
	    require_once( CTL_DIR. '/cooperation/list_attach.php' );
 }elseif('edit' == $a){
	 	require_once( CTL_DIR. '/cooperation/add.php' );
 }

?>