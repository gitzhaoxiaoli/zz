<?php
/**********************************************
 *											  *
 *				公共信息					  *
 *											  *
 **********************************************/

function prod_einfo( $args ){
	//判断审核员
	$job_type = explode('|', current_user('job_type'));
	foreach ($job_type as $k=>$v){
		if($v == '1004'){
			$auditor = 1;
		}
	}
	
	if( empty( $args ) ) return false;
	$default = array(
		'width'		=> 750
	);
	global $db;
	$args = parse_args( $args, $default );
	if( $args['ep_prod_id'] )
		$ep_prod_id = $args['ep_prod_id'];
	elseif( $cti_id=$args['cti_id'] ){
		
		$res = get_eid_by_cti_id( $args['cti_id'] );
		// p($res);
		extract($res,EXTR_SKIP);
	}elseif( $args['pid'] ){
		$res = get_eid_by_pid( $args['pid'] );
		extract($res);
	}elseif( $args['tid'] ){
		$tid = $args['tid'];
		$res = get_eid_by_tid( $args['tid'] );
		$ep_prod_id = $res['ep_prod_id'];
		$ct_id=$res[ct_id];
		$cti_id=$res[cti_id];
	}elseif( $args['zsid'] ){
		$res = get_eid_by_cert_id( $args['zsid'] );
		extract($res);
	}
	//if( empty( $eid ) ) return false;
	//显示开关
	$is_view = array(
		'ep_prod'		=> false,
		'contract'		=> false,
		'audit'			=> false,
		'cert'			=> false,
		'finance'		=> false,
		'archive'		=> false,
	);
	/*################
	 #      企业     #
	 ################*/
	// 生产企业
	if($ep_prod_id){
		$enterprise = load("enterprise");
		$e_info = $enterprise->get( array( 'eid' => $ep_prod_id ) );//生产企业
		$e_info['ctfrom'] = f_ctfrom( $e_info['ctfrom'] );
		//关联企业
		$union_enterprises = array();
		//@HBJ 2013年9月11日 10:24:24 修复 e.ep_amount 不能显示的bug(添加了该字段的读取)
		//@WZM 2013-09-28 关联公司只显示一个其实是多个
		$query = $db->query( "SELECT e.ep_name,e.ep_amount,e.audit_code,e.scope FROM sp_enterprises e WHERE e.parent_id = '$ep_prod_id'" );
		while( $rt = $db->fetch_array( $query ) ){
			$rt[scope]=unserialize($rt[scope]);
			$union_enterprises[] = $rt;
		}

		//分场所
		$sub_sites = array();
		$query = $db->query( "SELECT * FROM sp_enterprises_site WHERE eid = '$eid' AND deleted = 0" );
		while( $rt = $db->fetch_array( $query ) ){
			$sub_sites[$rt['es_id']] = $rt;
		}
		$is_view['ep_prod'] = true;
	}
	/*################
	 #   受理信息     #
	 ################*/
	if(!is_array($cti_id))
		$cti_id=array($cti_id);
	$cti_infos = array();
	foreach($cti_id as $id){
		$query = $db->query( "SELECT * FROM sp_contract_item WHERE cti_id = '$id' AND deleted = 0" );
		while( $rt = $db->fetch_array( $query ) ){
			$rt[ep_name]=getEpName($rt[eid]);
			$rt[manu_name]=getEpName($rt[ep_manu_id]);
			$rt[prod_name]=getEpName($rt[ep_prod_id]);
			// $rt[prod_id] = $db->getField("settings_prod_xiaolei","name",array("code" => $rt[prod_id]));
			// $rt[archives] = $db->get_results("SELECT * FROM sp_attachments  WHERE ct_id='$ct_id' and tid=0 order by id desc limit 10");
			$cti_infos[$rt[cti_id]]= $rt;
		}
	}
	$cti_infos && $is_view['contract']=true;


	/*################
	 #    审核任务   #
	 ################*/
	//审核任务
	$t_info = $auditors=array();
	$t_info = $db->get_row( "SELECT * FROM sp_task WHERE id = '$tid' AND deleted = 0 " );
	if($t_info ){
		$t_info = chk_arr($t_info);
		$t_info[tb_date] = substr($t_info[tb_date],0,10);
		$t_info[te_date] = substr($t_info[te_date],0,10);
		$t_info[tb_date] = substr($t_info[tb_date],0,10);
		$t_info[te_date] = substr($t_info[te_date],0,10);
	}
	//审核任务项目/审核组信息
	//审核组
	$query = $db->query( "SELECT * FROM sp_task_audit_team WHERE tid ='$tid' AND deleted = 0  order by role" );
	while( $rt = $db->fetch_array( $query ) ){
		$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
		$rt['is_leader'] = ( '1001' == $rt['role'] ) ? '是' : '否';
		$rt['qua_type_V'] = f_qua_type( $rt['qua_type'] );
		$rt['tel'] = $db->get_var("SELECT tel FROM `sp_hr` WHERE `id` = '$rt[uid]'");
		$rt['num'] =mkdate($rt['taskBeginDate'],$rt['taskEndDate']);
		$rt['wid'] =$db->getCol("task_audit_team","uid",array('tid'=>$rt[tid]));
		$auditors[$rt['id']] = $rt;
	}
	$t_info && $is_view['audit'] = true;






	/*################
	 #      证书     #
	 ################*/
	//证书
	$certs = array();
	$query = $db->query( "SELECT * FROM sp_certificate WHERE eid = '$ep_prod_id' AND deleted=0 AND iso_prod_type=1 ORDER BY s_date DESC" );
	while( $rt = $db->fetch_array( $query ) ){
		$certs[$rt['id']] = $rt;
	}

	$certs && $is_view['cert'] = true;
	//变更
	$cert_changes = array();
	if( $certs ){
		$query = $db->query( "SELECT * FROM sp_certificate_change WHERE zsid IN (".implode(',',array_keys($certs)).")" );
		while( $rt = $db->fetch_array( $query ) ){
			isset( $cert_changes[$rt['zsid']] ) or $cert_changes[$rt['zsid']] = array();
			$cert_changes[$rt['zsid']][$rt['id']] = $rt;
			$cert_changes[$rt['zsid']][$rt['id']]['cg_content']=$rt['cg_af'].'-'.$rt['cg_bf'];

		}
	}
	// 文档
	if(is_array($cti_id)){
		if($cti_id[0])
		$archives = $db->get_results("SELECT * FROM `sp_attachments` WHERE `cti_id` IN (".join(",",$cti_id).") AND `iso_prod_type` = '1'");
		$cti_id = join("|",$cti_id);
	}else{
		if($cti_id)
		$archives = $db->get_results("SELECT * FROM `sp_attachments` WHERE `cti_id` ='$cti_id' AND `iso_prod_type` = '1'");
		// echo $db->sql;
		
	}
	// p($archives);
	$archives && $is_view['archive'] = true;

	ob_start();
	if( file_exists( CP_VIEW_DIR . 'prod_einfo.htm' ) ){
		$located = CP_VIEW_DIR . 'prod_einfo.htm';
	}

	require_once $located;
	$result = ob_get_contents();
	ob_end_clean();
	unset( $located, $archives, $finance_datiles, $contract_costs, $cert_changes, $pds, $t_infos, $projects,
			$ct_infos, $sub_sites, $union_enterprises );
	return $result;
}


