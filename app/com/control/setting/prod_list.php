<?php
!defined('IN_SUPU') && exit('Forbidden');
//认证机构： 产品配置
if ($_POST) {
    //添加配置内容
    if ($_POST['new']['name']) {
        $db->insert('settings_prod_xiaolei', $_POST['new'],false);
    }
    //更新配置内容
    unset($_POST['new']);
    if ($_POST['old']) {
        foreach ($_POST['old'] as $k => $v) {
            $db->update('settings_prod_xiaolei', $v, array(
                'id' => $k
            ));
        }
    }
    showmsg('success', 'success', "?m=com&c=setting&a=$_GET[a]&type=$_GET[type]");
}
///////////////////////////显示数据/////////////////////////////////////
//加载模型
$set           = load('set');
$set->tbl_name = 'prod_xiaolei prod'; //初始化操作表
//删除配置
if ($_GET['del']) {
    $set->del_set($_GET['del']);
    showmsg('success', 'success', "?m=com&c=setting&a=$_GET[a]&type=$_GET[type]");
}
$where = '';
if ($_GET['name']) {
    $where .= " AND name like '%$_GET[name]%'";
}
if ($_GET['code']) {
    $where .= " AND code like '%$_GET[code]%'";
}
//实施规则搜索
if ($_GET['prod_rule_name']) {
    $_rules = $db->get_Col("SELECT code FROM sp_settings_prod_rule WHERE name LIKE '%" . str_replace('%', '\%', $_GET['prod_rule_name']) . "%'");
    if ($_rules) {
        $where .= " AND " . $db->sqls(array(
            'prod_rule_id' => $_rules
        ));
    } else {
        $where .= " AND id=0";
    }
}
$where .= " AND prod_type='$_GET[type]'";
$fields = '*';
//$total  = $set->count_set($where);
$total  = $db->get_var("SELECT COUNT(*) FROM sp_settings_prod_xiaolei prod WHERE 1 AND prod_type='b01001' AND prod. deleted='0'");
$pages  = numfpage($total);
$sql    = " SELECT $fields FROM sp_settings_prod_xiaolei $joins WHERE 1 $where AND deleted='0' order by vieworder desc,id desc $pages[limit] ";
$resdb  = $db->get_results($sql, 'id');
foreach ($resdb as $k => $v) {
    $resdb[$k]['prod_rule_name']        = $set->get_set_name_by_id('prod_rule', $v['prod_rule_id']);
    $resdb[$k]['prod_rule_detail_name'] = $set->get_set_name_by_id('prod_rule', $v['prod_rule_detail_id']);
}

tpl();