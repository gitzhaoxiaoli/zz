<?php
!defined('IN_SUPU') && exit('Forbidden');
// 修改项目
extract($_GET);

if ($ep_name = trim($ep_name)) {
	$where .= " AND e.ep_name LIKE '%$ep_name%'";
}

if ($ct_code = trim($ct_code)) {
	$where .= " AND t.ct_code LIKE '%$ct_code%'";
}

if ($cti_code = trim($cti_code)) {
	$where .= " AND t.cti_code LIKE '%$cti_code%'";
}

if ($audit_type) {
	$where .= " AND t.audit_type = '$audit_type'";
}

if ($iso) {
	$where .= " AND t.iso = '$iso'";
}

if ($audit_ver) {
	$where .= " AND t.audit_ver = '$audit_ver'";
}

if ($tb_date_start = trim($tb_date_start)) {
    $where .= " AND t.tb_date >= '$tb_date_start'";
}

if ($tb_date_end = trim($tb_date_end)) {
    $where .= " AND t.tb_date <= '$tb_date_end 23:00:00'";
}

if ($te_date_start = trim($te_date_start)) {
    $where .= " AND t.te_date >= '$te_date_start'";
}

if ($te_date_end = trim($te_date_end)) {
    $where .= " AND t.te_date <= '$te_date_end 23:00:00'";
}


$where .= " AND e.eid = t.eid AND t.deleted = 0";
if (!$export) {
    $total = $db->get_var("SELECT COUNT(*) FROM sp_task t , sp_enterprises e WHERE 1 $where");
    $pages = numfpage($total);
}
$sql      = "SELECT t.*,e.ep_name FROM sp_task t , sp_enterprises e WHERE 1 $where ORDER BY t.id DESC $pages[limit]";
$projects = array();
$query    = $db->query($sql);
while ($rt = $db->fetch_array($query)) {
    $p_info = $db->get_row("SELECT audit_ver,audit_type,ct_code,cti_code FROM sp_project WHERE tid = $rt[id] AND deleted = 0 LIMIT 1");
    if ($p_info) {
        $rt += $p_info;
    }
        

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