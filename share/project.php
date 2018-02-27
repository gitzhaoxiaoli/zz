<?php
// 项目查询列表
require "config.php";
require "../data/cache/audit_type.cache.php";
require "../data/cache/check_result.cache.php";
require_once (ROOT .'/data/sys_cache/region.cache.php'); //省份

$province_select = '';
if ($region_array) {
    foreach ($region_array as $code => $item) {
        if ('0000' == substr($code, 2, 4))
            $province_select .= "<option value=\"$code\">$item[name]</option>";
    }
}



//审核类型
$audit_type_select = '';
if ($audit_type_array) {
    foreach ($audit_type_array as $code => $item) {
        if (!in_array($code, array(
            '1002',
            '1003',
            '1007',
            '1008',
            '1010',
            '99'
        )))
            $audit_type_select .= "<option value=\"$code\">$item[name]</option>";
    }
}
// p($_SERVER);
$REQUEST_URI = preg_replace("/&paged=\d*/","",$_SERVER[REQUEST_URI]);
$project_status    = array(
    '未安排',
    '待派人',
    '待审批',
    '已审批',
    '',
    '维护'
);
$test_status_array = array(
    '未检验',
    '合格',
    '需整改',
    '不合格'
);
$pd_type_array     = array(
    '未评定',
    '通过',
    '待定',
    '不通过'
);
$cert_status_array = array(
    '01' => '有效',
    '02' => '暂停',
    '03' => '撤销',
    '04' => '注销',
    '05' => '过期失效'
);
extract($_GET, EXTR_SKIP);
$audit_pids = $assess_pids = array();

$fields = $join = $where = $page_str = '';

if ($ep_name) {
    $where .= " AND e.ep_name like '%$ep_name%'";
}
if ($ep_prod_name = trim($ep_prod_name)) {
    $eids = $db->get_col("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%$ep_prod_name%' AND deleted = 0");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . join(",", $eids) . ")";
    
}

if ($fac_code = trim($fac_code)) {
    $eids = $db->get_col("SELECT eid FROM sp_enterprises WHERE fac_code LIKE '%$fac_code%' AND deleted = 0");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . join(",", $eids) . ")";
    
}

//审核类型
if ($audit_type) {
    // $where .= " AND cti.audit_type = '$audit_type'";
    $eids = $db->get_col("SELECT eid FROM sp_enterprises WHERE audit_type LIKE '%$audit_type%' AND deleted = 0");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . join(",", $eids) . ")";
    $audit_type_select = str_replace("value=\"$audit_type\">", "value=\"$audit_type\" selected>", $audit_type_select);
}


if ($ep_manu_name = trim($ep_manu_name)) {
    $eids = $db->get_col("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%$ep_manu_name%' AND deleted = 0");
    array_push($eids, -1);
    $where .= " AND cti.ep_manu_id IN (" . join(",", $eids) . ")";
    
}
//省份
if ($areacode = getgp("areacode")) {
    $pcode  = substr($areacode, 0, 2) . '0000';
    $_eids  = array(
        -1
    );
    $_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '" . substr($areacode, 0, 2) . "'");
    while ($rt = $db->fetch_array($_query)) {
        $_eids[] = $rt['eid'];
    }
    $where .= " AND cti.ep_prod_id IN (" . implode(',', $_eids) . ")";
    unset($_eids, $_query, $rt, $_eids);
    
    $province_select = str_replace("value=\"$pcode\">", "value=\"$pcode\" selected>", $province_select);
}



//合同项目编号
if ($cti_code) {
    $where .= " AND cti.cti_code like '%$cti_code%'";
}
//认证体系
if ($iso) {
    $where .= " AND cti.iso = '$iso'";
    $iso_select = str_replace("value=\"$iso\">", "value=\"$iso\" selected>", $iso_select);
}


//计划开始时间 起
if ($audit_start_start) {
    $eids = $db->get_col("SELECT eid FROM sp_task WHERE deleted = 0 AND iso_prod_type = 1 AND tb_date >= '$audit_start_start'");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . implode(',', $eids) . ")";
}
//计划开始时间 止
if ($audit_start_end) {
    $eids = $db->get_col("SELECT eid FROM sp_task WHERE deleted = 0 AND iso_prod_type = 1 AND tb_date <= '$audit_start_end'");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . implode(',', $eids) . ")";
}
//计划结束时间 起
if ($audit_end_start) {
    $eids = $db->get_col("SELECT eid FROM sp_task WHERE deleted = 0 AND iso_prod_type = 1 AND te_date >= '$audit_end_start'");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . implode(',', $eids) . ")";
}
//计划结束时间 止
if ($audit_end_end) {
    $eids = $db->get_col("SELECT eid FROM sp_task WHERE deleted = 0 AND iso_prod_type = 1 AND te_date <= '$audit_end_end'");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . implode(',', $eids) . ")";
}

