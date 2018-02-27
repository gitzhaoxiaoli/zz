<?php
!defined('IN_SUPU') && exit('Forbidden');
//审核资料
$tid              = (int) getgp('tid');
$cti_id            = (int) getgp('cti_id');
//@HBJ 2013-09-16 重写全部的上传部分，采用upload上传
$upload           = load('upload');
$upload->savePath = get_option('upload_ep_dir') . date('Ymd') . '\\';
$upload->allowExts = get_option('uploadExts');
$filename2fd      = array();
foreach ($_FILES['archive']['name'] as $key => $value) {
        $filename2fd[$value] = array(
            'sort' => $_POST['sort'][$key]
        );
    
}
if (!$upload->upload()) {
    // 上传错误提示错误信息
    showmsg($upload->getErrorMsg(), 'error');
    exit;
} else {
    // 上传成功 获取上传文件信息
    $info = $upload->getUploadFileInfo();
    
    $attach = load('attachment');
    $task = $db->find_one('task',array('id' => $tid),'eid,ctfrom');
    foreach ($info as $key => $value) {
        $ftype = '3001';
        $new_attach = array(
            'eid' => $task[eid],
            'tid' => $tid,
            'cti_id' => $cti_id,
            'name' => $value['name'],
            'ctfrom' => $task['ctfrom'],
            'ext' => $value['extension'],
            'size' => filesize($value['savepath'] . $value['savename']),
            'filename' => date('Ymd') . '/' . $value['savename'],
            'ftype' => $ftype,
            'description' => $filename2fd[$value['name']]['description'],
            'sort' => $filename2fd[$value['name']]['sort']
        );
        $id         = $attach->add($new_attach);
    }
}
$REQUEST_URI = "?m=product&c=auditor&a=task_edit&tid=$tid";
showmsg('success', 'success', $REQUEST_URI);
