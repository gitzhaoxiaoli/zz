<?php
!defined('IN_SUPU') && exit('Forbidden');

if ($_POST) {
    //添加实验室信息
    if ($_POST['new']['name']) {
        $_POST['new']['userPwd'] = md5($_POST['new']['userPwd']);
        $db->insert('settings_test_org', $_POST['new']);
    }
   
    unset($_POST['new']); 
    //修改实验室信息
    if ($_POST['old']) {
        foreach ($_POST['old'] as $k => $v) {
            if (empty($v['userPwd'])) {
                unset($v['userPwd']);
            } else {
                $v['userPwd'] = md5($v['userPwd']);
            }
            $db->update('settings_test_org', $v, array('id' => $k));
        }
    }

    showmsg('success', 'success', "?m=com&c=setting&a=$_GET[a]");
}
//实验室列表查询
//加载模型
$set = load('set');
$set->tbl_name = 'test_org'; //初始化操作表
//删除配置
if ($_GET['del']) {
    $set->del_set($_GET['del']);
    showmsg('success', 'success', "?m=com&c=setting&a=$_GET[a]");
}
$where = '';

if ($_GET['audit_ver']) {
    $where .= " AND  is_" . $_GET['audit_ver'] . "=1";
}
if ($_GET['name']) {
    $where.=" AND name like '%$_GET[name]%'";
}
if ($_GET['code']) {
    $where.=" AND code like '%$_GET[code]%'";
}
if ($_GET['person']) {
    $where.=" AND person like '%$_GET[person]%'";
}
if ($_GET['msg']) {
    $where.=" AND msg like '%$_GET[msg]%'";
}
if ($_GET['addr']) {
    $where.=" AND addr like '%$_GET[addr]%'";
}

$total = $set->count_set($where);

$pages = numfpage($total);
//读取配置内容
$resdb = $db->find_results('settings_test_org', $where, '*', " vieworder desc,", $pages[limit]);
//echo $db->sql;
tpl();
