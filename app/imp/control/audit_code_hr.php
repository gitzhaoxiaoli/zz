<?php
//人员专业
@set_time_limit(0);
@ini_set('memory_limit', '1024M');
$db->drop('sp_hr_audit_code');

$hr_array = $db->get_results("SELECT `name` , id , ctfrom FROM `sp_hr` WHERE deleted = 0" , 'name');
// pe($hr_array);
$file_name=DATA_IMP."auditor-code.xls";
// echo $file_name;
$data=excel_read($file_name);
// p($data['Sheet1']);
// exit;

$iso_array = ['QMS' => 'A01' , 'EMS' => 'A02' , 'OHSMS' => 'A03'];
$qua_type_array = ['技术专家' => '04' ,'审核员' => '02'];
$error = array();
foreach($data['Sheet1'] as $k=>$item){
  if($k<2)continue;
  $item['A'] = str_replace(" ",'',$item['A']);
  $uid = $hr_array[$item['A']]['id'];
  if (!$uid) {
    $error[] = $item;
    continue;
  }
  $ctfrom = $hr_array[$item['A']]['ctfrom'];
  $iso = $iso_array[$item['C']];
  $qua_type = $qua_type_array[$item['B']];
  $qua_id = $db->get_var("SELECT id FROM `sp_hr_qualification` WHERE `uid` = '$uid' AND `qua_type` = '$qua_type' AND `iso` = '$iso' AND deleted = 0 ORDER BY `s_date` DESC");
  if (!$qua_id) {
    $default = array(
      'uid'   => $uid,  //用户id
      'ctfrom'  => $ctfrom,
      'iso'   => $iso,      //体系
      'qua_type'  => $qua_type,  //注册资格
      'qua_no'  => '系统生成要修改', //注册资格号码
      's_date'  => '2018-01-01', //资格开始时间
      'e_date'  => '2018-12-01', //资格结束时间

    );
    $qua_id = $db->insert('hr_qualification' , $default);
        
  }
  $new_code = array(
    'uid' => $uid,
    'iso' => $iso,
    'qua_id' => $qua_id,
    'qua_type' => $qua_type,
    'ctfrom' => $ctfrom
    );
  $item['D'] = str_replace("\n" , '',$item['D']);
  $codes = explode("；" , $item['D']);
  if (!$codes) {
    continue;
  }
  foreach ($codes as $v) {
    $new_code['audit_code'] = substr($v , 0 , 8);
    $new_code['use_code'] = $v;
    // pe($new_code);
    $db->insert('hr_audit_code' , $new_code);
        
  }
      

}

// file_put_contents(IMP_DIR."log/".date("Ymd")."log.log","13_hr_qua_code_1----".$i."----".date("Y-m-d H:i:s")."\r\n",FILE_APPEND);

$error && export_excel($error , 'e');
