<?php
/*
 * 
 */


!defined('IN_SUPU') && exit('Forbidden');
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
    $where .= " and c.prod_id like  '%$cti_name%'";
}
if ($name = trim($name)) {
    $where .= " and c.prod_name_chinese like  '%$name%'  ";
}
$seid=$_SESSION[userinfo][eid];
$where.=" and c.eid='$seid'  AND c.deleted = 0 AND c.status = '01'";
$total=$db->get_var("select COUNT(*) from sp_certificate c  where 1 $where");
$pages = numfpage($total,20);


$sql   = "select c.* from sp_certificate c  where 1 $where $pages[limit]";
$query = $db->query($sql);
$array = array();
while ($row = $db->fetch_array($query)) {
    $row[status] = read_cache("certstate",$row[status]);
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