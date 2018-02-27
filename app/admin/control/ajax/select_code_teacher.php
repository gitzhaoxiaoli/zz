<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *选择专业管理人员（弹窗）
 */	
	extract($_GET, EXTR_SKIP);
	$where="";
    if($name=trim($name)){
		$where .=" and name like '%$name%'";
	
	}
	if($easycode=trim($easycode)){
		$where .=" and easycode like '%$easycode%'";
	
	}
    
		
	$where .=" AND deleted=0";
	$count=$db->get_var("SELECT COUNT(*) FROM `sp_hr` WHERE `job_type` LIKE '%1001%' $where");
	$pages = numfpage( $count, 10 );	
	$sql="SELECT * FROM `sp_hr` WHERE `job_type` LIKE '%1001%' $where $pages[limit]";
	$hrs = array();
	$query=$db->query($sql);
    while ($rt = $db->fetch_array($query)) {
		$rt[sex]==1?$rt[sex]="男":$rt[sex]="女";
        $hrs[] = $rt;
    }
    tpl();