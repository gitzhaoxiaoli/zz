<?php
!defined('IN_SUPU') && exit('Forbidden');

//var_dump($_FILES);
//exit;
//@HBJ 2013-09-16 重写全部的上传部分，采用upload上传类
//exit(getgp('zz_id'));
    $upload =load('upload');
    $upload->is_must = false;
    $upload->allowExts = array('jpg','pdf');
	$upload->savePath = get_option('upload_zz_dir') . date('Ymd') . '\\';
    $filename2fd = array();
    foreach ($_FILES['zz_book']['name'] as $key => $value) {
        if (!empty($value)) {
            $filename2fd[$value] = array(
                'ftype'       => '4005',
                'description' => $_POST['description'][$key],///备注  描述
				'zz_name'     => $_POST['zz_name'][$key],
				'zz_code'     => $_POST['zz_code'][$key],
				'zz_sdate'    => $_POST['zz_sdate'][$key],
				'zz_edate'    => $_POST['zz_edate'][$key],
				'zz_fanwei'   => $_POST['zz_fanwei'][$key],
				
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
        $attach = load('pro');
		$attach->table='attachments_pro';
		if($info){
        foreach ($info as $key => $value) {
            $new_attach = array(
                'eid' => $eid,
                'name' => $value['name'],
                'ctfrom' => $value['ctfrom'],
                'ext' => $value['extension'],
                'size' => filesize($value['savepath'] . $value['savename']) ,
                'filename' => date('Ymd').'/'.$value['savename'],
                'ftype' => $filename2fd[$value['name']]['ftype'],
                'description' => $filename2fd[$value['name']]['description'],
				'zz_name'     => $filename2fd[$value['name']]['zz_name'],
				'zz_code'     => $filename2fd[$value['name']]['zz_code'],
				'zz_sdate'    => $filename2fd[$value['name']]['zz_sdate'],
				'zz_edate'    => $filename2fd[$value['name']]['zz_edate'],
				'zz_fanwei'    => $filename2fd[$value['name']]['zz_fanwei'],
				
            );
			if(getgp('zz_id')){
			$attach->edit(getgp('zz_id'),$new_attach);	
			}else{
			$id = $attach->add($new_attach);	
			}
        }
		}else{
			foreach($_POST['zz_name'] as $k=>$v){
				$new_attach = array(
                'eid' => $eid,
                'ftype' => '4005',
                'description' => $_POST['description'][$k],
				'zz_name'     => $_POST['zz_name'][$k],
				'zz_code'     => $_POST['zz_code'][$k],
				'zz_sdate'    => $_POST['zz_sdate'][$k],
				'zz_edate'    => $_POST['zz_edate'][$k],
				'zz_fanwei'    => $_POST['zz_fanwei'][$k],
				
              );
			  if(getgp('zz_id')){
			  $attach->edit(getgp('zz_id'),$new_attach);	
			  }else{
			  $id = $attach->add($new_attach);	
			  }
			}
		}
    }
    showmsg('success', 'success', "?c=enterprise&a=edit&eid={$eid}#tab-zzbook");