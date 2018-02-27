<?php
// 人员列表
require "config.php";
require "../data/cache/audit_job.cache.php";
require "../data/cache/department.cache.php";
require_once (ROOT .'/data/sys_cache/region.cache.php'); //省份

$REQUEST_URI = preg_replace("/&paged=\d*/","",$_SERVER[REQUEST_URI]);
//下拉省份
$province_select = '';
if ($region_array) {
    foreach ($region_array as $code => $item) {
        if ('0000' == substr($code, 2, 4))
            $province_select .= "<option value=\"$code\">$item[name]</option>";
    }
}
$hire_array = array(
    1 => '在职',
    2 => '离职'
);
$fields     = $join = $where = '';
extract($_GET, EXTR_SKIP);
$is_hire = max(1, intval($is_hire));
$name    = trim($name);
if ($name) {
    $where .= " AND name like '%$name%' ";
}
$easycode = trim($easycode);
if ($easycode) {
    $where .= " AND easycode like '%$easycode%' ";
}

$ccode = trim($_GET['code']);
if ($ccode) {
    $where .= " AND code like '%" . $ccode . "%' ";
}
if ($tel) {
    $where .= " AND tel like '%" . $tel . "%' ";
}


if ($areacode) { //省份搜索
    $pcode = substr($areacode, 0, 2) . '0000';
    $where .= " AND LEFT(areacode,2) = '" . substr($areacode, 0, 2) . "'";
    $province_select = str_replace("value=\"$pcode\">", "value=\"$pcode\" selected>", $province_select);
}
if ($audit_job != '') {
    $where .= " AND audit_job = '$audit_job'";
}
$where .= " AND sp_hr.deleted = 0 AND is_hire = '1' ";
if (!$export) {
    $hire_total = array(
        1 => 0,
        2 => 0,
        3 => 0
    );
    $total = $db->get_var("SELECT COUNT(*) total FROM sp_hr $join WHERE 1 $where");
    $pages      = numfpage($total, 20, $REQUEST_URI);
}
$sql = "SELECT * FROM sp_hr $join WHERE 1 $where ORDER BY id DESC $pages[limit]";

$query = $db->query($sql);

$date = date("Y-m-d");
while ($rt = $db->fetch_array($query)) {
    
    $rt['audit_job'] = $audit_job_array[$rt['audit_job']]['name'];
    $rt['areacode'] = substr($rt['areacode'], 0, 2) . '0000';
    $rt['areacode']  = $region_array[$rt['areacode']]['name']; //取省地址
    if ($rt['sex'] == '1') {
        $rt['sex'] = '男';
    } elseif ($rt['sex'] == '2') {
        $rt['sex'] = '女';
    }
    $rt[age] = $date - $rt[birthday];
    $rt['department'] = $department_cache_array[$rt['department']]['name'];
    $rt['mail']       = $db->meta($rt['id'], 'mail', '', 'user');
    $rt[card_no] = substr($rt[card_no],0,4)."****".substr($rt[card_no],-4);
    $users[$rt['id']] = $rt;
}
if (!$export) {
    require('view/list_hr.htm');
} else {
    ob_start();
    require('xls/list_hr.htm');
    $data = ob_get_contents();
    ob_end_clean();
    export_xls($hire_array[$is_hire] . '人员', $data);
}
?>