//审核开始时间 起
if ($p_start_start) {
    $eids = $db->get_col("SELECT eid FROM sp_task WHERE deleted = 0 AND iso_prod_type = 1 AND tb_date >= '$p_start_start'");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . implode(',', $eids) . ")";
}
//审核开始时间 止
if ($p_start_end) {
    $eids = $db->get_col("SELECT eid FROM sp_task WHERE deleted = 0 AND iso_prod_type = 1 AND tb_date >= '$p_start_end'");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . implode(',', $eids) . ")";
}
//审核结束时间 起
if ($p_end_start) {
    $eids = $db->get_col("SELECT eid FROM sp_task WHERE deleted = 0 AND iso_prod_type = 1 AND te_date >= '$p_end_start'");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . implode(',', $eids) . ")";
}
//审核结束时间 止
if ($p_end_end) {
    $eids = $db->get_col("SELECT eid FROM sp_task WHERE deleted = 0 AND iso_prod_type = 1 AND te_date >= '$p_end_end'");
    array_push($eids, -1);
    $where .= " AND cti.ep_prod_id IN (" . implode(',', $eids) . ")";
}
//评定时间 起
if ($assess_date_start) {
    $cti_ids = $db->get_col("SELECT eid FROM sp_project WHERE deleted = 0 AND iso_prod_type = 1 AND sp_date >= '$assess_date_start'");
    array_push($cti_ids, -1);
    $where .= " AND cti.cti_id IN (" . implode(',', $cti_ids) . ")";
}
//评定时间 止
if ($assess_date_end) {
    $cti_ids = $db->get_col("SELECT eid FROM sp_project WHERE deleted = 0 AND iso_prod_type = 1 AND sp_date <= '$assess_date_end'");
    array_push($cti_ids, -1);
    $where .= " AND cti.cti_id IN (" . implode(',', $cti_ids) . ")";
}
$where .= " AND cti.deleted = '0' AND cti.iso_prod_type = 1";

$fields .= "cti.*,e.ep_name,e.ctfrom,c.e_date,c.status cert_status,c.certno";

/* 关联表 */
$join .= " LEFT JOIN sp_enterprises e ON e.eid = cti.eid";
$join .= " LEFT JOIN sp_certificate c ON c.cti_id = cti.cti_id";

if (!$export) {
    $total = $db->get_var("SELECT COUNT(*) FROM sp_contract_item cti $join  WHERE 1 $where");
    $pages = numfpage($total, 20, $REQUEST_URI);
}
$projects = array();
$query    = $db->query("SELECT $fields FROM sp_contract_item cti $join WHERE 1 $where ORDER BY cti.cti_id desc $pages[limit]");
$sql      = $db->sql;
while ($rt = $db->fetch_array($query)) {
    $p_info = $db->get_row("SELECT is_samp,is_check,tid,test_id,is_sample,is_qualified,redata_status,is_finance,pd_type ,assess_date FROM `sp_project` WHERE `cti_id` = '$rt[cti_id]' AND `iso_prod_type` = '1' AND `deleted` = '0' order by id desc limit 1 ");
    if ($p_info) {
        $rt = array_merge($rt, $p_info);
    }
    $task_info = $db->get_row("SELECT tb_date,te_date,tb_date,te_date,is_finish,check_result,task_status FROM `sp_task` WHERE `id` = '$rt[tid]'");
    if ($task_info) {
        $rt = array_merge($rt, $task_info);
    }
    
    $rt['tb_date']     = mysql2date('Y-m-d', $rt['tb_date']);
    $rt['te_date']     = mysql2date('Y-m-d', $rt['te_date']);
    $rt['tb_date']     = mysql2date('Y-m-d', $rt['tb_date']);
    $rt['te_date']     = mysql2date('Y-m-d', $rt['te_date']);
    // $rt['status_V'] = $project_status[$rt['status']];
    // $cer = $db->get_row("select status,e_date from sp_certificate where cti_id='{$rt[cti_id]}' order by e_date desc");
    $rt['cert_status'] = $cert_status_array[$rt['cert_status']];
    // $rt['e_date'] = $cer[e_date];
    $rt['ep_manu']     = $db->get_var("select ep_name from sp_enterprises where eid=$rt[ep_manu_id]");
    $ep_prod           = $db->get_row("select ep_name,audit_type,fac_code from sp_enterprises where eid=$rt[ep_prod_id]");
    $rt['ep_prod']     = $ep_prod[ep_name];
    
    $rt['audit_type_V'] = $audit_type_array[$ep_prod['audit_type']]['name'];
    
    if ($rt[is_samp])
        $rt['test_status'] = $test_status_array[$rt['is_qualified']];
    else
        $rt['test_status'] = "";
    $rt['pd_type']           = $pd_type_array[$rt['pd_type']];
    $rt['check_result']      = $check_result_array[$rt['check_result']]['name'];
    $projects[$rt['cti_id']] = chk_arr($rt);
}


if (!$export) {
    require('view/list_audit_project.htm');
} else {
    ob_start();
    require('xls/list_project.htm');
    $data = ob_get_contents();
    ob_end_clean();
    
    export_xls('项目查询列表', $data);
}
?>