<?php
!defined('IN_SUPU') && exit('Forbidden');
//页面注释： 添加和编辑同用一个模板 edit.htm
//echo f_checkbox('class');

//require_once DATA_DIR.'cache/class.cache.php'; //添加课程

//品牌下拉列表

$brand_list=$db->get_results("SELECT * FROM sp_ot_basedata WHERE data_for='4'");
$dealerId=$_GET['dealerId'];
if($_GET['contractId']){//显示编辑信息
 	$contractInfo=$db->get_row("select * from sp_ot_contract where id=$_GET[contractId]");
	$contractInfo['class_arr']=explode('；',$contractInfo['class_arr']);
	if($contractInfo[leibie]!=null){
		$check=array();
		if($contractInfo[leibie]==0)
			$check[0]="checked";
		if($contractInfo[leibie]==1)
			$check[1]="checked";
	}
	$dealerId=$contractInfo[brand_id];
}

$dealerInfo=$db->get_row("select * from sp_ot_basedata where id='$dealerId'");

$currency_select=f_select('currency',$contractInfo[currency]);//注册资本币种
//显示文档

$archives=$db->get_results("select * from sp_attachments WHERE 1 AND ct_id='$_GET[contractId]' and data_for=4");



if($_GET['contractId'] and $_POST){//编辑经销商信息
	foreach($_POST as $k=>$_res){
		if($k=='ftype' || $k=='archive' || $k=='description') continue;
		$val[$k]=$_res;

	}
	$db->update('ot_contract',$val,array('id'=>$_GET['contractId']));
	$_val=$db->find_one('ot_contract',array('id'=>$_GET['contractId']));
	//处理上传

    if($_FILES[archive][name]) {
        $upload = load('upload');
        $upload->maxSize = get_option('uploadSize');
        $upload->allowExts = get_option('uploadExts');
        $upload->savePath = get_option('upload_ep_dir') . date('Ymd') . '\\';


        if (!$upload->upload()) {
            // 上传错误提示错误信息

            showmsg($upload->getErrorMsg(), 'error', "?c=in_auditor&a=contract_edit&dealerId=$_val[brand_id]&contractId=$_GET[contractId]");
            exit;
        } else {
            // 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            $attach = load('attachment');
            foreach ($info as $key => $value) {
                $new_attach = array(
                    'eid' => $_val[brand_id],
                    'ct_id' => $_GET['contractId'],
                    'name' => $value['name'],
                    'data_for' => '4',
                    //'ctfrom'		=> $ct_info['ctfrom'],
                    'ext' => $value['extension'],
                    'size' => filesize($value['savepath'] . $value['savename']),
                    'filename' => date('Ymd') . '/' . $value['savename'],
                    'ftype' => $filename2fd[$value['name']]['ftype'],
                    'description' => $filename2fd[$value['name']]['description'],
                );
                $id = $attach->add($new_attach);
                // 日志
                do {
                    log_add($_GET[dealerId], 0, "[说明:文档上传]" . "<内审员培训-培训登记:" . $_POST['contract_no'] . ">", NULL, serialize($new_attach));
                } while (false);
            }
        }
    }

	$REQUEST_URI='?c=in_auditor&a=contract_edit&contractId='.$_GET['contractId'];

	 showmsg( 'success', 'success', $REQUEST_URI );

}else if($_POST){ //如果提交表单

	foreach($_POST as $k=>$_res){
		if($k=='ftype' || $k=='archive' || $k=='description') continue;
		$val[$k]=$_res;

	}


	$val['brand_id']=$_GET['dealerId']; //客户ID
	//先判断该合同号是否存在，此操作只是防止2次提交
	$_row=$db->find_one("ot_contract"," AND contract_no='$_POST[contract_no]'");
	if($_row){
		$db->update("ot_contract",$val,array("id"=>$_row[id]));
		$rid=$_row[id];
	}
	else
		$rid=$db->insert('ot_contract',$val);
        //echo $rid;
    //exit;
	unset($_row,$val);
	if($rid){ //是否插入成功
		//处理上传

			$upload = load('upload');
			$upload->maxSize  = get_option('uploadSize');
			$upload->allowExts = get_option('uploadExts');
			$upload->savePath = get_option('upload_ep_dir') . date('Ymd').'\\';

			if(!$upload->upload()) {
				// 上传错误提示错误信息

				showmsg($upload->getErrorMsg(), 'error',"?c=in_auditor&a=contract_edit&dealerId=$_GET[dealerId]&contractId=$rid");exit;
			}else{
				// 上传成功 获取上传文件信息
				$info   = $upload->getUploadFileInfo();
				$attach	= load( 'attachment' );
				foreach($info as $key=>$value) {
					$new_attach = array(
						'eid'			=> $_GET[dealerId],
						'ct_id'			=> $rid,
						//'name'			=> $value['name'],
						//'ctfrom'		=> $ct_info['ctfrom'],
						'ext'			=> $value['extension'],
						'size'			=> filesize( $value['savepath'] . $value['savename'] ),
						'filename'		=> date('Ymd').'/'.$value['savename'],
						'ftype'			=> $filename2fd[$value['name']]['ftype'],
						'description'	=> $filename2fd[$value['name']]['description'],
					);
					$id = $attach->add( $new_attach );
					// 日志
					do {
						log_add($_GET[dealerId], 0, "[说明:文档上传]"."<内审员培训-培训登记:".$_POST['contract_no'].">", NULL, serialize($new_attach));
					}while(false);
				}
			}

		$REQUEST_URI='?c=in_auditor&a=contract_list';
		showmsg( 'success', 'success', $REQUEST_URI );
 	}
}else{//显示添加页面
    tpl('in_auditor/contract_edit');
}