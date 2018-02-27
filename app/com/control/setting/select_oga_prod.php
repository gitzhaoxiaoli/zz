<?php
!defined('IN_SUPU') && exit('Forbidden');
//选择合同来源
if ($_GET['name']) {
    $where .= (' AND name LIKE \'%' . str_replace('%', '\\%', $_GET['name'])) . '%\'';
}
if ($_GET['note']) {
    $where .= (' AND note LIKE \'%' . str_replace('%', '\\%', $_GET['note'])) . '%\'';
}
//映射
$map = array(
    'p01.01' => '11',
    'p01.02' => '13',
    'p01.03' => '12',
    'p02.01' => '14',
    'p02.02' => '16',
    'p02.03' => '15',
    'p03.01' => '17',
    
    
    '01.01.01' => '22',
    '01.01.02' => '21',
   // '01.01.02' => '18',
    '01.01.03' => '23',
    '01.02.01' => '20',
    '01.02.02' => '18',
    '01.02.03' => '18',
    '01.02.04' => '19',
    ' ' => '0'
);
 
$where .= " AND type2=" . $map[$_GET['prod_id']];
$prods = load('set')->get_set_datas('settings_oga_prod', $where,'*','code asc,');
 
tpl();
 