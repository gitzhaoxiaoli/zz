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
$dealer_lists=$db->get_results("SELECT * FROM sp_ot_basedata where 1 $where $pages[limit]");
$dealer_list = array();
foreach ( $dealer_lists as $k => $_v ) {
    $dealer_list[$k]['is_finance'] = $db->get_var("SELECT is_finance FROM sp_ot_contract WHERE brand_id = '$_v[id]' ");
    $dealer_list[] = $_v ;

}
unset($dealer_list[0]);
tpl();