<?php
$uid=getgp("uid");
if($_POST){
	if($_FILES[archive][name]){
  	$upload = load('upload');
	$upload->savePath = get_option('upload_hr_dir') . date('Ymd')."\\";
	$upload->upload();
	$info = $upload->getUploadFileInfo();
	$attach = load('attachment');
			$attach->table='hr_archives';
			foreach ($info as $key => $value) {
				$new_attach = array(
					'ftype' => $ftype,
					'uid' => $_GET['uid'],
					'name' =>$value['name'],
					'ext' => $value['extension'],
					'size' => filesize($value['savepath'] . $value['savename']) ,
					'filename' => date('Ymd').'/'.$value['savename'],
				);
				$id = $attach->add($new_attach);
			}
	$_POST['aid']=$id;
	}
	if($_GET['expId']==''){ //
		$db->insert( 'hr_experience', $_POST );
	}else{ //
		$db->update( "hr_experience", $_POST ,array("id"=>$_GET['expId']));
		
	}
	$REQUEST_URI = '?c=hr&a=credit_edit&uid='.$_GET['uid'];
	showmsg('success', 'success', $REQUEST_URI);
}elseif($_GET[type]=='del' and $id=getgp("cid")){
	$exp->del($id);
	$REQUEST_URI = '?c=hr&a=credit_edit&uid='.$_GET['uid'];
	showmsg('success', 'success', $REQUEST_URI);
}else{
require('data/cache/credit.cache.php' );
$credit_select="";
foreach($credit_array as $k=>$item){
	$credit_select.="<option value=\"$k\" i=$item[num]>$item[name]</option>";
	
	
}
$c_tip_msg = '添加信用档案';
if ($_GET['cid']) { //要修改的信用档案
        $c_tip_msg = '编辑信用档案';
        $cExpInfo = $exp->get($_GET['cid']);
		$credit_select=str_replace("value=\"$cExpInfo[area]\"","value=\"$cExpInfo[area]\" selected",$credit_select);
    }
$c_tip_msg.="(".f_username($uid).")";
$clist=$db->get_results("SELECT * FROM sp_hr_experience he where deleted='0' AND add_hr_id='$uid' and type='c' order by s_date desc");
$credit_select=str_replace("value=\"$cExpInfo[area]\"","value=\"$cExpInfo[area]\" selected",$credit_select);
tpl();
}