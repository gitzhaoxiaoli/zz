<?php
!defined('IN_SUPU') && exit('Forbidden');

//评定问题列表
//@lyh 2016-03-21
//评定问题针对任务sp_task，不针对sp_project，

extract( $_GET, EXTR_SKIP );
$fields = $join = $where = '';

require CONF.'/cache/fl_question.cache.php'; //问题标题

//搜索条件
	//审核开始时间 起
	if( $tb_date_s )
		$where .=" AND t.tb_date >= '$tb_date_s 00:00:00'";
	//审核开始时间 止
	if( $tb_date_e )
		$where .=" AND t.tb_date <= '$tb_date_e 24:00:00'";

	//企业名称
	if( $ep_name ){
		$_eids = array();
		$_query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%".str_replace('%','\%',trim($ep_name))."%'");
		while( $rt = $db->fetch_array( $_query ) ){
			$_eids[] = $rt['eid'];
		}
		if( $_eids ){
			$where .= " AND a.eid IN (".implode(',',$_eids).")";
		}
	}

	//审核员
	if( $shzcy_name=trim($shzcy_name) ){
		$where .= " AND a.shzcy_name like '%$shzcy_name%'";
	}

	//要获取的字段
	$fields .= "a.*,t.tb_date,t.te_date";
	//$fields .= "a.*,p.ct_code,p.cti_code,p.eid,p.assess_date,p.sp_date,t.tb_date,t.te_date";
	//要关联的表
	$join .= " LEFT JOIN sp_task t ON a.tid = t.id";
	//$join .= " INNER JOIN sp_project p ON p.tid = t.id";
	

	$where .= " AND a.deleted = '0'";

	if( !$export ){
		$total=$db->get_var("SELECT COUNT(*) total FROM `sp_assess_notes` a $join WHERE 1 $where ");
		$pages = numfpage( $total );
	}

	$resdb = array();
 	$sql = "SELECT $fields FROM `sp_assess_notes` a $join WHERE 1 $where ORDER BY a.id DESC $pages[limit]";
	$query = $db->query( $sql );
	while( $rt = $db->fetch_array( $query ) ){
		$rt[ep_name]=$db->get_var("SELECT ep_name FROM `sp_enterprises` WHERE `eid` = '$rt[eid]'");
		$rt[leader]=$db->get_var("SELECT name FROM sp_task_audit_team WHERE `role` = '1001' and tid='{$rt[tid]}' and deleted=0");	//组长
		//$rt[tb_date] = $db->get_var("SELECT name FROM sp_task_audit_team WHERE `role` = '1001' and pid='{$rt[pid]}'");			//取审核员  自己的时间
		$rt['ctfrom'] = $db->getField('task',"ctfrom",array('id'=>$rt['tid']));
		$rt['ctfrom'] = f_ctfrom($rt['ctfrom']);
		$resdb[$rt['id']] = $rt;
		
	}


	if( !$export ){
		tpl();
	} else { //导出excel表格
		ob_start();
		tpl( 'xls/list_question' );
		$data = ob_get_contents();
		ob_end_clean();

		export_xls( '评定问题列表', $data );
	}