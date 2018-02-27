<?php
/**********************************************
 *											  *
 *				公共信息					  *
 *											  *
 **********************************************/

function einfo( $args ){

	//判断审核员
	$job_type = explode('|', current_user('job_type'));
	foreach ($job_type as $k=>$v){
		if($v == '1004-1'){
			$auditor = 1;
		}
	}
	
	if( empty( $args ) ) return false;
	$default = array(
		'eid'		=> 0,
		'ct_id'		=> 0,
		'cti_id'	=> 0,
		'pid'		=> 0,
		'tid'		=> 0,
		'auditor_id'=> 0,
		'pd_id'		=> 0,
		'cert_id'	=> 0,
		'cg_id'		=> 0,
		'width'		=> 750
	);
	global $db;
	$args = parse_args( $args, $default );
	$ct_id=$args['ct_id'];
	$tid = $args['tid'];
	$eid = 0;
	if( $args['eid'] )
		$eid = $args['eid'];
	elseif( $args['ct_id'] )
		$eid = get_eid_by_ct_id( $args['ct_id'] );
	elseif( $args['cti_id'] ){
		$res = get_eid_by_cti_id( $args['cti_id'] );
		$eid=$res[eid];
		$ct_id=$res[ct_id];
	}elseif( $args['pid'] ){
		$res = get_eid_by_pid( $args['pid'] );
		$eid=$res[eid];
		$ct_id=$res[ct_id];
	}elseif( $args['tid'] ){
		$res = get_eid_by_tid( $args['tid'] );	
		$eid=$res[eid];

		$ct_id=$res[ct_id];
	}elseif( $args['cert_id'] ){
		$res = get_eid_by_cert_id( $args['cert_id'] );
		$eid=$res[eid];
		$ct_id=$res[ct_id];
	}elseif( $args['cg_id'] ){
		$res = cg_id2eid( $args['cg_id'] );
		$eid=$res[eid];
		$ct_id=$res[ct_id];
	}
	!$ct_id && $ct_id=-1;
	if( empty( $eid ) ) return false;
	//显示开关
	$is_view = array(
		'enterprise'	=> true,
		'contract'		=> false,
		'audit'			=> false,
		'cert'			=> false,
		'finance'		=> false,
		'archive'		=> false,
		'archive1'		=> false,
		'visit'			=>false
	);
	/*################
	 #      企业     #
	 ################*/
	//企业信息
	$enterprise = load( 'enterprise' );
	$e_info = $enterprise->get( array( 'eid' => $eid ) );

	$e_info['ctfrom'] = f_ctfrom( $e_info['ctfrom'] );
 
	//关联企业
	$union_enterprises = array();
	//@HBJ 2013年9月11日 10:24:24 修复 e.ep_amount 不能显示的bug(添加了该字段的读取)
	//@WZM 2013-09-28 关联公司只显示一个其实是多个
	$query = $db->query( "SELECT e.ep_name,e.ep_amount,e.audit_code,e.scope FROM sp_enterprises e WHERE e.parent_id = '$eid'" );
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

	/*################
	 #      合同     #
	 ################*/


	/**/
	//合同信息
	$ct_infos = array();
	$query = $db->query( "SELECT * FROM sp_contract WHERE eid = '$eid' AND deleted = 0 ORDER BY ct_id DESC" );
	while( $rt = $db->fetch_array( $query ) ){
		$ct_infos[$rt['ct_id']] = $rt;
	}
	
	
	$ct_infos && $is_view['contract'] = true;
	//合同项目
	$cti_infos = array();
	$query = $db->query( "SELECT * FROM sp_contract_item WHERE eid = '$eid' AND deleted = 0" );
	while( $rt = $db->fetch_array( $query ) ){
		isset( $ct_infos[$rt['ct_id']]['items'] ) or $ct_infos[$rt['ct_id']]['items'] = array();
        $rt[risk_level]=read_cache('risk_level',$rt[risk_level]);
		$ct_infos[$rt['ct_id']]['items'][$rt['cti_id']] = $rt;
		$child_query=$db->query("SELECT * FROM `sp_contract_num` WHERE `ct_id` = '$rt[ct_id]' AND `type` = '1' order by eid");
		while($r=$db->fetch_array($child_query)){
			$r[name]=$db->get_var("SELECT ep_name FROM `sp_enterprises` WHERE `eid` = '$r[eid]'");
			$ct_infos[$rt['ct_id']][child][$r[id]]=$r;
		}
		$site_query=$db->query("SELECT * FROM `sp_contract_num` WHERE `ct_id` = '$rt[ct_id]' AND `type` = '2' order by eid" );
		while($r1=$db->fetch_array($site_query)){
			$r1[name]=$db->get_var("SELECT es_name FROM `sp_enterprises_site` WHERE  `es_id` = '$r1[eid]'");
			$r1[iso]=f_iso($rt[iso]);
			$ct_infos[$rt['ct_id']][site][$r1[id]]=$r1;
		}
		// p($ct_infos);exit;
	}

	/*
		合同
	*/
	
	$ct_project = $db->find_results("project", array('ct_id'=>$ct_id));
	// echo $db->sql;
	// p($ct_id);die;



	/*################
	 #    审核任务   #
	 ################*/
	//审核项目
	$G_projects = array();
	$query = $db->query( "SELECT * FROM sp_project   WHERE eid = '$eid' AND deleted = 0  " );
	while( $rt = $db->fetch_array( $query ) ){
		$rt['iso_V'] = f_iso( $rt['iso'] );
		$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
		$rt['audit_type_V'] = f_audit_type( $rt['audit_type'] );
		$G_projects[$rt['id']] = $rt;
	}

	$projects && $is_view['audit'] = true;
	//审核任务
	$t_infos = array();
	$query = $db->query( "SELECT * FROM sp_task WHERE eid = '$eid' AND deleted = 0 order by te_date desc" );
	while( $rt = $db->fetch_array( $query ) ){
		$rt['tb_date'] = mysql2date( 'Y-m-d H:i', $rt['tb_date'] );
		$rt['te_date'] = mysql2date( 'Y-m-d H:i', $rt['te_date'] );
		$t_infos[$rt['id']] = $rt;
	}
	//审核任务项目/审核组信息
	if( $t_infos ){
		$query = $db->query( "SELECT tid,id pid,audit_type,iso FROM sp_project WHERE tid IN (".implode(',',array_keys( $t_infos )).") order by id DESC" );
		while( $rt = $db->fetch_array( $query ) ){
			isset( $t_infos[$rt['tid']]['items'] ) or $t_infos[$rt['tid']]['items'] = array();
			isset( $projects[$rt['pid']] ) && $t_infos[$rt['tid']]['items'][$rt['pid']] = $projects[$rt['pid']];
            //$t_infos[$rt['tid']]['audit_type'][]=f_iso($rt[iso]).":".read_cache("audit_type",$rt['audit_type']);
		}

		//审核组
		$query = $db->query( "SELECT * FROM sp_task_audit_team WHERE tid IN (".implode(',',array_keys( $t_infos )).") AND deleted = 0  order by role" );
		while( $rt = $db->fetch_array( $query ) ){
			isset( $t_infos[$rt['tid']]['auditors'] ) or $t_infos[$rt['tid']]['auditors'] = array();
			$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
			$rt['is_leader'] = ( '1001' == $rt['role'] ) ? '是' : '否';
			$rt['qua_type_V'] = f_qua_type( $rt['qua_type'] );
			$rt['tel'] = $db->get_var("SELECT tel FROM `sp_hr` WHERE `id` = '$rt[uid]'");
			$rt['num'] =mkdate($rt['taskBeginDate'],$rt['taskEndDate']);
            $rt['wid'] =$db->getCol("task_audit_team","uid",array('tid'=>$rt[tid]));
			$t_infos[$rt['tid']]['auditors'][$rt['id']] = $rt;
		}
	}

	$t_infos && $is_view['audit'] = true;




	/*################
	 #    认证决定   #
	 ################*/
	//评定
	$pds = array();
	$query = $db->query( "SELECT * FROM sp_project WHERE eid = '$eid' AND deleted = 0" );
	while( $rt = $db->fetch_array( $query ) ){
		$pds[$rt['id']] = $rt;
	}
	//$pds && $is_view['assess'] = true;
	//评定人员


	//评定问题



	/*################
	 #      证书     #
	 ################*/
	//证书
	$certs = array();
	$query = $db->query( "SELECT * FROM sp_certificate WHERE eid = '$eid' AND deleted=0 ORDER BY s_date DESC" );
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

	/* 说明:添加新模块 */
	/* @zys 2016-5-19 */
	/*################
	#  合同回访记录  #
	################*/
	$htnotes = array();
	$query = $db->query("SELECT * FROM sp_enterprises_note WHERE eid = '$eid' order by id DESC limit 5 ");
	// p($eid);die;
	while( $rt = $db->fetch_array( $query ) ){
		$rt['eid'] = f_username($rt['eid']);
		$htnotes[$rt['id']] = $rt;
	}
	$htnotes && $is_htnotes['htnotes'] = true;

	/*################
	 #    财务收费   #
	 ################*/
/* @wangp 目前没有显示 所以暂时注释掉 2013-09-28 16:41
	//合同费用
	$contract_costs = array();
	$query = $db->query( "SELECT cc.*,ct.ct_code FROM sp_contract_cost cc LEFT JOIN sp_contract ct ON ct.ct_id = cc.ct_id WHERE cc.eid = '$eid' AND cc.deleted = 0 AND cc.cost > 0" );
	while( $rt = $db->fetch_array( $query ) ){
		$contract_costs[$rt['id']] = $rt;
	}
	$matchs = parse_url( $_SERVER['HTTP_REFERER'] );
	parse_str( $matchs['query'], $url_par );
	//($contract_costs && $url_par['m'] != 'auditor' ) && $is_view['finance'] = true;

	//收费明细
	$finance_datiles = array();
	//@wangp 取收费明细 2013-09-28 16:39
	$sql = "select * from sp_contract_cost_detail where eid = '$eid' order by id asc";
	$res = $db->query($sql);
	while($row=$db->fetch_array($res)){
		$sql = "select * from sp_project where id IN (SELECT pid FROM sp_contract_cost_detial_project WHERE ccd_id = '$row[id]')";

		$in_res = $db->query($sql);
		$audit_type_s = $iso_s = array();
		while($in_row = $db->fetch_array($in_res)){
			$iso_s[] = $iso_array[$in_row['iso']]['name'];
			$audit_type_s[] = f_audit_type($in_row['audit_type']);
		}
		$row['iso_s'] = implode('<br>', $iso_s);
		$row['audit_type_s'] = implode('<br>', $audit_type_s);
		$finance_datiles[] = $row;
	}
*/
	//企业文档
	$where = " AND view = " . (int)current_user("check_auth");
	$archives = array();
	$query = $db->query( "SELECT * FROM sp_attachments  WHERE eid='$eid' and ct_id = 0  $where order by id desc" );
	// echo $db->sql;
	while( $rt = $db->fetch_array( $query ) ){
		$rt['ftype'] = f_arctype($rt['ftype']);
		$rt['create_uid'] = f_username($rt['create_uid']);
		$archives[$rt['id']] = $rt;
	}


	$query = $db->query( "SELECT * FROM sp_attachments WHERE ct_id ='$ct_id'  AND eid='$eid' AND tid = 0  $where order by id desc" );
	while( $rt = $db->fetch_array( $query ) ){
		$rt['ftype'] = f_arctype($rt['ftype']);
		$rt['create_uid'] = f_username($rt['create_uid']);
		$archives[$rt['id']] = $rt;
	}
	
	$query = $db->query( "SELECT * FROM sp_attachments WHERE ct_id ='$ct_id'  AND eid='$eid' AND tid = 0  $where order by id desc" );
	while( $rt = $db->fetch_array( $query ) ){
		$rt['ftype'] = f_arctype($rt['ftype']);
		$rt['create_uid'] = f_username($rt['create_uid']);
		$archives[$rt['id']] = $rt;
	}

	$archives && $is_view['archive'] = true;
	// p($archives);
	ob_start();
	if( file_exists( STYLESHEET_DIR . 'einfo.htm' ) ){
		$located = STYLESHEET_DIR . 'einfo.htm';
	} elseif( file_exists( VIEW_DIR . 'einfo.htm' ) ){
		$located = VIEW_DIR . 'einfo.htm';
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
	return $db->get_row( "SELECT eid,ct_id FROM sp_contract_item WHERE cti_id = '$cti_id'" );
}

/*
 * 审核项目ID转企业ID
 */
function get_eid_by_pid( $pid ){
	global $db;
	return $db->get_row( "SELECT eid,ct_id FROM sp_project WHERE id = '$pid'" );
}

/*
 * 任务ID转企业ID
 */
function get_eid_by_tid( $tid ){
	global $db;
	return $db->get_row( "SELECT eid,ct_id FROM sp_project WHERE  tid = '$tid' order by ct_id desc limit 1" );
}


/*
 * 证书ID转企业ID
 */
function get_eid_by_cert_id( $cert_id ){
	global $db;
	return $db->get_row( "SELECT eid,ct_id FROM sp_certificate WHERE id = '$cert_id'" );
}


/*
 * 变更ID转企业ID
 */
function cg_id2eid( $cg_id ){
	global $db;
	return get_eid_by_cert_id( $db->get_var( "SELECT zsid FROM sp_certificate_change WHERE id = '$cg_id'" ) );
}

?>