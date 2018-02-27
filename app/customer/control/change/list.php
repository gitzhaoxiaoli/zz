<?php
/*
 * 
 */


!defined('IN_SUPU') && exit('Forbidden');
$status_array = array("未审批","通过","未通过");
extract($_GET);
if ($ep_name = trim($ep_name)) {
    
    $where .= " and c.cert_name like '%$ep_name%'";
}
if ($manu_name = trim($manu_name)) {
    $where .= " and c.manu_name like '%$manu_name%'";
}
if ($prod_name = trim($prod_name)) {
    $where .= " and c.pro_name like '%$pro_name%'";
}
if ($cti_name = trim($cti_name)) {
    $where .= " and cti.prod_id like  '%$cti_name%'";
}
if ($name = trim($name)) {
    $where .= " and cti.prod_name_chinese like  '%$name%'  ";
}
$seid=$_SESSION[userinfo][eid];
$where.=" and c.eid =$seid ";
$total=$db->get_var("select COUNT(*) from sp_change_app cp LEFT JOIN  sp_certificate c ON c.id = cp.zsid where 1 $where");
$pages = numfpage($total,20);


$sql   = "select cp.*,c.certno,cti.status c_status from sp_change_app cp LEFT JOIN  sp_certificate c ON c.id = cp.zsid LEFT JOIN sp_contract_item cti ON cti.cti_id = cp.cti_id where 1 $where $pages[limit]";
$query = $db->query($sql);
$array = array();
while ($row = $db->fetch_array($query)) {
    $row[status_V] = $status_array[$row[c_status]];
    $array[$row['id']] = $row;
}


//p($pages);
//tpl('contract/list');


    if( !$export ){
        tpl();
    } else { //导出excel表格
        ob_start();
        tpl( 'xls/contract_list' );
        $data = ob_get_contents();
        ob_end_clean();

        export_xls( '查看申请', $data );
    }
?>