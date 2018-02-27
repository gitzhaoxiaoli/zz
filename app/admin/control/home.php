<?php
!defined('IN_SUPU') && exit('Forbidden');



//公司公告

$notices = array();
$total=$db->get_var("SELECT COUNT(*) FROM sp_notice  WHERE status=1");
$pages = numfpage( $total, 8);
$query = $db->query( "SELECT n.*,hr.name author FROM sp_notice n INNER JOIN sp_hr hr ON hr.id = n.create_uid WHERE n.status=1 ORDER BY id DESC $pages[limit]" );
while( $rt = $db->fetch_array( $query ) ){

	$rt['filename'] = substr($rt['filename'],strlen($rt['id'].'_') );
	//$rt['filename']
	$notices[] = $rt;
}



//if( check_sys_boolen( 'notice' ) ){

	//合同统计
	/* @zys 2016-7-7 */
	$ct_total = array(0,0,0,0,0,0);
	$query = $db->query("SELECT status,COUNT(*) total FROM sp_contract WHERE deleted = '0' AND iso_prod_type = 0 GROUP BY status");
	while( $rt = $db->fetch_array( $query ) ){
		$ct_total[$rt['status']] = $rt['total'];
	}

	//检测
	/*
	$test_wait_array = array(0,0);
	$test_wait_array[0] = $db->get_var("SELECT COUNT(*) total FROM sp_project p WHERE 1  AND p.deleted=0 AND p.iso_prod_type = 1 AND is_samp = 1 AND p.test_org_id = 0");
	$test_wait_array[1] = $db->get_var("SELECT COUNT(*) total FROM sp_project p WHERE 1  AND p.deleted=0 AND p.iso_prod_type = 1 AND is_samp = 1 AND p.test_org_id > 0");
	$test_array = array(0,0,0,0);
	$query = $db->query("SELECT is_qualified,COUNT(*) total FROM sp_project WHERE deleted = '0' AND iso_prod_type = 1 AND is_samp = 1 and test_org_id > 0 GROUP BY is_qualified");
	while( $rt = $db->fetch_array( $query ) ){
		$test_array[$rt['is_qualified']] = $rt['total'];
	}	
	*/



	//审核项目
	$project_total = array(0,0,0,0);
	//$query = $db->query( "SELECT status,COUNT(*) total FROM sp_project WHERE 1 AND status =0" );
	//未安排
	$res=$db->find_one("project"," AND status =0 AND deleted = 0 AND iso_prod_type = 0","COUNT(*)");
	$project_total[0]=$res['COUNT(*)'];
	$query = $db->query("SELECT status,COUNT(*) total FROM sp_task   WHERE 1 AND status IN (1,2,3) and deleted=0 AND iso_prod_type = 0 GROUP BY status");
	while( $rt = $db->fetch_array( $query ) ){
		$project_total[$rt['status']] = $rt['total'];
	}

	//认证评定
	$assess_total = array(0,0,0,0,0,0);

	$query = $db->query("SELECT pd_type,COUNT(*) total FROM sp_project WHERE 1 and status=3 and deleted=0 AND iso_prod_type = 0 AND  redata_status = 1 GROUP BY pd_type ");
	while( $rt = $db->fetch_array( $query ) ){
		$assess_total[$rt['pd_type']] = $rt['total'];
	}


	//证书
	$cert_total = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0);
	$query = $db->query("SELECT status,COUNT(*) total FROM sp_certificate WHERE 1 AND deleted=0 AND is_check = 'y' GROUP BY status");
	while( $rt = $db->fetch_array( $query ) ){
		// isset( $cert_total[$rt['status']] ) or $cert_total[$rt['status']] = 0;
		$cert_total[(int)$rt['status']] = $rt['total'];
	}
	
	
	$day_time = date('Y-m-d');
	$day_time1 = thedate_add($day_time,70,'day');
	$change_total = $db->get_var("SELECT COUNT(cert.id) FROM sp_certificate cert LEFT JOIN sp_certificate_change c ON cert.id = c.zsid WHERE 1  and cert.is_check='y' and cert.status='02' and cge_date>='$day_time' and cge_date<='$day_time1' and cert.deleted=0 AND cert.iso_prod_type=1");
	if($change_total > 0 ){
		$style = "#ff0000";
	}
	
	/* 人员相关统计 */
	$curr_date = mysql2date( 'Y-m-d', current_time('mysql') );//今天
	$month_3 = thedate_add( $curr_date, 3, 'month' );
	//即将到期的聘用合同
	$hr_contract_total = $db->get_var("SELECT COUNT(*) FROM sp_hr WHERE cte_date > '$curr_date' AND cte_date < '$month_3' AND is_hire = 1");

	//即将到期的审核员证书
	$auditor_total = $db->get_var("SELECT COUNT(*) FROM sp_hr_qualification hqa INNER JOIN sp_hr hr ON hr.id = hqa.uid WHERE hqa.e_date > '$curr_date' AND hqa.e_date < '$month_3' AND hqa.status = 1 AND hr.is_hire = 1");


	//即将年满65岁的审核员
	$date_65 = mysql2date('Y-m-d', thedate_add( $curr_date, -65, 'year' ));
	$note_65 = mysql2date('Y-m-d', thedate_add( $date_65, 3, 'month' ));
	//
	$year_65_total = $db->get_var( "SELECT COUNT(*) FROM sp_hr WHERE is_hire = 1 AND job_type LIKE '%1004-1%' AND birthday > '$date_65' AND birthday <= '$note_65'" );
//}
$job_type = explode('|', current_user('job_type'));
	foreach ($job_type as $k=>$v){
		if($v == '1004-1'){
			$auditor = 1;
		}
	}
$current_user = current_user('uid');
//$current_user = 8;
$query = $db->query("SELECT iso,qua_type,qua_no,s_date,e_date FROM sp_hr_qualification WHERE uid = '".$current_user."' AND iso!='OTHER' AND status=1");
while( $rt = $db->fetch_array( $query ) ){
	/*
	$s_month = substr($rt['s_date'],5,2);
	if(in_array($s_month,array('08', '09', '10', '11', '12', '01'))) {
		if(in_array(date('m',time()),array('10','11', '12', '01'))) {
			$warn[] = $rt;
		}
	}
	else {
		if(in_array(date('m',time()),array('04','05', '06', '07'))) {
			$warn[] = $rt;
		}
	}*/
	$warn[] = $rt;
}


//@lyh 2016-03-20 微信  仅已经注册用户，提醒
$uid		= current_user('uid');
$wstatus	= $db->getField("hr","wstatus",array('id'=>$uid));
if($wstatus=='1'){
    $msgtxt=$msgtxt."微信已经注册，请进入 个人中心 微信关注，使用微信扫一扫<br>";

}

tpl('home');
?>