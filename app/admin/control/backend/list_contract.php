<?php
!defined('IN_SUPU') && exit('Forbidden');
// 修改项目
extract($_GET);

if ($ep_name = trim($ep_name)) {
	$where .= " AND e.ep_name LIKE '%$ep_name%'";
}

if ($ct_code = trim($ct_code)) {
	$where .= " AND ct.ct_code LIKE '%$ct_code%'";
}

if ($cti_code = trim($cti_code)) {
	$where .= " AND ct.cti_code LIKE '%$cti_code%'";
}

if ($audit_type) {
	$where .= " AND ct.audit_type = '$audit_type'";
}

if ($iso) {
	$where .= " AND ct.iso = '$iso'";
}

if ($audit_ver) {
	$where .= " AND ct.audit_ver = '$audit_ver'";
}


$where .= " AND e.eid = ct.eid AND ct.deleted = 0";
if (!$export) {
    $total = $db->get_var("SELECT COUNT(*) FROM sp_contract ct , sp_enterprises e WHERE 1 $where");
    $pages = numfpage($total);
}
$sql      = "SELECT ct.*,e.ep_name FROM sp_contract ct , sp_enterprises e WHERE 1 $where ORDER BY ct.ct_id DESC $pages[limit]";
$projects = array();
$query    = $db->query($sql);
while ($rt = $db->fetch_array($query)) {
   
    $projects[$rt['ct_id']] = chk_arr($rt);
}
if (!$export) {
    tpl();
} else {
    ob_start();
    tpl('xls/list_wait_arrange');
    $data = ob_get_contents();
    ob_end_clean();
    export_xls('审核项目列表', $data);
}
?>