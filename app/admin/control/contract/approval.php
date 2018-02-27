<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 * 合同评审审批取信息
 */
$eid=(int)getgp( 'eid' );
$ct_id = (int)getgp( 'ct_id' );

	//合同信息
	$sql = "SELECT ct.*,e.ep_name FROM sp_contract ct INNER JOIN sp_enterprises e ON e.eid = ct.eid WHERE ct_id = '$ct_id'";
	$contract = $db->get_row( $sql );
	$contract['audit_type_V'] = f_audit_type( $contract['audit_type'] );

	$approval_date = ($contract['approval_date'] == '0000-00-00') ? '' :$contract['approval_date'];
 	//是否可审批
	$approval_disabled = ( 2 != $contract['status'] ) ? 'disabled' : '';

	//是否可撤销审批
	$projects = $db->get_results("SELECT * FROM sp_project WHERE ct_id = '$ct_id' AND deleted = 0 AND iso_prod_type = 0 ", 'id');
	if( $projects ){
		$disabled = false;
		foreach( $projects as $project ){
			if( 0 != $project['status'] ){
				$disabled = true;
				break;
			}
		}
	}
	$unapproval_disabled = ( $disabled || 3 != $contract['status'] ) ? 'disabled' : '';

	$allow_types = array('1001','1002');
	$ct_archives = array();
	$archive_join = " LEFT JOIN sp_hr hr ON hr.id = a.create_uid";
	$sql = "SELECT a.*,hr.name author FROM sp_attachments a $archive_join WHERE a.ct_id = '$ct_id' AND a.ftype IN ('".implode("','",$allow_types)."') ORDER BY a.id ASC";
	$query = $db->query( $sql );
	while( $rt = $db->fetch_array( $query ) ){
		$rt['ftype_V'] = f_arctype( $rt['ftype'] );
		$ct_archives[$rt['id']] = $rt;
	}

	/*****************取合同评审信息********/
	require ("data/cache/mark.cache.php");
	require( ROOT . '/data/cache/add_basis.cache.php' );
	require( ROOT . '/data/cache/exc_basis.cache.php' );
	$mark_label = "";
	if($mark_array)
		foreach ($mark_array as $key => $value) {
			if($value['is_stop'])continue;
			$mark_label .= "<label>$value[name]<input type=\"radio\" name=\"\" value=\"$key\"  /></label>";

		}
	// $contract = $ct->get( array( 'ct_id' => $ct_id ) );
	extract( $contract, EXTR_SKIP );
 	
	

	$is_first_V = ($is_first == 'y') ? '是' : '否';
	$is_site_Y = ($is_site)?'checked':'';
	$is_site_N = ($is_site)?'':'checked';

	$join = $where = '';
	$ct_items = array();
	$iso_arr = array();
	$where .= " AND ct_id = '$ct_id' AND deleted=0";
	$sql = "SELECT * FROM sp_contract_item  WHERE 1 $where AND iso_prod_type = 0 order by iso";
	$query = $db->query( $sql );
	while( $rt = $db->fetch_array( $query ) ){
		// 处理 认可标志
		$rt['mark_label'] = str_replace('name=""', 'name="mark['.$rt[cti_id].']"', $mark_label);
		$rt['mark_label'] = str_replace("value=\"$rt[mark]\"", "value=\"$rt[mark]\" checked", $rt['mark_label']);
		$add_basis=unserialize($rt['add_basis']);
		foreach($add_basis_array as $k=>$item){
		if($item['is_stop']) continue;
		$ckeck="";
		if(@in_array($k,$add_basis))
			$ckeck="checked";
		$rt['add_basis_check'].="<label><input type=\"checkbox\" name=\"add_basis[$rt[cti_id]][]\" value=\"$k\" $ckeck />".$item['name']."</label><br/>";

	}
		$exc_basis=unserialize($rt['exc_basis']);
		foreach($exc_basis_array as $k=>$item){
			if($item['is_stop']) continue;
			$ckeck="";
			if(@in_array($k,$exc_basis))
				$ckeck="checked";
			$rt['exc_basis_check'].="<label><input type=\"checkbox\" name=\"exc_basis[$rt[cti_id]][]\" value=\"$k\" $ckeck />".$item['name']."</label><br/>";

		}

		$rt['risk_level_select'] = str_replace( "value=\"$rt[risk_level]\">", "value=\"$rt[risk_level]\" selected>",  $risk_level_select );
		$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
		$rt['audit_type_V'] = f_audit_type( $rt['audit_type'] );
		$rt['is_turn_V'] = ($rt['is_turn']) ? '是' : '否';
		
		$ct_items[$rt['cti_id']] = $rt;
	}

	/*****************取合同评审信息********/

	tpl( 'contract/approval' );