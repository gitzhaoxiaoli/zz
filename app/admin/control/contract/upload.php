<?php
!defined('IN_SUPU') && exit('Forbidden');
/**
 * 上传修改 @zxl 2016年5月20日18:03:31
 * @var [type]
 */
$ct_id  = (int) getgp('ct_id');
$eid    = (int) getgp('eid');
$cti_id = (int) getgp('cti_id');
$pid    = (int) getgp('pid');
$tid    = (int) getgp('tid');

$ctfrom = $db->getField('enterprises', 'ctfrom', array(
    'eid' => $eid
));
if ($_POST) {
    //@HBJ 2013-09-16 重写全部的上传部分，采用upload上传类 
    $upload           = load('upload');
    $upload->savePath = get_option('upload_ep_dir') . date('Ymd') . '\\';
    $upload->allowExts = get_option('uploadExts');
    
    $filename2fd = array();
    foreach ($_FILES['archive']['name'] as $key => $value) {
        if (!empty($value)) {
            $filename2fd[$value] = array(
                'ftype' => $_POST['ftype'][$key],
                'view' => $_POST['view'][$key],
                'description' => $_POST['description'][$key]
            );
        }
    }
    if (!$upload->upload()) {
        // 上传错误提示错误信息 
        showmsg($upload->getErrorMsg(), 'error');
        exit;
    } else {
        // 上传成功 获取上传文件信息
        $info   = $upload->getUploadFileInfo();
        $attach = load('attachment');
        foreach ($info as $key => $value) {
            $new_attach = array(
                'eid' => $eid,
                'ct_id' => $ct_id,
                'cti_id' => $cti_id,
                'pid' => $pid,
                'tid' => $tid,
                'name' => $value['name'],
                'ctfrom' => $ctfrom,
                'ext' => $value['extension'],
                'size' => filesize($value['savepath'] . $value['savename']),
                'filename' => date('Ymd') . '/' . $value['savename'],
                'ftype' => $filename2fd[$value['name']]['ftype'],
                'view' => $filename2fd[$value['name']]['view'],
                'description' => $filename2fd[$value['name']]['description']
            );
            $id         = $attach->add($new_attach);
            // 日志
        }
    }
    showmsg('success', 'success', "?c=contract&a=upload&ct_id={$ct_id}");
} else {
    //合同附件上传类型

    $arctype_select = f_select('arctype');
    
    $where = " AND view = " . (int)current_user("check_auth");
   
    //已上传的文档
    $ct_archives  = array();
    $sql          = "SELECT * FROM sp_attachments  WHERE ct_id = '$ct_id' $where ORDER BY id DESC";
    $query        = $db->query($sql);
    while ($rt = $db->fetch_array($query)) {
        $rt['ftype_V']          = f_arctype($rt['ftype']);
        $ct_archives[$rt['id']] = $rt;
    }
    
    
    
    tpl();
}
?>