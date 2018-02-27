<?php
/**
 * 审核员年报
 */
set_time_limit(0);
$begindate = getgp('s_date');//取数开始日期
$enddate = getgp('e_date');//取数截止日期
$data_arr = array(array('AUDORGID','AUDSYSID','PERSNAME','PERSSEX','BIRTHDAY','PROFES','INCUMB','AREAID','AUTHENID','PERSNO','PERSUNIT','EDUCATLONAL','INSTITUTION','SPECIALTY','ALLDAYS','SYSDAYS','CHARACTAR','REGISTER','SYSCODE','ENGADATE','LEAVDATE','INFYEAR','CHECKMSG'));
$enddate .= " 23:59:59";

function get_day($a,$b){
	$day = (strtotime(substr($b,0,10))-strtotime(substr($a,0,10)))/86400 + 1;
	$shangwu = intval(substr($a, 11,2));
	$xiawu = intval(substr($b, 11,2));
	if($shangwu>=12){
		$day -= 0.5;
	}
	if($xiawu<=12){
		$day -= 0.5;
	}
	return $day;

}
// echo get_day("2015-01-02 08:00","2015-01-12 08:00");
// exit;


$sql1 = "select * from sp_task_audit_team where iso !='' and taskBeginDate >='$begindate' and taskBeginDate <= '$enddate' and deleted = 0 ORDER BY uid,taskBeginDate";
$query = $db->query($sql1);
// 每个人的总天数$count_day  每个人每个体系人天$person_count
$count_day = $person_count = array();
$temp = array();

while($row = $db->fetch_array($query)){
	$person_count[$row['uid']][$row['iso']][num] += get_day($row['taskBeginDate'],$row['taskEndDate']);
	if($temp[$row[uid]]['taskBeginDate'] == $row['taskBeginDate'])continue;
	$count_day[$row[uid]] += get_day($row['taskBeginDate'],$row['taskEndDate']);
	$temp[$row[uid]]['taskBeginDate'] = $row['taskBeginDate'];
}
// p($count_day);

// 每个人的专业
$sql = "select uid,iso,audit_code from sp_hr_audit_code where deleted = 0";
$res = $db->query($sql);
while($row = $db->fetch_array($res)){
	$code_s[$row['uid']][$row['iso']][] = $row['audit_code'];
}

$zdep_id = 'CNAS-'. substr(get_option('zdep_id'),-3);
$zdep_name = get_option('zdep_name');
$year = substr($begindate, 0,4);
$sql = "select is_office,uid,iso,qua_type,qua_no,name,cts_date,cte_date,sex,birthday,audit_job,card_no,code,technical,unit,areacode from sp_hr_qualification hq left join sp_hr h on h.id=hq.uid where hq.qua_type in ('01','02','04') and hq.status='1' and h.is_hire =1 and h.deleted = 0  AND h.job_type like '%1004%' ";
$res = $db->query($sql);

while($row=$db->fetch_array($res)){
	if($row['sex']==1){
		$row['sex'] = '01';
	}else{
		$row['sex'] = '02';
	}
	if($row['audit_job']==1){
		$row['audit_job'] = '02';
	}else{
		$row['audit_job'] = '01';
	}
	if($row['is_office'] == '1'){
		$row['audit_job'] = '03';
	}
	if($row['audit_job'] != '01'){
		$row['unit'] = $zdep_name;
	}
	$j_info = $db->get_row("select * from sp_hr_experience where add_hr_id='$row[uid]' AND type = 'j'  AND `area` <> ' '  order by e_date desc limit 1 ");
	$row['xueli'] = $j_info['department'];
	$row['school'] = $j_info['area'];
	$row['zhuanye'] = $j_info['position'];

	if(!$person_count[$row[uid]][$row[iso]][num]){
		$person_count[$row[uid]][$row[iso]][num] = '0.0';
	}
	$_code = join("；",$code_s[$row[uid]][$row[iso]]);
	if(!$_code){
		$_code = '无';
	}

	$data_arr[] = array(
		"0"			=> $zdep_id,
		"1"			=> f_iso($row['iso']),
		"2"			=> $row['name'],
		"3"			=> $row['sex'],
		"4"			=> $row['birthday'],
		"5"			=> $row['technical'],
		"6"			=> $row['audit_job'],
		"7"			=> $row['areacode'],
		"8"			=> $row['card_no'],
		"9"			=> $row['code'],
		"10"		=> $row['unit'],
		"11"		=> $row['xueli'],
		"12"		=> $row['school'],
		"13"		=> $row['zhuanye'],
		"14"		=> $count_day[$row[uid]],
		"15"		=> $person_count[$row[uid]][$row[iso]][num],
		"16"		=> $row['qua_type'],
		"17"		=> $row['qua_no'],
		"18"		=> $_code,
		"19"		=> $row['cts_date'],
		"20"		=> $row['cte_date'],
		"21"		=> $year,
		"22"		=> '',
		);
	}
export_excel($data_arr,'审核员年报');