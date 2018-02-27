<?php
!defined('IN_SUPU') && exit('Forbidden');
$type      = $_GET['type'];
//计算规则类型： 规则还是细则 
//规则上传文件路径
// $rule_type = explode('_', $type);
// if ($rule_type['1'] == 'index') {
    // $rule_file_dir = get_conf('prod_rule_index_url');
	// $set_type='规则';
// } else {
    // $rule_file_dir = get_conf('prod_rule_detail_url');
	// $set_type='细则';
// }
if ($_POST) {
    //添加配置内容
    if ($_POST['new']['name']) {
        $_POST['new']['type'] = $type;
        $id                   = $db->insert('settings_prod_rule', $_POST['new']);
    }
    //更新配置内容
    unset($_POST['new']);
    if ($_POST['old']) {
        foreach ($_POST['old'] as $k => $v) {
            //规则对应的文件
            $rule_file      = $_FILES['old']['name'][$k];
            $rule_file_name = explode('.', $rule_file['file_name']);
            $save_name      = $rule_file_dir . $k . '.' . $rule_file_name['1'];
            if (move_uploaded_file($_FILES['old']['tmp_name'][$k]['file_name'], $save_name)) {
                $v['ext'] = $rule_file_name['1'];
            }
			
            $db->update('settings_prod_rule', $v, array(
                'id' => $k
            ));
        }
    }
    showmsg('success', 'success', "?m=com&c=setting&a=prod_rule_list&type=$_GET[type]");
}
//加载模型
$set           = load('set');
$set->tbl_name = 'prod_rule'; //初始化操作表
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
if ($_GET['msg']) {
    $where .= " AND msg like '%$_GET[msg]%'";
}
$where .= " AND type='$type'";
$total = $set->count_set($where);
$pages = numfpage($total);
//读取配置内容
$resdb=$db->get_results("select * from sp_settings_prod_rule WHERE 1  $where AND deleted='0'  $pages[limit]",'id');

tpl();
