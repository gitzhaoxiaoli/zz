<?php
!defined('IN_SUPU') && exit('Forbidden');
// set_time_limit(0);
// excel导入到数据库 重复的更新,没有的添加
$file_dir = "data/imp/zzrz.xls";
$data = excel_read($file_dir);
$i = 1;
foreach ($data['Sheet1'] as $key => $value) {
	// 转换
	if($value['E'] == '高级审核员'){
		$hr['E'] = '1004';
		$hr_qualification['E'] = '01';
	}elseif($value['E'] == '审核员'){
		$hr['E'] = '1004';
		$hr_qualification['E'] = '02';
	}
	// 判断专兼职
	if($value['I'] == '兼职'){
		$hr['I'] = '0';
	}elseif($value['I'] == '专职'){
		$hr['I'] = '1';
	}else{
		$hr['I'] = '9';
	}

	$hr['G'] = strtr($value['G'],'.','-');
	$hr['H'] = strtr($value['H'],'.','-');
	// p($hr['G']);die;
	$arr = array(
		'ctfrom'	=> '01000000',
		'iso'		=> 'A10',
		'qua_type'	=> $hr_qualification['E'], // 级别
		'qua_no'	=> $value['F'], //发文文号
		's_date '	=> $hr['G'], // 日期
		'e_date'	=> $hr['H'] //三年有效期
		
	);
	$query = $db->query( "SELECT * FROM sp_hr WHERE name = '$value[B]'" );


	if( $rt = $db->fetch_array($query) ){
		// p($rt);die;
		$rt['birthday'] = substr($value['C'],6,4)."-".substr($value['C'],10,2).'-'.substr($value['C'],12,2);
		$sex = substr($value['C'],-2,1);
		// p($value['C']);

		// p($sex);
		// echo '<br>';
		if($sex%2==0){
			$sex = '2';
		}else{
			$sex = '1';
		}

		$arr_hr=array(
			'ctfrom'	=> '01000000',
			'is_hire'	=> '1',
			'sex'		=> $sex,
			'birthday'	=> $rt['birthday'],  // 生日
			'card_no' 	=> $value['C'],	// s身份证号
			'tel'		=> $value['D'], // 手机号
			'job_type'	=> $hr['E'], 	// 级别
			'audit_job'	=> $hr['I'],	// 专兼职
		);

			// echo 123;
		$db->update('hr',$arr_hr,array('name' => $value[B]));

		$sql = $db->query( "SELECT * FROM sp_hr_qualification WHERE uid = '$rt[id]'" );
		if($rts = $db->fetch_array($sql) ){
			$db->update('hr_qualification',$arr,array('uid' => $rt['id']));
		}else{
			$arr['uid'] = $rt['id'];
			$db->insert('hr_qualification',$arr);
		}
		
		$i++;
	}elseif(!empty($value['B'])){
		$rt['birthday'] = substr($value['C'],6,4)."-".substr($value['C'],10,2).'-'.substr($value['C'],12,2);
		$sex = substr($value['C'],17,1);
		if($sex%2==0){
			$sex = '2';
		}else{
			$sex = '1';
		}

		$arr_hr=array(
			'ctfrom'	=> '01000000',
			'is_hire'	=> '1',
			'sex'		=> $sex,
			'name'		=> $value['B'],
			'birthday'	=> $rt['birthday'],  // 生日
			'card_no' 	=> $value['C'],	// s身份证号
			'tel'		=> $value['D'], // 手机号
			'job_type'	=> $hr['E'], 	// 级别
			'audit_job'	=> $hr['I'],	// 专兼职
		);
		$uid = $db->insert('hr',$arr_hr);

		$arr['uid'] = $uid;

		$db->insert('hr_qualification',$arr);
		echo $db->sql;
		echo '<br>';
		$g++;
	}
}
		echo $i;
		echo '<br>';
		echo $g;



?>