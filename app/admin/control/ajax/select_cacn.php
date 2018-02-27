<?php
!defined('IN_SUPU') && exit('Forbidden');

$where="";
$code = trim(getgp('code'));
$name = trim(getgp('name'));

if($name){
    $where .= "AND name LIKE '%$name%'";
}

if($code){
    $where .= "AND code = '$code'";
}

$total = $db->find_num("cnca_list",$where);
$pages = numfpage( $total,10 );

$sql = "SELECT * FROM `sp_cnca_list` WHERE 1 $where $pages[limit]";
$query = $db->query($sql);
$cnca_list = array();
while($rt = $db->fetch_array($query)){
    $cnca_list[] = $rt;
}
tpl();