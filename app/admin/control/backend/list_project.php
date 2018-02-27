<?php
!defined('IN_SUPU') && exit('Forbidden');
// 修改项目
extract($_GET);

if ($ep_name = trim($ep_name)) {
	$where .= " AND e.ep_name LIKE '%$ep_name%'";
}

if ($ct_code = trim($ct_code)) {
	$where .= " AND p.ct_code LIKE '%$ct_code%'";
}

if ($cti_code = trim($cti_code)) {
	$where .= " AND p.cti_code LIKE '%$cti_code%'";
}

if ($audit_type) {
	$where .= " AND p.audit_type = '$audit_type'";
}

if ($iso) {
	$where .= " AND p.iso = '$iso'";
}

if ($audit_ver) {
	$where .= " AND p.audit_ver = '$audit_ver'";
}


$where .= " AND e.eid = p.eid AND p.deleted = 0";
if (!$export) {
    $total = $db->get_var("SELECT COUNT(*) FROM sp_project p , sp_enterprises e WHERE 1 $where");
    $pages = numfpage($total);
}
$sql      = "SELECT p.*,e.ep_name FROM sp_project p , sp_enterprises e WHERE 1 $where ORDER BY p.id DESC $pages[limit]";
$projects = array();
$query    = $db->query($sql);
while ($rt = $db->fetch_array($query)) {
    $rt['audit_ver']  = f_audit_ver($rt['audit_ver']);
    $rt['audit_type'] = f_audit_type($rt['audit_type']);
    $projects[$rt['id']] = chk_arr($rt);
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