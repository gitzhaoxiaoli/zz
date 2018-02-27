<?php
!defined('IN_SUPU') && exit('Forbidden');
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
$cid=(int)$_POST['cid'];


// p($_POST);
// exit;

// $eid        = $db->getField("enterprises", "eid", "work_code = '$work_code'");
$ep_manu_id = $db->getField("enterprises", "eid", "work_code = '$manu_work_code '");
$ep_prod_id = $db->getField("enterprises", "eid", "work_code = '$prod_work_code'");
$eid = current_user("eid");

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
/* else {
    
    $eid = $db->insert('enterprises', array(
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
        'areaaddr' => $areaaddr,
		'areacode' => getgp("areacode"),
        'cta_addr' => $cta_addr,
        'cta_addr_e' => $cta_addr_e,
        'cta_addrcode' => $cta_addrcode,
		'ctfrom'	=> "01000000",
    ));
} */

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
} else {
    
        $ep_manu_id = $db->insert('enterprises', array(
            
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
            'cta_addrcode' => $manu_cta_addrcode,
			'ctfrom'	=> "01000000",
        ));
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

else {
    $ep_prod_id = $db->insert("enterprises", array(
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
        'person_email' => $prod_person_email,
                 'ep_mail'=>$prod_person_email,
        'areaaddr' => $prod_areaaddr,
        'prod_addrcode' => $prod_cta_addrcode,
      
        
    ));
} 

$iso = getgp("iso");
$audit_ver = "B010101";
$_type = "C";
if($iso == "B05"){
	$audit_ver = "B0599";
	$_type = "P";
}
$year = date("Y");
$sort = $db->get_var("SELECT sort FROM `sp_contract_item` WHERE `deleted` = '0' AND `iso_prod_type` = '1' AND `iso` = '$iso' ORDER BY `sort` DESC LIMIT 1");
if(!$cid){
	$sort ++;
}
$sort = sprintf("%06d", $sort);

$cti_code= $year . "-".$_type."-" . $_POST['prod_id'] . "-" . $sort;
if($type == '6')
	$app_type = "2";
else
	$app_type = "0";
$up_cti = array(
    'iso' => $iso,
    'ep_prod_addr' => $prod_cta_addr,
    'ep_prod_addr_e' => $prod_cta_addr_e,
    'pro_areaaddr' => getgp("prod_areacode"),
    'audit_type' => $type,
    'app_type' => $app_type,
    'total' => $prod_ep_amount,
    'prod_id' => $prod_id,
    'fac_code' => $fac_code,
    'prod_ver' => $prod_sta,
    'audit_ver' => $audit_ver,
    'scope' => $scope,
    'prod_name_chinese' => $pro_name,
    'prod_name_english' => $pro_name_e,
    'cti_code' => $cti_code,
    'app_finish' => getgp("app_finish"),
    'mark' => getgp("mark"),
    'note' => getgp("note"),
	"new"  => serialize($_POST['new']),
    );
if($cid){
	$cti_id = $cid;
	$db->update('contract_item', $up_cti,"cti_id='$cid'");
}else{
	$new_cti = array(
    'eid' => $eid,
    'ep_manu_id' => $ep_manu_id,
    'ep_prod_id' => $ep_prod_id,
    'ctfrom' => '01000000',
    'iso_prod_type' => 1,
    'is_app' => 1,
    'sort'=>$sort,
    //'year'=>$year,
	"create_uid" => current_user("eid"),
	"create_user" => current_user("ep_name"),
    
);
$new_cti = array_merge($new_cti,$up_cti);
$cti_id = $db->insert('contract_item', $new_cti);
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
		'cti_id' => $cid,
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

showmsg("success","success","?m=customer&c=contract&a=list");






?>