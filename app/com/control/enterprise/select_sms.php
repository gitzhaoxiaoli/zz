<?php
!defined('IN_SUPU') && exit('Forbidden');
if($_GET['tid']){
 $tid = $_GET['tid'];//3c计划评审中点击快递补充时获得task的id
}

$where = "  AND deleted='0' ";
if ($_GET['sms_no']) {
    $where .= " AND sms_no LIKE '%" . str_replace('%', '\%', trim($_GET['sms_no'])) . "%'";
}

if ($_GET['sms_name']) {
    $where .= " AND sms_name = '".$_GET['sms_name']."'";
}

if ($_GET['accept_date']) {
    $where .= " AND accept_date = '".$_GET['accept_date']."'";
}

$total = $db->get_var(" select COUNT(*) from sp_express where 1 $where");
$pages = numfpage($total, '15');
$list_datas = $db->get_results(" select * from sp_express WHERE 1 $where $pages[limit]");
tpl();
