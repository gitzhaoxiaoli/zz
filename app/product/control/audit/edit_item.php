<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *	增加特殊审核项
 */
$cti_id = (int)getgp('cti_id');
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
	// $ifchangecert	= getgp( 'ifchangecert' );
	$final_date		= getgp( 'final_date' );
	// $scope			= getgp("scope");
	//审核类型是这些的 允许重复
	$type_allow=array("1009","1011");
	if(!in_array($audit_type,$type_allow)){
		$sql = "select * from sp_project where cti_id='$cti_id' and deleted='0' and audit_type='$audit_type' order by audit_type asc ";
		$info = $db->get_row($sql);
		if($info){
			echo "<script type='text/javascript'>alert('同一种审核类型不允许重复');history.go(-1);</script>";
			exit;
		}
	}
	$row = $cti->get( array( 'cti_id' => $cti_id ) );
	extract($row);
	$cert=$db->get_row("SELECT * FROM `sp_certificate` WHERE `cti_id` = '$row[cti_id]' AND `eid` = '$row[eid]' ORDER BY `e_date` DESC LIMIT 1");
	$p_info=$db->get_row("");
	$new_item=array(
            'eid' => $eid,
            'ep_manu_id' => $ep_manu_id,
            'ep_prod_id' => $ep_prod_id,
            'ct_id' => $ct_id,
            'cti_id' => $cti_id,
            'cti_code' => $cti_code,
            'iso' => $iso,
            'prod_ver' => $prod_ver,
            'audit_type' => $_POST[audit_type],
            'ctfrom' => $ctfrom,
            'total' => $total,
            'iso_prod_type' => 1,
            'ct_code'=>$ct_code,
			'prod_id' => $prod_id,
			'fac_code' => $fac_code,
			'scope'=>$scope,
			'scope_e'=>$scope_e,
			'is_samp'=>$_POST['is_samp'],
			'is_check'=>$_POST['is_check'],
			'final_date'=>$_POST['final_date'],
        );
	if($new_item[is_check] ){//检查
		$new_item[status]=0;
		$new_item[redata_status]=0;
		$new_item[pd_type]=0;
		$new_item['is_check'] = 1;	
	}else{
		$new_item[redata_status]=1;
		$new_item['is_samp'] = 1;
		$new_item[status]=3;
		$new_item['is_check'] = 0;	
	}
	// exit(p($new_item));
	$audit->add( $new_item );
	$url="?m=product&c=audit&a=list_contract_item";
	showmsg( 'success', 'success', $url );
} else {

	$sql = "select * from sp_project where cti_id='$cti_id' and deleted='0'  order by audit_type asc ";
	$res = $db->query($sql);
	$ddatas = array();
	while($row = $db->fetch_array($res)){
		$ddatas[] = $row;
	}
	tpl();
}


?>