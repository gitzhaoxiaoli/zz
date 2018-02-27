<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 * 数据导入 
 * GetData 日期转换函数
 * excel_read 读取 excel 函数
 * sp_settings_prod_ver 产品对标准
 */
/**
 * 初次导入
 * @var [type]
 */
function getBrithday($cardno = ''){
    if(!$cardno)return;
    if (strlen($cardno) == 18) {
        return substr($cardno,6,4) . "-" . substr($cardno, 10,2) . "-" .substr($cardno, 12,2);
    }else{
        return "19".substr($cardno,6,2) . "-" . substr($cardno, 8,2) . "-" .substr($cardno, 10,2);
    }
}

$log_dir   = IMP_DIR . "log/error-" . date("Ymd") . ".log";
$total_num = array();
$file_name = DATA_IMP . "report_cc.xls";
$data      = excel_read($file_name);
$ctfrom = '01000000';
$iso = 'A10';
$audit_ver = 'A100101';
// exit(p($data['Sheet1']));
foreach ($data['Sheet1'] as $k => $item) {
    if ($k < 8 OR !$item['C'])
        continue;
    foreach ($item as $key => $value) {
        if (in_array($value, array("/","\\"))) {
            $item[$key] = '';
        }
        $item[$key] = str_replace(array("'","\"","\\"), array("’","”","/"), $value);
        if (in_array($key, array("AG","AH","AL","AM","AN")) and substr($value, 0,1) == '4') {
            $item[$key] = GetData($value);
        }
    }
    $item['F'] = str_replace("-", "", $item['F']);
    // 处理企业
    $eid    = $db->getField("enterprises", "eid", array(
        "work_code" => $item['F']
    ));
    if(!$eid){
        $new_ep=array(
            "ep_name"           => $item['C'],
            "ep_name_e"         => $item['D'],
            "work_code"         => $item['F'],
            "industry"          => $item['G'],
            "statecode"         => "156",
            "areacode"          => $item['I'],
            "areaaddr"          => f_region_all($item['I']),
            "ep_addr"           => $item['J'],
            "ep_addrcode"       => $item['K'],
            "cta_addr"           => $item['J'],
            "cta_addrcode"       => $item['K'],
            "prod_addr"           => $item['J'],
            "prod_addrcode"       => $item['K'],
            "ep_phone"          => $item['L'],
            "ep_fax"            => $item['M'],
            "delegate"          => $item['N'],
            "nature"            => $item['O'],
            "capital"           => $item['P'],
            "currency"          => "01",
            "ep_amount"         => $item['R'],
            "ctfrom"            => $ctfrom 
            );
        $eid = $db->insert('enterprises',$new_ep);
        $total_num['ep'] ++;
    }
    //项目处理
    //合同
    $new_ct = array(
        "eid" => $eid,
        "ctfrom" => $ctfrom,
        "iso_prod_type" => "0",
        "status" => "3" //合同3 已经审批
    );
    $ct_id = $db->insert("contract", $new_ct);

    $ct_code = "ct-".sprintf("%04d",$ct_id);
    $db->update('contract',array('ct_code' => $ct_code),array('ct_id' => $ct_id));

    //合同项目=============================================
    $new_cti = array(
        "ct_id" => $ct_id,
        "eid" => $eid,
        "audit_type" => '1001',
        "ctfrom" => $ctfrom,
        "iso" => $iso,
        "mark" => "99",
        "total_num" => $item['AI'],
        "audit_ver" => $audit_ver,
        "scope" => $item['AA'],
        "total" => $item['S'],
        "iso_prod_type" => "0",
        "status" => "1"
    );
    
    $cti_id = $db->insert("contract_item", $new_cti);
    $total_num['cti'] ++;
    $cti_code = "cti-".sprintf("%04d",$cti_id);
    $db->update('contract_item',array('cti_code' => $cti_code),array('cti_id' => $cti_id));   
    //===================================================================================
    // 项目project
    $new_p = array(
        "cti_id" => $cti_id,
        "eid" => $eid,
        "ct_id" => $ct_id,
        "ct_code" => $ct_code,
        "cti_code" => $cti_code,
        "audit_ver" => $audit_ver,
        "audit_type" => '1003',
        "ctfrom" => $ctfrom,
        "iso" => $iso,
        "st_num" => $item['AI'],
        "mark" => "99",
        "iso_prod_type" => "0",
        "redata_status" => "1",
        "product_status" => "1",
        "is_check" => "1",
        "pd_type" => "1",
        "assess_date" => $item['AL'],
        "sp_date" => $item['AL'],
        "status" => "3"
    );
    $comment_name = str_replace(" ", "", $item['AK']);
    $comment_id = $db->getField('hr','id',array('name' => $comment_name));
    if (!$comment_id) {
        $comment_id = $db->insert('hr',array("name"=>$comment_name));
    }
    $new_p['comment_a_uid'] = $comment_id;
    $new_p['comment_a_name'] = $comment_name;
    $pid   = $db->getField("project", "id", array(
        "eid" => $eid
    ));
    if ($pid)
        $db->update("project", $new_p, array(
            "id" => $pid
        ));
    else {
        $pid = $db->insert("project", $new_p);
        $total_num['project'] ++;
    }
    // 处理task
    $item['AG'] .= " 08:00";
    $item['AH'] .= " 17:00";
    $new_task = array(
        "eid" => $eid,
        "tb_date" => $item['AG'],
        "te_date" => $item['AH'],
        "tk_num" => $item['AI'],
        "jh_sp_status" => "1",
        "ctfrom" => "01000000",
        "status" => '3',
        "iso_prod_type" => '0'
    );
    $tid      = $db->getField("task", "id", array(
        "tb_date" => $item['AH'],
        "eid" => $eid
    ));
    if ($tid)
        $db->update("task", $new_task, array(
            "id" => $tid
        ));
    else {
        $tid = $db->insert("task", $new_task);
        $total_num['task'] ++;
    }
    $db->update("project", array(
        "tid" => $tid
    ), array(
        "id" => $pid
    ));
    
    // 证书
    $new_cert=array(
            "eid"               => $eid,
            "ct_id"             => $ct_id,
            "cti_id"            => $cti_id,
            "cti_code"          => $cti_code,
            "ct_code"           => $ct_code,
            "pid"               => $pid,
            "iso"               => $iso,
            "audit_ver"         => $audit_ver,
            "certno"            => $item['U'],
            "first_date"        => $item['T'],
            "s_date"            => $item['AM'],
            "e_date"            => $item['AN'],
            "cert_name"         => $item['C'],
            "cert_name_e"       => $item['D'],
            "cert_addr"         => $item['J'],
            "cert_scope"        => $item['AA'],
            "status"            => "01",
            "mark"              => "99",
            "ctfrom"            => $ctfrom,
            "is_check"          => "y"
    );
    $zsid = $db->getField('certificate','id',array('certno' => $new_cert['certno']));
    if ($zsid) {
        $db->update('certificate',$new_cert,array('id' => $zsid));
    }else{
        $db->insert("certificate",$new_cert);
        $total_num['cert'] ++;
    }
}

