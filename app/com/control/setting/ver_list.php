<?php
!defined('IN_SUPU') && exit('Forbidden');
//标准文件保存路径
$prod_ver_dir = get_option('prod_ver_url');
if ($_POST) {
	// $load=load('upload');
	// $load->savePath = $prod_ver_dir;
    // $load->upload();
    // $info=$load->getUploadFileInfo();
	//showmsg($load->getErrorMsg(), 'error',"");exit;
    //添加配置内容
    if ($_POST['new']['name']) {
        $add_data = $_POST['new'];
        $add_data['type'] = $_GET['type'];
        $db->insert('settings_ver', $add_data);
    }
    //更新配置内容
    unset($_POST['new']);
    // p($_FILES['old']);
	// exit;
    if ($_POST['old']) {
        foreach ($_POST['old'] as $k => $v) {

            //标准对应的文件 
            $rule_file_name = explode('.', $_FILES['old']['name'][$k]['file_name']);
			$save_name = $prod_ver_dir . $k . '.' . $rule_file_name['1'];
            if (move_uploaded_file($_FILES['old']['tmp_name'][$k]['file_name'], $save_name)) {
                $v['ext'] = $rule_file_name['1'];
            }
            unset($v['file_name']);
            $db->update('settings_ver', $v, array('id' => $k));
        }
    }
    //exit;  
    showmsg('success', 'success', "?m=com&c=setting&a=$_GET[a]&type=$_GET[type]");
}
//加载模型
$set = load('set');
$set->tbl_name = 'ver prod_ver'; //初始化操作表
//删除配置
if ($_GET['del']) {
    $set->del_set($_GET['del']);
    showmsg('success', 'success', "?m=com&c=setting&a=$_GET[a]&type=$_GET[type]");
}

//读取配置内容  
$where = '';
if ($_GET['prod_name']) {
    $where.=" AND prod.name like '%$_GET[prod_name]%'";
}
if ($_GET['name']) {
    $where.=" AND prod_ver.name like '%$_GET[name]%'";
}
if ($_GET['code']) {
    $where.=" AND prod_ver.code like '%$_GET[code]%'";
}
if ($_GET['msg']) {
    $where.=" AND prod_ver.msg like '%$_GET[msg]%'";
}

$where.=" AND prod_ver.deleted='0'";
$where.=" AND prod_ver.type='$_GET[type]'";


$total = $set->count_set("$where", $joins);

$pages = numfpage($total);
//读取配置内容  
$sql = " SELECT prod_ver.* FROM sp_settings_ver prod_ver $joins WHERE 1 $where order by prod_ver.vieworder desc,id desc $pages[limit]";
$resdb = $db->get_results($sql,'id');


tpl();
