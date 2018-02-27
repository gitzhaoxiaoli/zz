<?php
!defined('IN_SUPU') && exit('Forbidden');
//产品查询列表
$fields            = $join = $where = $item_join = $item_where = $page_str = '';
$contracts = array();
extract($_GET);
if($cti_code){
    $where .= "AND cti_code = '$cti_code'";
}

$total = $db->find_num("contract_item","$where and iso_prod_type = 1 and deleted = 0");
$pages = numfpage($total, 15);

$sql = "SELECT * FROM sp_contract_item WHERE 1 $where AND iso_prod_type = 1 and deleted = 0 order by cti_id desc ";
$query = $db->query($sql);
while($rt = $db->fetch_array($query)){
    $rt['ep_name'] = $db->get_var("select ep_name from sp_enterprises where eid=$rt[eid]");
    $rt['ep_manu'] = $db->get_var("select ep_name from sp_enterprises where eid=$rt[ep_manu_id]");
    $rt['ep_prod'] = $db->get_var("select ep_name from sp_enterprises where eid=$rt[ep_prod_id]");
    $contracts[] =$rt;
}


if (!$export) {
    tpl('contract/list');
} else {
    ob_start();
    tpl('xls/list_contract');
    $data = ob_get_contents();
    ob_end_clean();
    export_xls(${'status_' . $status} . '_合同列表', $data);
}