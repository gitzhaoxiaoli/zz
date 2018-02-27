<?php
!defined('IN_SUPU') && exit('Forbidden');
//审核资料
$file_arr = array(
	"BG-20"=>"审核计划",
	"BG-21"=>"首末次会议签到表",
	"BG-22"=>"现场审核检查表",
	"BG-23"=>"管理体系文件审查报告",
	"BG-24"=>"第一阶段审核问题清单",
	"BG-25"=>"第一阶段审核报告",
	"BG-26"=>"不符合报告",
);
$tid = (int)getgp('tid');
$ct_id = (int)getgp('ct_id');
 //@HBJ 2013-09-16 重写全部的上传部分，采用upload上传
    $upload = load('upload');
    $upload->savePath = get_option('upload_ep_dir') . date('Ymd') . '\\';
    $upload->allowExts = get_option('uploadExts');
    $upload->maxSize = get_option('uploadSize');
    $filename2fd = array();
    // exit(p($_FILES));
	foreach ($_FILES['archive']['name'] as $key => $value) {
        if (!empty($value)) {
            $_FILES['archive']['name'][$key] =$value;
			$filename2fd[$value] = array(
                'ftype' => $_POST['ftype'][$key],
                'description' => $_POST['description'][$key],
                'sort' => $_POST['sort'][$key],
            );
        }
    }
    if (!$upload->upload()) {
        // 上传错误提示错误信息
		$tid = (int)getgp('tid');
		$REQUEST_URI = "?c=auditor&a=task_edit&tid=$tid&ct_id=$ct_id";
        showmsg($upload->getErrorMsg() , 'error');
        exit;
    } else {
        // 上传成功 获取上传文件信息
        $info = $upload->getUploadFileInfo();
        $eid = (int)getgp('eid');
        $attach = load('attachment');
        $e_row = $db->get_row("SELECT * FROM sp_enterprises WHERE eid = '$eid'");
        foreach ($info as $key => $value) {
			$ftype='3000';
            $new_attach = array(
                'eid' => $eid,
                'tid' => $tid,
				'ct_id' => $ct_id,
                'name' => $value['name'],
                'ctfrom' => $e_row['ctfrom'],
                'ext' => $value['extension'],
                'size' => filesize($value['savepath'] . $value['savename']) ,
                'filename' => date('Ymd') . '/' . $value['savename'],
                'ftype' => $filename2fd[$value['name']]['ftype'],
                'description' => $filename2fd[$value['name']]['description'],
                'sort' => $filename2fd[$value['name']]['sort'],
            );
            $id = $attach->add($new_attach);
        }
    }
    $REQUEST_URI = "?c=auditor&a=task_edit&tid=$tid&ct_id=$ct_id";
    showmsg('success', 'success', $REQUEST_URI);
