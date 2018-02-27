<?php
!defined('IN_SUPU') && exit('Forbidden');
$ep_prod_id    = getgp("ep_prod_id");
$id    = getgp('id');
$eid    = getgp('eid');
$ctfrom = getgp('ctfrom');
$step = getgp("step");
// p($eid);die;
/*说明:停止使用*/
/*@zys 2016-5-6*/
/*
$place_select = '<select name="place" class="place">';
$place_select .= '<option value="">==请选择==</option>';
$place_select .= '<option value="1">TMP</option>';
$place_select .= '<option value="2">WMT</option>';
$place_select .= '</select>';*/
 require_once( ROOT . '/data/cache/app_change.cache.php' );//变更类别
// 变更类别
if($app_change_array){
	$changeitem_li = '';
	foreach($app_change_array as $key=>$value){
		$changeitem_li .= "<li><label><input type='checkbox' name='type[]' value='$key'  />".$key.'.'.$value['name']."</label></li>";
		
	}
} 

//审核类型
$audit_type_select = '';
if( $audit_type_array ){
	foreach( $audit_type_array as $code => $item ){
		if( in_array( $code, array( '1001', '1004-1','1004-2' ,'1007') ) )
			$audit_type_select .= "<option value=\"$code\">$item[name]</option>";
	}
}



$audit_codes =getgp( 'audit_code' );
$use_codes = getgp( 'use_code' );
if($step){
	//受理日期
	p($_POST);die;
	$accept_date = $_POST['accept_date'];
	// unset($_POST['accept_date']);
	unset($_POST[prod_name],$_POST[id]);
    $_POST[iso_prod_type] = 1;
	$_POST[audit_ver] = 'B010101';
    if($_POST[iso] == "B05")
		$_POST[audit_ver] = "B0599";
    unset($_POST[step],$_POST['ep_name']);
	$_POST['new'] = serialize($_POST['new']);
	$_type = "C";
	if($_POST[iso] == 'B05')
		$_type = "P";
	$year = date("Y");
	$sort = $db->get_var("SELECT sort FROM `sp_contract_item` WHERE `deleted` = '0' AND `iso_prod_type` = '1'  AND iso = '$_POST[iso]' ORDER BY `sort` DESC LIMIT 1");
	if(!$id)
		$sort ++;
	$sort = sprintf("%06d",$sort);
	$_POST['cti_code'] = $year."-".$_type."-".$_POST['prod_id']."-".$sort;
	$up_cti = $_POST;
	unset($up_cti[reportno],$up_cti[title],$up_cti[type],$up_cti[cg_af],$up_cti[cg_bf]);
    if($id){
           $res = $db->update("contract_item", $up_cti, array('cti_id' => $id));
    }else{
		

        $up_cti['sort'] = $sort ;
        $up_cti['ctfrom'] = $ctfrom ;
        $up_cti['approval_user'] = current_user('uid');
        /* $arr = array(
            "eid"=>$up_cti['eid'],
            "status"=>2,
            "ctfrom"=>$ctfrom ,
            "accept_date"=>$accept_date,
            "iso_prod_type"=>1,
            "ct_code"=>$up_cti['cti_code']."（HT）", // 合同号暂时生成
        );
        $up_cti['ct_id'] = $db->insert("contract",$arr); */
        $res = $db->insert("contract_item",$up_cti);
		
    }
	// 更新生产企业地址
	$areaaddr = f_region_all($up_cti[pro_areaaddr]);
	$db->update("enterprises",array("prod_addr"=>$up_cti[ep_prod_addr],"prod_addr_e"=>$up_cti[ep_prod_addr_e],"areacode"=>$up_cti[pro_areaaddr],"areaaddr"=>$areaaddr),array("eid"=>$ep_prod_id));

	// 更新变更申请表
	$cti = $db->find_one("contract_item",array("cti_id"=>$id));
	if($cti[app_type] == '1'){
		$up_change = array(
				"reportno"		=> $_POST[reportno],
				"title"			=> $_POST[title],
				// "app_type"		=> $_POST[audit_type],
				"type"			=> serialize($_POST['type']),
				"cg_af"			=> $_POST[cg_af],
				"cg_bf"			=> $_POST[cg_bf],
				);
			$db->update("change_app",$up_change,array('cti_id'=>$id));
	}
    if($res) {
        showmsg('success', 'success', "?m=product&c=contract&a=approval");
    }else{
        showmsg('error', 'error', "?m=product&c=contract&a=edit&ep_prod_id=$ep_prod_id");
    }
}elseif($id){
    $cti = $db->find_one("contract_item","and cti_id='$id'");

    $audit_type_select = str_replace("value=\"$cti[audit_type]\">", "value=\"$cti[audit_type]\" selected>", $audit_type_select);

	if($new = unserialize($cti['new']))
		$cti = array_merge($cti,$new);
    extract($cti);
	$pro_areatext = f_region_all($pro_areaaddr);
	$prod_name=$db->getField("settings_prod_xiaolei","name",array("code"=>$prod_id));
	if($prod_ver){
		$prod_arr=explode("；",$prod_ver);
		$body="";
		foreach($prod_arr as $value){
			$s=$db->getField("settings_ver","name",array("code"=>$value,"type"=>"b01001"));
			$body.="<p>";
			$body.=$value."  ".$s;
			$body.="<a attr='$value'  class='icon-del del_prod_ver_code'></a></p>";
			
		}
	}

	/*说明:停止使用*/
	/*@zys 2016-5-6*/
    /* $place_select = str_replace("value=\"$place\"","value=\"$place\" selected",$place_select);*/
    $ep_manu = $db->get_var("select ep_name from sp_enterprises where eid=$ep_manu_id");
    $ep_name = $db->get_var("select ep_name from sp_enterprises where eid=$eid");
    $ep_prod_name = $db->get_var("select ep_name from sp_enterprises where eid=$ep_prod_id");
	
	// 变更信息
	$res=$db->find_one('change_app',array(
			   'cti_id'=>$cti_id
		));
		$num=unserialize($res['type']);
		if(!empty($num)){
			foreach ($num as $key => $value) {
				$changeitem_li=str_replace("value='$value'", "value='$value' checked", $changeitem_li);
			}
		}
}else{
	$ep_info = $db->find_one("enterprises"," AND eid = '$ep_prod_id'","ep_name,prod_addr,prod_addr_e,areacode,areaaddr");
	$ep_prod_name = $ep_info['ep_name'];
	$ep_prod_addr = $ep_info['prod_addr'];
	$ep_prod_addr_e = $ep_info['prod_addr_e'];
	$pro_areaaddr = $ep_info['areacode'];
	$pro_areatext = $ep_info['areaaddr'];

}

tpl();