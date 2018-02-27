<?php
!defined('IN_SUPU') && exit('Forbidden');
$tid = getgp("tid");
if (!$tid) {
    exit("ERROR");
}
require( DATA_DIR . 'cache/audit_ver.cache.php' );
$data = array();

$temp_array = ['person','person_bumen','person_job','person_tel','person_mph','person_email'];

for ($i=0; $i < 2; $i++) { 
    if ($i == 0) {
        $_i = "";
    } else {
        $_i = $i;
    }
    foreach ($temp_array as $v) {
        $data[$v . $_i] = "";
    }
}
// p($data);exit;

$task  = $db->find_one('task' , array("id" => $tid));

$ep_info  = $db->find_one('enterprises' , array("eid" => $task[eid]));
$person_array = get_person($task['eid']);
foreach ($person_array as $k => $v) {
    if (!$v['person']) {
        continue;
    }
    if ($k == 0) {
        $k = '';
    }
    foreach ($v as $_k => $_v) {
        $data[$_k . $k] = $_v;
    }
}
$data[ep_name] = $ep_info[ep_name];
$data[ep_addr] = $ep_info[ep_addr];
$data[prod_addr] = $ep_info[prod_addr];
$data[buss_addr] = $ep_info[buss_addr];
$data[ep_amount] = $ep_info[ep_amount];
$data[person] = $ep_info[person];
$data[person_tel] = $ep_info[person_tel];
$data[ep_fax] = $ep_info[ep_fax];
$data[ep_mail] = $ep_info[ep_mail];
$query = $db->query("SELECT * FROM `sp_enterprises_site` WHERE `eid` = '$task[eid]' AND deleted = 0");
$es = array();
while ($rt = $db->fetch_array($query)) {
    $es[] = read_cache("site_type" , $rt['es_type']) . ":" .$rt['ep_name'] ."/".$rt[ep_addr];
}
unset($query,$rt);

$data[es] = join(";",$es);

$iso_array = ['A01' => 'QMS' , 'A02' => 'EMS' , 'A03' => 'OHSMS'];

$query = $db->query("SELECT p.*,cti.exc_clauses,cti.total FROM `sp_project` p LEFT JOIN sp_contract_item cti ON cti.cti_id = p.cti_id WHERE `tid` = '$tid' AND p.deleted = 0");
$total = $audit_type = $audit_ver = $exc_clauses = $scope = array();
while ($rt = $db->fetch_array($query)) {
    $_iso = $iso_array[$rt[iso]];
    $data[ct_code] = $rt['ct_code'];
    $total[] = $_iso . "：".$rt[total];
    $scope[] = $_iso . "：".$rt[scope] . "/" . $rt[audit_code];
    $audit_type[] = $_iso . "：".f_audit_type($rt[audit_type]);
    $audit_ver[] = $_iso . "：".$audit_ver_array[$rt[audit_ver]][audit_basis];
    if (substr($rt[iso] , 0 ,5 ) == 'A01') {
        $exc_clauses[] = $_iso . "：".$rt[exc_clauses];
    }

}
unset($query,$rt);

$data[total] = join(";" , $total);
$data[audit_type] = join(";" , $audit_type);
$data[audit_ver] = join(";" , $audit_ver);
$data[exc_clauses] = join(";" , $exc_clauses);
$data[scope] = join(";" , $scope);
    
$data['tk_num'] = $task[tk_num] ;
$data['name'] = current_user('name');
$data['date'] = date("Y-m-d");
list($tb_date , $tb_time) = explode(" ", $task[tb_date]);
if ($tb_time == '13:00:00') {
           $tb_time = '下午';
}else{
    $tb_time = '上午';
}
$tb_date .= $tb_time;
list($te_date , $te_time) = explode(" ", $task[te_date]);
if ($te_time == '12:00:00') {
           $te_time = '上午';
}else{
    $te_time = '下午';
}
$te_date .= $te_time;
$data['audit_date'] = $tb_date . "至" . $te_date;
$data['note'] = $task['note'];

$query = $db->query("SELECT tat.*,hr.tel,hr.sex FROM `sp_task_audit_team` tat LEFT JOIN sp_hr hr ON hr.id = tat.uid WHERE `tid` = '$tid' AND tat.deleted = 0   ORDER BY role");
while ($rt = $db->fetch_array($query)) {
    $_iso = $iso_array[$rt[iso]];
    $qua = $db->get_var("SELECT qua_no FROM `sp_hr_qualification` WHERE `uid` = $rt[uid] AND `iso` = '$rt[iso]' ORDER BY `e_date` DESC LIMIT 1");
        
    $auditor[$rt[name]][audit_code][] = $_iso . ":". $rt[audit_code];
    $auditor[$rt[name]][qua_type][] = $_iso . ":" . read_cache('qualification' , $rt['qua_type']) . "(" . $qua . ")";
    $auditor[$rt[name]][iso][] = $_iso;
    $auditor[$rt[name]][tel] = $rt['tel'];
    $auditor[$rt[name]][sex] = $rt['sex'];
    $auditor[$rt[name]][role] = $rt['role'];
}
unset($query,$rt);

for ($i=1; $i < 8; $i++) { 
    for ($j=1; $j < 8; $j++) { 
        $data['f' . $i . $j] = "";
    }
}
$i = 1;
foreach ($auditor as $name => $v) {

    $data['f' . $i . '1'] = read_cache("audit_role",$v[role]);
    $data['f' . $i . '2'] = $name;
    $data['f' . $i . '3'] = $v[sex] == '1' ?'男':'女';
    $data['f' . $i . '4'] = join(";", $v[qua_type]);
    $data['f' . $i . '5'] = join(";", $v[iso]);
    $data['f' . $i . '6'] = join(";", $v[audit_code]);
    $data['f' . $i . '7'] = $v[tel];
    $i++;
}
// p($data);
// exit;

require(STYLESHEET_DIR.'word/PHPWord.php');
$PHPWord = new PHPWord();
$document = $PHPWord->loadTemplate(DOCTPL_PATH.'view/rws.docx');
foreach ($data as $k => $v) {
    
    $document->setValue($k,$v);

}

$filedir = DATA_DIR.'word/temp.docx';
$document->save($filedir);
$filename = $ep_info[ep_name]."-任务书.docx";
export_word($filename, $filedir);
?>