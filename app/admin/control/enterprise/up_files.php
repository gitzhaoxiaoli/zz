<?php
!defined('IN_SUPU') && exit('Forbidden');
set_time_limit(0); 

//@HBJ 2013-09-16 重写全部的上传部分，采用upload上传类
    $upload =load('upload');
    $upload->savePath = get_option('upload_ep_dir') . date('Ymd') . '\\';
    $filename2fd = array();
    foreach ($_FILES['archive']['name'] as $key => $value) {
        if (!empty($value)) {
            $filename2fd[$value] = array(
                'ftype' => $_POST['ftype'][$key],
                'description' => $_POST['description'][$key],
            );
        }
    }
    if (!$upload->upload()) {
        // 上传错误提示错误信息
        showmsg($upload->getErrorMsg() , 'error');
        exit;
    } else {
        // 上传成功 获取上传文件信息
        $info = $upload->getUploadFileInfo();
        $eid = (int)getgp('eid');
		
        $attach = load('attachment');
		$attach->table='attachments';
        foreach ($info as $key => $value) {
            $new_attach = array(
                'eid' => $eid,
                'name' => $value['name'],
                'ext' => $value['extension'],
                'size' => filesize($value['savepath'] . $value['savename']) ,
                'filename' => date('Ymd').'/'.$value['savename'],
                'ftype' => $filename2fd[$value['name']]['ftype'],
                'description' => $filename2fd[$value['name']]['description'],
            );
            // p($attach);die;
            $id = $attach->add($new_attach);
        }
    }
    showmsg('success', 'success', "?c=enterprise&a=edit&eid={$eid}#tab-epfiles");