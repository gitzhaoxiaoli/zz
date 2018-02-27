<?php
!defined('IN_SUPU') && exit('Forbidden');
 require_once( ROOT . '/data/cache/app_change.cache.php' );//变更类别
// 变更类别
if($app_change_array){
	$changeitem_li = '';
	foreach($app_change_array as $key=>$value){
		$changeitem_li .= "<li><label><input type='checkbox' name='type[]' value='$key'  />".$key.'.'.$value['name']."</label></li>";
		
	}
} 

$id = getgp("id");
$zsid=$_GET['zsid'];

if($_POST){
	

	if($id){
		// 修改
		$change_app = $db->find_one("change_app",array("id"=>$id));
		extract($change_app);
		// $db->update("change_app",$_POST,array('id'=>$id));

	}
	// 取三者的id
	$cert = $db->find_one("certificate",array("id"=>$zsid),"eid,ep_manu_id,ep_prod_id,iso");
	extract($cert);
	 
	 // 处理三者 产品
	//委托人
	$ep_name      = $_POST['ep_name'];
	$ep_name_e    = $_POST['ep_name_e'];
	$work_code    = $_POST['work_code'];
	$nature       = $_POST['nature'];
	$statecode    = $_POST['statecode'];
	$delegate     = $_POST['delegate'];
	$ep_amount    = $_POST['ep_amount'];
	$person       = $_POST['person'];
	$person_tel   = $_POST['person_tel'];
	$ep_phone     = $_POST['ep_phone'];
	$ep_fax       = $_POST['ep_fax'];
	$person_email = $_POST['person_email'];
	$areaaddr     = $_POST['areaaddr'];
	$cta_addr     = $_POST['cta_addr'];
	$cta_addr_e   = $_POST['cta_addr_e'];
	$cta_addrcode = $_POST['cta_addrcode'];

	//生产者
	$manu_ep_name      = $_POST['manu_ep_name'];
	$manu_ep_name_e    = $_POST['manu_ep_name_e'];
	$manu_work_code    = $_POST['manu_work_code'];
	$manu_nature       = $_POST['manu_nature'];
	$manu_statecode    = $_POST['manu_statecode'];
	$manu_delegate     = $_POST['manu_delegate'];
	$manu_ep_amount    = $_POST['manu_ep_amount'];
	$manu_person       = $_POST['manu_person'];
	$manu_person_tel   = $_POST['manu_person_tel'];
	$manu_ep_phone     = $_POST['manu_ep_phone'];
	$manu_ep_fax       = $_POST['manu_ep_fax'];
	$manu_person_email = $_POST['manu_person_email'];
	$manu_areaaddr     = $_POST['manu_areaaddr'];
	$manu_cta_addr     = $_POST['manu_cta_addr'];
	$manu_cta_addr_e   = $_POST['manu_cta_addr_e'];
	$manu_cta_addrcode = $_POST['manu_cta_addrcode'];

	//生产企业
	$prod_ep_name      = $_POST['prod_ep_name'];
	$prod_ep_name_e    = $_POST['prod_ep_name_e'];
	$prod_work_code    = $_POST['prod_work_code'];
	$prod_nature       = $_POST['prod_nature'];
	$prod_statecode    = $_POST['prod_statecode'];
	$prod_delegate     = $_POST['prod_delegate'];
	$prod_ep_amount    = $_POST['prod_ep_amount'];
	$prod_person       = $_POST['prod_person'];
	$prod_person_tel   = $_POST['prod_person_tel'];
	$prod_ep_phone     = $_POST['prod_ep_phone'];
	$prod_ep_fax       = $_POST['prod_ep_fax'];
	$prod_person_email = $_POST['prod_person_email'];
	$prod_areaaddr     = $_POST['prod_areaaddr'];
	$prod_cta_addr     = $_POST['prod_cta_addr'];
	$prod_cta_addr_e   = $_POST['prod_cta_addr_e'];
	$prod_cta_addrcode = $_POST['prod_cta_addrcode'];

	//产品
	$prod_id    = $_POST['prod_id'];
	$fac_code   = $_POST['fac_code'];
	$pro_name   = $_POST['pro_name'];
	$pro_name_e = $_POST['pro_name_e'];
	$prod_sta   = $_POST['prod_sta'];
	$scope      = $_POST['pro_model'];
	$type      = $_POST['type'];

	if ($eid) {
		$db->update("enterprises", array(
			'ep_name' => $ep_name,
			'prod_addr_e' => $prod_addr_e,
			'prod_addr' => $prod_addr,
			'ep_name_e' => $ep_name_e,
			'work_code' => $work_code,
			'nature' => $nature,
			'statecode' => $statecode,
			'delegate' => $delegate,
			'ep_amount' => $ep_amount,
			'person' => $person,
			'person_tel' => $person_tel,
			'ep_phone' => $ep_phone,
			'ep_fax' => $ep_fax,
			'person_email' => $person_email,
			 'ep_mail'=>$person_email,
			'areacode' => getgp("areacode"),
			'areaaddr' => $areaaddr,
			'cta_addr' => $cta_addr,
			'cta_addr_e' => $cta_addr_e,
			'cta_addrcode' => $cta_addrcode
		   
			
			
		), "eid = '$eid'");
	} 
	
	$payment_name = getgp("payment_name");
	$payment_addr = getgp("payment_addr");
	$db->meta($eid,"name_ac",$payment_name,"enterprise");
	$db->meta($eid,"r_add",$payment_addr,"enterprise");

	if ($ep_name != $manu_ep_name) {

	if ($ep_manu_id) {
		$db->update("enterprises", array(
			'ep_name' => $manu_ep_name,
			'prod_addr_e' => $manu_prod_addr_e,
			'prod_addr' => $manu_prod_addr,
			'ep_name_e' => $manu_ep_name_e,
			'work_code' => $manu_work_code,
			
			'nature' => $manu_nature,
			'statecode' => $manu_statecode,
			'delegate' => $manu_delegate,
			'ep_amount' => $manu_ep_amount,
			'person' => $manu_person,
			'person_tel' => $manu_person_tel,
			'ep_phone' => $manu_ep_phone,
			'ep_fax' => $manu_ep_fax,
			'person_email' => $manu_person_email,
			 'ep_mail'=>$manu_person_email,
			'areaaddr' => $manu_areaaddr,
			'areacode' => getgp("manu_areacode"),
			'cta_addr' => $manu_cta_addr,
			'cta_addr_e' => $manu_cta_addr_e,
			'cta_addrcode' => $manu_cta_addrcode

			
		), "eid = '$ep_manu_id'");
	}
		
		

	}

	if ($ep_prod_id) {
		$db->update("enterprises", array(
			'ep_name' => $prod_ep_name,
			'prod_addr_e' => $prod_cta_addr_e,
			'prod_addr' => $prod_cta_addr,
			'ep_name_e' => $prod_ep_name_e,
			'work_code' => $prod_work_code,
			'nature' => $prod_nature,
			 
			'statecode' => $prod_statecode,
			'delegate' => $prod_delegate,
			'ep_amount' => $prod_ep_amount,
			'person' => $prod_person,
			'person_tel' => $prod_person_tel,
			'ep_phone' => $prod_ep_phone,
			'ep_fax' => $prod_ep_fax,
			'ep_mail'=>$prod_person_email,
			'person_email' => $prod_person_email,
			'areaaddr' => $prod_areaaddr,
			'areacode' => getgp("prod_areacode"),
			'prod_addrcode' => $prod_cta_addrcode,
			'ctfrom'	=> "01000000",
		), "eid = '$ep_prod_id'");
	} 

	$up_change = array(
			"reportno"		=> $_POST[reportno],
			"title"			=> $_POST[title],
			"app_type"		=> $_POST[app_type],
			"type"			=> serialize($_POST['type']),
			"sq_date"		=> date("Y-m-d"),
			"cg_af"			=> $_POST[cg_af],
			"cg_bf"			=> $_POST[cg_bf],
			"note"			=> $_POST[note],
			);
	$up_cti = array(
		'ep_prod_addr' => $prod_cta_addr,
		'ep_prod_addr_e' => $prod_cta_addr_e,
		'pro_areaaddr' => getgp("prod_areacode"),
		'audit_type' => $_POST[app_type],
		'total' => $prod_ep_amount,
		'prod_id' => $prod_id,
		'fac_code' => $fac_code,
		'prod_ver' => $prod_sta,
		'scope' => $scope,
		'mark' => getgp("mark"),
		'note' => getgp("note"),
		'new'	=> serialize($_POST['new']),
		'prod_name_chinese' => $pro_name,
		'prod_name_english' => $pro_name_e,
	  
		
	);
	if($id){
		// 修改
		// 更新change_app
		$db->update("change_app",$up_change,array('id'=>$id));
		// 更新contract_item
		$db->update('contract_item', $up_cti,"cti_id='$cti_id'");

	}else{
		// 新增
		$new_change = $up_change;
		$new_cti = array(
			'eid' => $eid,
			'ep_manu_id' => $ep_manu_id,
			'ep_prod_id' => $ep_prod_id,
			'iso' => $iso,
			'ctfrom' => '01000000',
			'iso_prod_type' => 1,
			'is_app' => 1,
			'app_type' => 1,
			'cti_code' => $cti_code,
		);
		$new_cti = array_merge($new_cti,$up_cti);

		if($iso == "B05"){
			$_type = "P";
			$new_cti[audit_ver] = "B0599";
		}else{
			$_type = "C";
			$new_cti[audit_ver] = "B010101";
		}
		$year = date("Y");
		$sort = $db->get_var("SELECT max(sort)	FROM `sp_contract_item` WHERE `deleted` = '0' AND `iso_prod_type` = '1' AND `iso` = '$iso' ");
		$sort ++;
		$new_cti['sort'] = $sort;
		$sort = sprintf("%06d", $sort);
		$new_cti[cti_code] = $new_change[changeno] = $year . "-".$_type."-" . $prod_id . "-" . $sort;
		$new_change[cti_id] = $cti_id = $db->insert('contract_item',$new_cti);
		$new_change[create_uid] = current_user("eid");
		$new_change[create_user] = current_user("ep_name");
		$new_change[zsid] = $zsid;
		// exit(p($_POST));
		$db->insert("change_app",$new_change);
	
	
	$db->insert("progress",array(	"cti_id" => $cti_id,
									"audit_type" => "1001",
									"step1"		=> date("Y-m-d H:i:s"),
									"status"	=> "1",
									));
	}
	$upload = load('upload');
	$upload->savePath = get_option('upload_pro_dir') . date('Ymd') . '\\';
	$filename2fd = array();
	if ($upload->upload()){
		// 上传成功 获取上传文件信息
		$info = $upload->getUploadFileInfo();
		$attach = load('attachment');
		foreach ($info as $key => $value) {
			$new_attach = array(
			'eid' => $ep_prod_id,
			'cti_id' => $cti_id,
			'name' => $value['name'],
			'ext' => $value['extension'],
			'size' => filesize($value['savepath'] . $value['savename']) ,
			'filename' => date('Ymd') . '/' . $value['savename'],
			'ftype' => "1001",
			'iso_prod_type' => "1",
			'type' => "1",
			'create_uid' => current_user("eid"),
			'create_user' => current_user("ep_name"),
			);
			$id = $attach->add($new_attach);
		}
	}
	showmsg("success","success","?m=customer&c=change&a=list");
}else{
	$cti_id = $db->getField("certificate","cti_id",array("id"=>$zsid));
	if($id){
		$res=$db->find_one('change_app',array(
			   'id'=>$id
		));
		${"checked".$res[app_type]} = "checked";
		$zsid = $res[zsid];
		$cti_id = $res[cti_id];
		$num=unserialize($res['type']);
		if(!empty($num)){
			foreach ($num as $key => $value) {
				$changeitem_li=str_replace("value='$value'", "value='$value' checked", $changeitem_li);
			}
		}
	}
	
	$prod = $db->get_row("select * from sp_contract_item cti where cti_id = '$cti_id'");
	if($new = unserialize($prod['new'])){
		$prod = array_merge($prod,$new);
		extract($new);
	}
	$cti_id= $prod['cti_id'];
	$eid= $prod['eid'];
	$ep_manu_id= $prod['ep_manu_id'];
	$ep_prod_id= $prod['ep_prod_id'];

	$ep_name=$db->find_one('enterprises',array('eid'=>$eid));
	$ep_manu=$db->find_one('enterprises',array('eid'=>$ep_manu_id));
	$ep_prod=$db->find_one('enterprises',array('eid'=>$ep_prod_id));
	$prod_id=$prod['prod_id'];
	$fac_code=$prod['fac_code'];
	$xiaolei=$db->find_one('settings_prod_xiaolei',array('code'=>$prod_id,'fac_code'=>$fac_code));

	$ep_nature=$ep_name['nature'];
	$ep_statecode=$ep_name['statecode'];
	$ep_manu_nature=$ep_manu['nature'];
	$ep_manu_statecode=$ep_manu['statecode'];
	$ep_prod_nature=$ep_prod['nature'];
	$ep_prod_statecode=$ep_prod['statecode'];
	$payment_name = $db->meta($eid,"name_ac",'',"enterprise");
	$payment_addr = $db->meta($eid,"r_add",'',"enterprise");



	tpl();

}



?>