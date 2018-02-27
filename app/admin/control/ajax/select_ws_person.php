<?php
!defined('IN_SUPU') && exit('Forbidden');

/*
*选择派人
*/
    require_once (ROOT .'/data/sys_cache/region.cache.php'); //省份
    require_once (ROOT . '/data/cache/audit_job.cache.php');
    require_once (ROOT . '/data/cache/qualification.cache.php');
    require_once (ROOT . '/data/cache/ctfrom.cache.php');
     $sql = "SELECT * from sp_hr where deleted = 0";
	 $query = $db->query($sql);
	 $hr_lists = array();
	 while($rt = $db->fetch_array($query))
	 {
		 $rt['province']	= f_region_province( $rt['areacode'] );         $rt['audit_job'] = f_audit_job($rt['audit_job']);
         $rt['sex'] = ($rt['sex'] == 1) ? '男' : '女';
		 $hr_lists[$rt['id']] = $rt;
	 };
    
    tpl();
?>