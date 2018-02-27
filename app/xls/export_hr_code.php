<?php
!defined('IN_SUPU') && exit('Forbidden');
$data = array(array("序号","姓名","性别","年龄","所在地","电话","注册资格号","体系","专业小类
"));
// $hr = $db->find_one('hr',array('name' => $name),'name,sex,birthday,areacode_str,tel');
// echo $db->sql;
// exit;
// $data[] = $hr;

$i = 1;


$query = $db->query("SELECT qua.id,qua.uid,qua.qua_no,name,sex,birthday,areacode_str,tel FROM `sp_hr_qualification` qua LEFT JOIN sp_hr hr ON qua.uid = hr.id WHERE `status` = '1' AND qua.`deleted` = '0'");

while($rt = $db->fetch_array($query)){

	$rt[sex] = $rt[sex] == '1'?'男':'女';
	$rt[birthday] = date("Y-m-d") - $rt[birthday];
	$rt[code] = $db->getCol("hr_audit_code",'audit_code',array("qua_id"=>$rt[id]));
	$rt[code] = join("；",$rt[code]); //$rt[code]是数组  转换成字符串
	$data[] = array($i++,$rt[name],$rt[sex],$rt[birthday],$rt[areacode_str],$rt[tel],$rt[qua_no],$rt[code]);
	

}

unset($query,$rt);
// exit(p($data));
export_excel($data,"人员专业");
?>