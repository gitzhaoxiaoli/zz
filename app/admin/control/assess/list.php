<?php
!defined('IN_SUPU') && exit('Forbidden');
$comment_select="";
$province_select = f_province_select();//省分下拉 (搜索用
$_query=$db->query("SELECT * FROM `sp_hr` WHERE `job_type` LIKE '%1006%' AND  `deleted` = '0' AND `is_hire` = '1' ");
while($_r=$db->fetch_array($_query)){
	$comment_select.="<option value=\"$_r[id]\">$_r[name]</option>";

}
$rect_array=array("无","已整改","<span style='color:#00f'>未整改</span>");
$province_select = f_province_select();//省分下拉 (搜索用)

//认证评定
	extract( $_GET );
//标签样式	
	$pd_type = (int)$pd_type;
	$pd_type_0 = $pd_type_1 = $pd_type_4 = $pd_type_3 = $pd_type_5 = '';
	${'pd_type_'.$pd_type} = " ui-tabs-active ui-state-active";
//搜索条件
$fields = $join = $where = '';
	//企业名称
	if( $ep_name ){
		$_eids = array();
		$_query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%".str_replace('%','\%',trim($ep_name))."%'");
		while( $rt = $db->fetch_array( $_query ) ){
			$_eids[] = $rt['eid'];
		}
		if( $_eids ){
			$where .= " AND p.eid IN (".implode(',',$_eids).")";
		} else {
			$where .= " AND p.eid < -1";
		}
	}
//省份
if( $areacode=getgp("areacode") ){
	$pcode = substr($areacode,0,2) . '0000';
	$_eids = array(-1);
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '".substr($areacode,0,2)."'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	$where .= " AND p.eid IN (".implode(',',$_eids).")";
	unset( $_eids, $_query, $rt, $_eids );
	
	$province_select = str_replace( "value=\"$pcode\">", "value=\"$pcode\" selected>" , $province_select );
}

//@gxd trim('$ct_code')
	//合同编号
if( $ct_code=trim($ct_code) ){
   $where .= " AND p.ct_code = '$ct_code'";
	
}
if($comment){
	$comment_select=str_replace("value=\"$comment\"","value=\"$comment\" selected",$comment_select);
	$where .= " AND p.comment_a_uid = '$comment' OR p.comment_b_uid = '$comment'";


}
//合同项目编号
if( $cti_code=trim($cti_code) ){
	$where .= " AND p.cti_code like '%$cti_code%'";
}
	//体系
	if( $iso ){
		$where .= " AND p.iso = '$iso'";
		$iso_select = str_replace( "value=\"$iso\">", "value=\"$iso\" selected>" , $iso_select );
	}

	//审核类型
	if( $audit_type ){
		$where .= " AND p.audit_type = '$audit_type'";
		$audit_type_select = str_replace( "value=\"$audit_type\">", "value=\"$audit_type\" selected>" , $audit_type_select );
	}

	//标准版本
	$audit_ver = getgp( 'audit_ver' );
	if( $audit_ver ){
		$where .= " AND p.audit_ver = '$audit_ver'";
		$audit_ver_select = str_replace( "value=\"$audit_ver\">", "value=\"$audit_ver\" selected>" , $audit_ver_select );
	}

	//审核时间
	$_tids = array();
	$task_where = '';
	if( $audit_start_s ){
		$task_where .= " AND tb_date >= '$audit_start_s'";
	}
	if( $audit_start_e ){
		$task_where .= " AND tb_date <= '$audit_start_e'";
	}
	if( $audit_end_s ){
		$task_where .= " AND te_date >= '$audit_end_s'";
	}
	if( $audit_end_e ){
		$task_where .= " AND te_date <= '$audit_end_e'";
	}
	if( $task_where ){
		$query = $db->query("SELECT id FROM sp_task WHERE 1 $task_where");
		while( $rt = $db->fetch_array( $query ) ){
			$_tids[] = $rt['id'];
		}
	}
	if( $_tids ){
		$where .= " AND p.tid IN (".implode(',',$_tids).")";
	}

 
	//合同来源限制
	$len = get_ctfrom_level( current_user( 'ctfrom' ) ); //获取当前用户合同来源

	if( $ctfrom && substr( $ctfrom, 0, $len ) == substr( current_user( 'ctfrom' ), 0, $len ) ){
		$_len = get_ctfrom_level( $ctfrom );
		$len = $_len;
	} else {
		$ctfrom = current_user( 'ctfrom' );
	}
	switch( $len ){
		case 2	: $add = 1000000; break;
		case 4	: $add = 10000; break;
		case 6	: $add = 100; break;
		case 8	: $add = 1; break;
	}
	$ctfrom_e = sprintf("%08d",$ctfrom+$add);
 	$where .= " AND p.ctfrom >= '$ctfrom' AND p.ctfrom < '$ctfrom_e'";
 
//审批时间
	if( $sp_start ){
		$where .= " AND p.sp_date >= '$sp_start'";
	}
	if( $sp_end ){
		$where .= " AND p.sp_date <= '$sp_end'";
	}

	//要获取的字段
	$fields .= "p.*,e.ep_name,t.tb_date,t.te_date";

	//要关联的表
	$join .= " LEFT JOIN sp_enterprises e ON e.eid = p.eid";
	$join .= " LEFT JOIN sp_task t ON t.id = p.tid";


	$pd_type_total = array(0,0,0,0,0,0);
	$where .= " AND p.status = 3 AND p.deleted=0 AND p.iso_prod_type = 0 AND p.redata_status=1";
	if( !$export ){
		$query = $db->query("SELECT p.pd_type,COUNT(*) total FROM sp_project p WHERE 1 $where GROUP BY p.pd_type");
		while( $rt = $db->fetch_array( $query ) ){
			$pd_type_total[$rt['pd_type']] = $rt['total'];
		}
		$pages = numfpage( $pd_type_total[$pd_type] );
	}


	$where .= " AND p.pd_type = '$pd_type'";
	
	$resdb = array();
	$sql = "SELECT $fields FROM sp_project p $join WHERE 1 $where ORDER BY t.te_date DESC $pages[limit]";
	$query = $db->query( $sql );
	while( $rt = $db->fetch_array( $query ) ){
		//$rt['leader'] = $db->get_var( "SELECT name FROM sp_task_audit_team WHERE pid = '$rt[id]' and role='1001'" );
		$rt['audit_type_V'] = f_audit_type( $rt['audit_type'] );
		$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
		//$rt['iso_V'] = f_iso( $rt['iso'] );
		$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
		$rt['is_finance_V'] = ($rt['is_finance'] == '2')?'收完':'未收完';
		$rt['comment_date'] = mysql2date( 'Y-m-d', $rt['comment_date'] );
		'1970-01-01' == $rt['comment_date'] && $rt['comment_date'] = '';
		if($rt['comment_pass'] == "1" ){
            $rt['comment_pass_V']='(是)';
            }
        elseif($rt['comment_pass'] == "2")
            $rt['comment_pass_V']='(否)';

		// $rt['rect_finish_V'] = $rect_array[$rt['rect_finish']];

		$rt['sp_date'] == '0000-00-00' && $rt['sp_date'] = '';
		
		$pids[] = $rt[id];
		$rt[leader]=$db->getField("task_audit_team","name",array("role"=>'1001',"pid"=>$rt[id]));

		$resdb[$rt['id']] = $rt;
	}
	// p($resdb);die;

	if( !$export ){
		tpl( 'assess/list' );
	} else {
		ob_start();
		tpl( 'xls/list_assess' );
		$data = ob_get_contents();
		ob_end_clean();

		export_xls( '企业列表', $data );
	}