// 处理人员
foreach ($data['Sheet2'] as $k => $item) {
    if ($k < 8 OR !$item['C'])
        continue;
    $item['F'] = str_replace(" ", "", $item['F']);
    $new_hr=array(  "name"      => $item['F'],
                    "card_type" => "01",
                    "card_no"   => $item['H'],
                    "retire"    => "1",
                    "is_hire"   => "1",
                    "job_type"  => "1004",
                    "ctfrom"    => "01000000",
                    "birthday"  => getBrithday($item['H'])
                    );

    $uid=$db->getField("hr","id",array("card_no"=>$new_hr[card_no]));
    if (!$uid) {
        $uid = $db->getField('hr','id',array('name' => $new_hr[name]));
    }
    if($uid)
        $db->update("hr",$new_hr,array("id"=>$uid));
    else{
        $uid=$db->insert("hr",$new_hr);
        $total_num['hr'] ++;
    }
    
    $new_hr_qualification=array(    
                    "uid"       => $uid,
                    "status"    => "1",
                    "iso"       => "A01",
                    "is_leader" => "1",
                    "qua_type"  => $item['J'],
                    "qua_no"    => "中认协注2013325号",
                    "s_date"    => "2016-01-01",
                    "e_date"    => "2018-12-31",
                    "ctfrom"    => "01000000",
                    "update_user"=> "管理员",
                    );
    $qua_id=$db->getField("hr_qualification","id",array("uid"=>$uid));
    if($qua_id)
        $db->update("hr_qualification",$new_hr_qualification,array("id"=>$qua_id));
    else{
        $qua_id = $db->insert("hr_qualification",$new_hr_qualification);
        $total_num['qua'] ++;
    }



    $cert=$db->get_row("SELECT * FROM `sp_certificate` WHERE `certno` = '$item[B]'");
    if(!$cert)continue;
    $p_info = $db->find_one('project',array('id' => $cert['pid']),'tid,audit_type');
    $t_info = $db->find_one('task',array('id' => $p_info[tid]),'tb_date,te_date');
    $new_tat=array(
            "eid"               => $cert[eid],
            "pid"               => $cert[pid],
            "tid"               => $p_info[tid],
            "uid"               => $uid,
            "iso"               => $iso,
            "audit_type"        => $p_info[audit_type],
            "audit_ver"         => $audit_ver,
            "name"              => $item['F'],
            "ctfrom"            => $ctfrom,
            "taskBeginDate"     => $item['D'],
            "taskEndDate"       => $item['E'],
            "role"              => "10".$item['I'],
            "qua_type"          => $item['J'],
            );
    $db->insert("task_audit_team",$new_tat);
    $total_num['tat'] ++;
}
echo "success";
p($total_num);