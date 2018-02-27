<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *	增加特殊审核项
 */
require( ROOT . '/data/cache/audit_ver.cache.php' );
$cti_id = (int)getgp('cti_id');
$ct_id=getgp("ct_id");
// $pid=getgp("pid");
if( $step ){
	if($step=="del"){
		$pid=getgp("pid");
		$db->update("project",array("deleted"=>1),array("id"=>$pid));
		showmsg("success","success","?c=audit&a=list_item");
		exit;
	}

	$audit_type		= getgp( 'audit_type' );
	// $audit_ver		= getgp( 'audit_ver' );
	$is_samp		= (int)getgp( 'is_samp' );
	$is_check		= (int)getgp( 'is_check' );
	$ifchangecert	= getgp( 'ifchangecert' );
	$pre_date		= getgp( 'pre_date' );
	// $scope			= getgp("scope");
	//审核类型是这些的 允许重复
	$type_allow=array("1008","1009","1010","1011");
	if(!in_array($audit_type,$type_allow)){
		$sql = "select * from sp_project where cti_id='$cti_id' and deleted='0' and audit_type='$audit_type' order by audit_type asc ";
		$info = $db->get_row($sql);
		if($info){
			echo "<script type='text/javascript'>alert('同一种审核类型不允许重复');history.go(-1);</script>";
			exit;//@HBJ 2013-09-26 修复bug避免同一种审核类型重复
		}
	}
	$row = $cti->get( array( 'cti_id' => $cti_id ) );
	$cert=$db->get_row("SELECT * FROM `sp_certificate` WHERE `cti_id` = '$row[cti_id]' AND `eid` = '$row[eid]' ORDER BY `e_date` DESC LIMIT 1");
	$p_info=$db->get_row("");
	$new_item = array(
		'eid'		=> $row['eid'],
		'cti_id'	=> $row['cti_id'],
		'ct_id'		=> $row['ct_id'],
		'ep_manu_id'=>$row['ep_manu_id'],
		'ep_prod_id'=>$row['ep_prod_id'],
		'ct_code'	=> $cert[ct_code],
		'cti_code'	=> $row['cti_code'],
		'ctfrom'	=> $row['ctfrom'],
		'iso'		=> $row['iso'],
		'audit_ver'	=> $cert['audit_ver'],
		'audit_code'=> $row['audit_code'],
		'use_code'	=> $row['use_code'],
		'st_num'	=> $row['xcsh_num'],
		'audit_type'=> $audit_type,
		'pre_date'	=> $pre_date,
		'scope'		=> $cert[cert_scope],
		'flag'		=> 1,
		'status'	=> 3,//默认评定=3；如果检查等于0；
		'redata_status'	=> 0,//资料收回状态0未收
		'ifchangecert'	=> $ifchangecert,
		'prod_ver'	=> $cert[prod_ver],
		'prod_id'	=> $cert[prod_id],
	);
	if($is_samp ){//检验
		$new_item[redata_status]=1;
		$new_item['is_samp'] = 1;
	};
	if($is_check ){//检查
		$new_item[status]=0;
		$new_item[redata_status]=0;
		$new_item['is_check'] = 1;	
	};
	// exit(p($new_item));
	$audit->add( $new_item );
	$url="?c=audit&a=list_contract_item";
	showmsg( 'success', 'success', $url );
} else {

	$sql = "select * from sp_project where cti_id='$cti_id' and deleted='0'  order by audit_type asc ";
	$res = $db->query($sql);
	$ddatas = array();
	while($row = $db->fetch_array($res)){
		$audit_ver=$row[audit_ver];
		$ddatas[] = $row;
	}

/* 	if($audit_ver_array){
		$ver_temp = substr($audit_ver,0,3);
		foreach($audit_ver_array as $key=>$value){
			if($value['audit_ver'] != $audit_ver){
				if($ver_temp==$value['iso'] && $value['is_stop'] == 0 ){
					$audit_ver_radio.= "<input type='radio' name='audit_ver' value=\"$value[audit_ver]\">".$value[audit_basis].'<br>';
			 	}
			}
		}
	} */
	/* $scope=$db->get_var("SELECT scope FROM `sp_contract_item` WHERE `cti_id` = '$cti_id' ");
	if($pid){
		$p_info=$db->get_row("SELECT cti_id,audit_type,pre_date,scope,ifchangecert FROM `sp_project` WHERE `id` = '$pid' ");
		extract($p_info,EXTR_OVERWRITE);
	
	} 
	$audit_type_select2=str_replace("value=\"$audit_type\"","value=\"$audit_type\" selected",$audit_type_select2);*/
	tpl( 'audit/edit_item' );
}


?>