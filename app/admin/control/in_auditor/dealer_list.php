<?php
!defined('IN_SUPU') && exit('Forbidden');
$where = NULL;
$name=$_GET['name'];
if($name) {
    $where .= " AND name LIKE '%".trim($name)."%' ";
}

$where .= "AND data_for='4' AND deleted='0'";
$total=$db->get_var("SELECT COUNT(*) total FROM sp_ot_basedata WHERE 1 $where ");
$pages = numfpage($total);
$dealer_list=$db->get_results("SELECT * FROM sp_ot_basedata where 1 $where $pages[limit]");
tpl();