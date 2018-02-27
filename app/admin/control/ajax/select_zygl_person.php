<?php
!defined('IN_SUPU') && exit('Forbidden');
	require_once (ROOT . '/data/cache/audit_job.cache.php');
    require_once (ROOT . '/data/cache/ctfrom.cache.php');
    require_once (ROOT . '/data/cache/qualification.cache.php');
	require_once (ROOT .'/data/sys_cache/region.cache.php'); //省份
$where = $join = '';






//获取有管理用人员资格的人
//$join = " RIGHT JOIN sp_hr_audit_code hac ON hr.id = hac.uid";
$hr_quas = array(); 
$sql = "SELECT * FROM sp_hr hr $join WHERE 1 $where AND hr.job_type like '%1002%'";
$query   = $db->query($sql);
while($rt = $db->fetch_array($query)){
	if($rt['sex'] == 1){
		$rt['sex'] = '男';
	}else{
		$rt['sex'] = '女';
	}
	//专兼职
	switch($rt['audit_job']){
		case 1:
		   $rt['audit_job'] = "专职";
		   break;
		case 2:
		   $rt['audit_job'] = "兼职";
		   break;
		default:
		   $rt['audit_job'] = "其它";
	}
	//省份
	$rt['areacode'] = substr($rt['areacode'],0,2).'0000';
	$rt['province'] = f_region_province( $rt['areacode'] );
	$hr_quas[$rt['id']] = $rt;
}
tpl();
?>