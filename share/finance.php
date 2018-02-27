<?php
// 财务收费列表
require "config.php";
require "../data/cache/audit_type.cache.php";
require_once (ROOT .'/data/sys_cache/region.cache.php'); //省份

$province_select = '';
if ($region_array) {
    foreach ($region_array as $code => $item) {
        if ('0000' == substr($code, 2, 4))
            $province_select .= "<option value=\"$code\">$item[name]</option>";
    }
}

require "../data/cache/audit_type.cache.php";
$audit_type_select = '';
foreach ($audit_type_array as $k => $v) {
    if (!in_array($k, array(
        "1002",
        "1003",
        "1007",
        "1008",
        "1010",
        "99"
    )))
        $audit_type_select .= "<option value=\"$k\">$v[name]</option>";
}
$REQUEST_URI = preg_replace("/&paged=\d*/","",$_SERVER[REQUEST_URI]);

extract($_GET, EXTR_SKIP);
$fileds = $join = $where = '';
if ($ep_prod = trim($ep_prod)) {
    $eid = $db->getCol("enterprises", array(
        'ep_name' => $ep_prod
    ), "eid");
    array_push($eid, -1);
    $where .= "AND ep_prod_id in (" . join(",", $eid) . ")";
}


if ($cti_code = trim($cti_code)) {
    $where .= " AND cti_code = '$cti_code'";
    
}

//合同来源限制
$len = get_ctfrom_level(current_user('ctfrom'));
if ($ctfrom && substr($ctfrom, 0, $len) == substr(current_user('ctfrom'), 0, $len)) {
    $_len = get_ctfrom_level($ctfrom);
    $len  = $_len;
} else {
    $ctfrom = current_user('ctfrom');
}
switch ($len) {
    case 2:
        $add = 1000000;
        break;
    
    case 4:
        $add = 10000;
        break;
    
    case 6:
        $add = 100;
        break;
    
    case 8:
        $add = 1;
        break;
}
$ctfrom_e      = sprintf("%08d", $ctfrom + $add);
//$where .= " AND p.ctfrom >= '$ctfrom' AND p.ctfrom < '$ctfrom_e'";
$ctfrom_select = str_replace("value=\"$ctfrom\"", "value=\"$ctfrom\" selected", $ctfrom_select);
if ($dk_date_start) {
    $where .= " AND dk_date >= '$invoice_date_start'";
}
if ($dk_date_end) {
    $where .= " AND dk_date <= '$invoice_date_end'";
}

if ($audit_type) {
    
    $where .= "AND audit_type = '$audit_type'";
    $audit_type_select = str_replace("value=\"$audit_type\"", "value=\"$audit_type\" selected", $audit_type_select);
}



$where .= " and pc.deleted = 0";
/*列表*/
if (!$export) {
    $total = $db->get_var("SELECT COUNT(*) FROM sp_project_cost pc LEFT JOIN sp_project p ON pc.pid = p.id WHERE 1 $where order by pc.id desc");
    
    $pages = numfpage($total, 20, $REQUEST_URI);
}
$sql = "SELECT pc.*,p.ep_prod_id,p.cti_code,p.audit_type FROM sp_project_cost pc LEFT JOIN sp_project p ON pc.pid = p.id WHERE 1 $where order by pc.id desc $pages[limit]";

$res = $db->query($sql);
while ($rt = $db->fetch_array($res)) {
    // $rt['ctfrom'] = f_ctfrom($rt['ctfrom']);
    if ($rt[audit_type] == '1001') {
        $rt[c_cost] = $rt[inspection];
    } else {
        $rt[j_cost] = $rt[inspection];
    }
    $rt[audit_type]   = $audit_type_array[$rt[audit_type]]['name'];
    $rt['ep_prod']    = $db->getField("enterprises", "ep_name", array("eid" => $rt[ep_prod_id]));
    $datas[$rt['id']] = chk_arr($rt);
}
if (!$export) {
    require('view/dlist.htm');
} else {
    ob_start();
    require('xls/dlist.htm');
    $data = ob_get_contents();
    ob_end_clean();
    export_xls('财务收费明细列表', $data);
}
