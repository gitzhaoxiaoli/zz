<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *选择专业管理人员（弹窗）
 */	
	extract($_GET, EXTR_SKIP);

	$where="";
    if($name=trim($name)){
		$where .=" and hr.name like '%$name%'";
	
	}
	if($easycode=trim($easycode)){
		$where .=" and hr.easycode like '%$easycode%'";
	
	}
    
    $_uids = $db->get_col("SELECT DISTINCT(uid) FROM sp_task_audit_team WHERE tid = $tid AND deleted = 0 ");
    array_push($_uids, -1);
    $where .= " AND id NOT IN (".join(",",$_uids).")";
		
		 
	$sql="SELECT id,name,sex,code,easycode FROM `sp_hr` WHERE `job_type` like '%1008%'  AND `deleted` = '0' AND `is_hire` = '1' $where";
	$hrs = array();
	$query=$db->query($sql);
    while ($rt = $db->fetch_array($query)) {
		$rt[sex]==1?$rt[sex]="男":$rt[sex]="女";
        $hrs[] = $rt;
    }
    tpl();