<?php
!defined('IN_SUPU') && exit('Forbidden');

	$use_code		= trim(getgp( 'use_code' ));
	$iso			= getgp( 'iso' );



	if( $iso ){ //认证体系
		$where=" AND iso='$iso'";
	}
	if($use_code){
		$where.=" AND code like '%$use_code%'";
	
	}
	$where.=" and deleted != 1"; 
	$total=$db->get_var("SELECT COUNT(*) FROM `sp_settings_audit_code` WHERE 1  $where");

	$pages = numfpage( $total );

	//列表
	$datas = array();
	$sql = "SELECT * FROM `sp_settings_audit_code` WHERE 1  $where ORDER BY iso,code $pages[limit]";

	$query = $db->query( $sql );
	while( $rt = $db->fetch_array( $query ) ){
		$datas[]=$rt;
		
	}

	
	tpl();