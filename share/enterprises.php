<?php
// 企业列表
require "config.php";
require "../data/cache/ep_level.cache.php";
require_once (ROOT .'/data/sys_cache/region.cache.php'); //省份

$province_select = '';
if ($region_array) {
    foreach ($region_array as $code => $item) {
        if ('0000' == substr($code, 2, 4))
            $province_select .= "<option value=\"$code\">$item[name]</option>";
    }
}
$REQUEST_URI = preg_replace("/&paged=\d*/","",$_SERVER[REQUEST_URI]);
$fields      = $join = $where = '';
extract($_GET, EXTR_SKIP); //获取搜索项 
//$_GET['ep_name']
/*搜索条件*/
if ($ep_name) {
    $where .= " AND e.ep_name LIKE '%" . str_replace('%', '\%', $ep_name) . "%'";
}

if ($ep_phone) {
    $where .= " AND e.ep_phone LIKE '%" . str_replace('%', '\%', $ep_phone) . "%'";
}
$ep_amount_select = '<option value="500">0——500</option>
       <option value="1000">500——1000</option>
       <option value="more">more</option>';
if ($ep_amount == "500") {
    $where .= " AND e.ep_amount<=500";
    $ep_amount_select = str_replace('value="500"', 'value="500" selected', $ep_amount_select);
}
if ($ep_amount == "1000") {
    $where .= " AND e.ep_amount>500 AND e.ep_amount<=1000";
    $ep_amount_select = str_replace('value="1000"', 'value="1000" selected', $ep_amount_select);
}
if ($ep_amount == "more") {
    $where .= " AND e.ep_amount>1000";
    $ep_amount_select = str_replace('value="more"', 'value="more" selected', $ep_amount_select);
}
if ($ep_level) {
    $where .= " AND e.ep_level='$ep_level'";
}
if ($areacode) {
    $pcode = substr($areacode, 0, 2) . '0000';
    $where .= " AND LEFT(e.areacode,2) = '" . substr($areacode, 0, 2) . "'";
    $province_select = str_replace("value=\"$pcode\">", "value=\"$pcode\" selected>", $province_select);
}
if ($work_code) {
    $where .= " AND e.work_code = '$work_code'";
}
if ($fac_code) {
    $where .= " AND e.fac_code = '$fac_code'";
}
if ($person) {
    $where .= " AND e.person LIKE '%" . str_replace('%', '\%', $person) . "%'";
}
if ($person_tel) {
    $where .= " AND e.person_tel LIKE '%" . str_replace('%', '\%', $person_tel) . "%'";
}
$where .= " AND e.deleted = '0'";
/*分页*/
if (!$export) {
    $total = $db->get_var("SELECT COUNT(*) FROM sp_enterprises e WHERE 1 $where");
    $pages = numfpage($total, 20, $REQUEST_URI);
}
/*列表*/
$enterprises = array();
$query       = $db->query("SELECT e.* FROM sp_enterprises e $join WHERE 1 $where ORDER BY e.create_date DESC $pages[limit]");


while ($rt = $db->fetch_array($query)) {
    $rt['province'] = substr($rt['areacode'], 0, 2) . '0000';
    $rt['province']          = $region_array[$rt['province']]['name']; //取省地址
    $rt['ep_type_V']         = $ep_type_array[$rt['ep_type']]['name'];
    $enterprises[$rt['eid']] = $rt;
}
if (!$export) {
    
    require('view/list_cus.htm');
} else { //导出企业列表
    ob_start();
    require('xls/list_cus.htm');
    $data = ob_get_contents();
    ob_end_clean();
    export_xls('客户列表', $data);
}