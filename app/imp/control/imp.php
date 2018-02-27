<?php
//导数据概述：根据体系上报数据导数据
//导数据的几种情况： 原有数据库--上报报表-excel access
//输数据模块应用函数
function get_tid($args, $is_add = false)
{
    $task_info = load('task')->get(array(
        'eid' => $args['eid'],
        'tb_date' => $args['tb_date'],
        'te_date' => $args['te_date'],
    ));
/*	if($args['eid']==6 and $args['old_id']=='201401'){
		
	p($args);	
	}*/
    $tid       = $task_info['id'];
    if (!$tid and $is_add) {
        $tid = load('task')->add($args);
    }
    return $tid;
}
function get_eid($args, $is_add = true)
{
    global $db;
    $ep  = load('enterprise');
    //根据组织机构代码判断企业是否存在
    $eid = $db->getField('enterprises', 'eid', array(
        'work_code' => trim($args['work_code'])
    ));
    if (!$eid and $is_add) {
        $eid = $ep->add($args, true);
    }
 
    return $eid;
}
function get_region_by_country($code)
{
    global $db;
    //区县
    $country  = $db->getField('settings_region', 'name', array(
        'code' => $code
    ));
    //市
    $city     = $db->getField('settings_region', 'name', array(
        'code' => substr($code, 0, 4) . '00'
    ));
    $province = $db->getField('settings_region', 'name', array(
        'code' => substr($code, 0, 2) . '0000'
    ));
    return $province . $city . $country;
}
function get_cert_id($args, $is_add = true)
{
    global $db;
    //
    $cert_id = $db->getField('certificate', 'id', array(
        'certno' => $args['certno']
    ));
    if (!$cert_id and $is_add) {
        return $cert_id = $db->insert('certificate', $args);
    }
    return $cert_id;
}
function get_uid($args, $is_add = false)
{
    global $db;
    $uid              = $db->getField('hr', 'id', array(
        'name' => trim($args['name'])
    ));
    $args['ctfrom']   = '01000000';
    $args['is_hire']  = 1;
    $args['job_type'] = 1004-1;
    if (!$uid and $is_add) {
        return $uid = $db->insert('hr', $args);
    }
    return $uid;
}
function get_hr_qua($args, $is_add = false)
{
    global $db;
    $id = $db->getField('hr_qualification', 'id', $args);
    if (!$id and $is_add) {
        $args['ctfrom'] = '01000000';
        return $db->insert('hr_qualification', $args);
    }
    return $id;
}
 
 //审核类型映射: 原编码，监督次数
function map_audit_type($code,$num){
	
	 $map_audit_type=array(
 	'01'=>'1001',
	'02'=>'1007',
	'99'=>'99', 
	'04'=>'1101',
	'0302'=>'1009', 
 	);
	if($num==1){ 
		$map_audit_type['0301']='1004-1';	
	}elseif($num==2){
		$map_audit_type['0301']='1004-2';	
		
		}elseif($num==3){
			$map_audit_type['0301']='1004-3';	
			
	 }
 	return $map_audit_type[$code];
} 
/**
 * 通过身份证号得生日
 * 11010619830220091X
 * @param  string $cardno [description]
 * @return [type]         [description]
 */
function getBrithday($cardno = ''){
    if(!$cardno)return;
    if (strlen($cardno) == 18) {
        return substr($cardno,6,4) . "-" . substr($cardno, 10,2) . "-" .substr($cardno, 12,2);
    }else{
        return "19".substr($cardno,6,2) . "-" . substr($cardno, 8,2) . "-" .substr($cardno, 10,2);
    }
}


if ($_GET['a']) {
/* 	$qua_type_array=array("GC"=>"05","GJ"=>"01","JC"=>"06","SH"=>"02","SX"=>"03","YZ"=>"0j","ZJ"=>"04");
	$iso_array=array("Q1"=>"A01","E1"=>"A02","S1"=>"A03","P1"=>"B05","J1"=>"J01");
    // 数据源对象-强制实例化导数据
    $db_source = load('db.mysql', true);
    $db_source->connect(get_option('db.db_host'), get_option('db.db_user'),get_option('db.db_pwd'),get_option('db.from'));
    // 初始化
    $db_source->_pre = '';
 */	
 
    set_time_limit(0);
    @ini_set('memory_limit', '1024M');
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
开始导数据';
    echo '<br>'; 
	
    $ctl_path = APP_DIR . 'imp/control/' . $_GET['a'] . '.php';
    require $ctl_path;
    exit;
}
//读取导数据菜单
tpl();