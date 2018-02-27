<?php
// 证书查询
require "config.php";
require "../data/cache/certstate.cache.php";
require "../data/cache/iso.cache.php";
require "../data/cache/certstate.cache.php";
$REQUEST_URI     = $_SERVER[REQUEST_URI];
require_once (ROOT .'/data/sys_cache/region.cache.php'); //省份
$province_select = '';
if ($region_array) {
    foreach ($region_array as $code => $item) {
        if ('0000' == substr($code, 2, 4))
            $province_select .= "<option value=\"$code\">$item[name]</option>";
    }
}
/*
 *证书查询列表
 */

$certstate_select = "";
if ($certstate_array) {
    
    foreach ($certstate_array as $key => $value) {
        $certstate_select .= "<option value=\"$key\">$value[name]</option>";
        
    }
}


$fields = $join = $where = '';
extract($_GET, EXTR_SKIP);
//合同来源


//企业名称
$ep_name = trim($ep_name);
if ($ep_name) {
    $where .= " AND cert.cert_name like '%$ep_name%'";
}

//生产者名称
if ($ep_manu_id = trim($ep_manu_id)) {
    $where .= " AND cert.manu_name like '%$ep_manu_id%'";
}
//生产企业名称
if ($ep_prod_id = trim($ep_prod_id)) {
    $where .= " AND cert.pro_name like '%$ep_prod_id%'";
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
    $where .= " AND cert.eid IN (" . implode(',', $_eids) . ")";
    unset($_eids, $_query, $rt, $_eids);
    
    $province_select = str_replace("value=\"$pcode\">", "value=\"$pcode\" selected>", $province_select);
}



if ($s_dates) { //注册时间 起
    $where .= " AND cert.s_date >= '$s_dates'";
}
if ($s_datee) { //注册时间 止
    $where .= " AND cert.s_date <= '$s_datee'";
}

if ($e_dates) { //到期时间 起
    $where .= " AND cert.e_date >= '$e_dates'";
}
if ($e_datee) { //到期时间 止
    $where .= " AND cert.e_date <= '$e_datee'";
}

if ($cti_code = trim($cti_code)) { //合同项目编码
    $where .= " AND cert.cti_code like '%$cti_code%'";
}

if ($iso) { //认证体系
    $where .= " AND cert.iso = '$iso'";
    if ($item['is_stop'] == 0) {
        $iso_select = str_replace("value=\"$iso\">", "value=\"$iso\" selected>", $iso_select);
    }
}

$scope = trim($scope);
if ($scope) {
    $where .= " AND cert_scope LIKE '%$scope%'";
}


if ($certno = trim($certno)) {
    $where .= " AND cert.certno like '%$certno%'";
}

if ($certstate) {
    $where .= " AND cert.status = '$certstate'";
    $certstate_select = str_replace("value=\"$certstate\">", "value=\"$certstate\" selected>", $certstate_select);
    
}
$where .= " AND cert.deleted = 0";

$where .= " AND cert.status <> ''";


$join .= " LEFT JOIN sp_enterprises  e ON e.eid = cert.eid ";
// $join .= " LEFT JOIN sp_project p ON p.id = cert.pid";

if (!$export and !$export1) {
    $total = $db->get_var("SELECT COUNT(*) FROM sp_certificate cert $join WHERE 1 $where and cert.iso_prod_type = 1");
    $pages = numfpage($total, 20, $REQUEST_URI);
}

$sql = "SELECT cert.* FROM sp_certificate cert $join WHERE 1 $where and cert.iso_prod_type = 1 ORDER BY cert.id DESC $pages[limit]";

$query = $db->query($sql);


while ($rt = $db->fetch_array($query)) {
    $rt['status'] = $certstate_array[$rt['status']]['name'];
    
    
    // 暂停时间
    $rt['time1'] = $db->get_var("SELECT cgs_date FROM sp_certificate_change WHERE zsid = '{$rt['id']}' AND cg_type = '97_01' AND status=1 and deleted=0 ORDER BY id DESC ");
    // 暂停到期
    $rt['time2'] = $db->get_var("SELECT cge_date FROM sp_certificate_change WHERE zsid = '{$rt['id']}' AND cg_type = '97_01' AND status=1 and deleted=0 ORDER BY id DESC ");
    // 撤销时间
    $rt['time3'] = $db->get_var("SELECT cgs_date FROM sp_certificate_change WHERE zsid = '{$rt['id']}' AND cg_type = '97_03' AND status=1 and deleted=0 ORDER BY id DESC ");
    // 恢复时间
    $rt['time4'] = $db->get_var("SELECT cgs_date FROM sp_certificate_change WHERE zsid = '{$rt['id']}' AND cg_type = '97_02' AND status=1 and deleted=0 ORDER BY id DESC ");
    $datas[]     = chk_arr($rt);
}
if (!$export) {
    require('view/list.htm');
} else {
    
    ob_start();
    require('xls/list.htm');
    $data = ob_get_contents();
    ob_end_clean();
    
    export_xls('证书查询', $data);
}


