<?php
/*
!defined('IN_SUPU') && exit('Forbidden');

//@HBJ 2013-09-16 重写全部的上传部分，采用upload上传类
    $upload =load('upload');$upload->savePath = get_option('upload_pro_dir') . date('Ymd') . '\\';
    $filename2fd = array();
	$is_qualified_arr = array();
    foreach ($_FILES['product_files']['name'] as $key => $value) {
        if (!empty($value)) {
        	//echo 'aaa';die;
            $filename2fd[$value] = array(
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
		$ep_prod_id =(int)getgp('ep_prod_id');
		$tid =(int)getgp('tid');
		$test_id =(int)getgp('test_id');
        $attach = load('attachment');
		$attach->table='attachments';
		$preg = "/\?.+a=\w+/";
		$page = pregStr($preg,$_SERVER[REQUEST_URI]);
        foreach ($info as $key => $value) {
            $new_attach = array(
                'tid' => $tid,
                'test_id' => $test_id,
                'ep_prod_id' => $ep_prod_id,
                'name' => $value['name'],
                'ext' => $value['extension'],
                'size' => filesize($value['savepath'] . $value['savename']) ,
                'filename' => date('Ymd').'/'.$value['savename'],
                'description' => $filename2fd[$value['name']]['description'],
				'iso_prod_type'=> 1 ,
				'page' => $page,
            );
            p($new_attach);die;
            $id = $attach->add($new_attach);
        }
    };
    echo "<script>alert('SUCCESS');window.history.back();</script>";
	
	
	
	?>