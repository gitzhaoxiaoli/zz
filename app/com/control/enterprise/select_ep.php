<?php
!defined('IN_SUPU') && exit('Forbidden');
//选择生产者或者生产企业
$where = "  AND deleted='0' ";
if ($_GET['ep_name']) {
    $where .= " AND ep_name LIKE '%" . str_replace('%', '\%', $_GET['ep_name']) . "%'";
}

if ($_GET['code']) {
    $where .= " AND code LIKE '%" . str_replace('%', '\%', $_GET['code']) . "%'";
}
$total = $db->get_var(" select COUNT(*) from sp_enterprises where 1 $where");
$pages = numfpage($total, '10');
$list_datas = $db->get_results(" select * from sp_enterprises WHERE 1 $where $pages[limit]");
tpl();