/*
 * 合同ID转企业ID
 */
function get_eid_by_ct_id( $ct_id ){
	global $db;
	return $db->get_var( "SELECT eid FROM sp_contract WHERE ct_id = '$ct_id'" );
}

/*
 * 合同项目ID转企业ID
 */
function get_eid_by_cti_id( $cti_id ){
	global $db;
	return $db->get_row( "SELECT eid,ct_id,ep_manu_id,ep_prod_id FROM sp_contract_item WHERE cti_id = '$cti_id'" );
}

/*
 * 审核项目ID转企业ID
 */
function get_eid_by_pid( $pid ){
	global $db;
	return $db->get_row( "SELECT eid,ct_id,ep_manu_id,ep_prod_id,cti_id FROM sp_project WHERE id = '$pid'" );
}

/*
 * 任务ID转企业ID
 */
function get_eid_by_tid( $tid ){
	global $db;
	$query=$db->query( "SELECT eid,ct_id,ep_manu_id,ep_prod_id,cti_id FROM sp_project WHERE  tid = '$tid' and deleted=0 AND iso_prod_type=1" );
	$ct_id=$cti_id=$eid=$ep_manu_id=array();
	while($rt=$db->fetch_array($query)){
		$ep_prod_id=$rt[ep_prod_id];
		$ct_id[]=$rt[ct_id];
		$cti_id[]=$rt[cti_id];
		$eid[]=$rt[eid];
		$ep_manu_id[]=$rt[ep_manu_id];
		
	}
	$data=array("eid"				=> $eid,
				"ep_manu_id"		=> $ep_manu_id,
				"ep_prod_id"		=> $ep_prod_id,
				"ct_id"				=> $ct_id,
				"cti_id"			=> $cti_id,
				);
	return $data;
}


/*
 * 证书ID转企业ID
 */
function get_eid_by_cert_id( $cert_id ){
	global $db;
	return $db->get_row( "SELECT eid,ep_manu_id,ep_prod_id,cti_id FROM sp_certificate WHERE id = '$cert_id'" );
}


/*
 * 变更ID转企业ID
 */
function cg_id2eid( $cg_id ){
	global $db;
	return get_eid_by_cert_id( $db->get_var( "SELECT zsid FROM sp_certificate_change WHERE id = '$cg_id'" ) );
}

?>