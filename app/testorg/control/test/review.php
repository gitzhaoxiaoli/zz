<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*检验评价
*/
$comment_select="";
$province_select = f_province_select();//省分下拉 (搜索用)
$_query=$db->query("SELECT * FROM `sp_hr` WHERE `job_type` LIKE '%1004-1%' OR `job_type` LIKE '%1004-2%' OR `job_type` LIKE '%1004-3%' AND `deleted` = '0' AND `is_hire` = '1' ");
while($_r=$db->fetch_array($_query)){
	$comment_select.="<option value=\"$_r[id]\">$_r[name]</option>";

}
$rect_array=array("无","已整改","<span style='color:#00f'>未整改</span>");

//认证评定
   extract($_GET, EXTR_SKIP);
//标签样式	
	$pd_type = (int)$pd_type;
	$pd_type_0 = $pd_type_1 = $pd_type_2 = $pd_type_3 = '';
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
if($fac_code=trim($fac_code)){
    $res = $db->find_one("enterprises",array('fac_code'=>$fac_code),"eid");
    $where .= "AND ep_prod_id = '$res[eid]'";
}
//合同认证申请编号
if( $cti_code=trim($cti_code) ){
	$where .= " AND p.cti_code like '%$cti_code%'";
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
 	// $where .= " AND p.ctfrom >= '$ctfrom' AND p.ctfrom < '$ctfrom_e'";
 
  //生产企业搜索
  
	if($ep_prod_name){
		$ep_prod_ids = $db->getCol("enterprises",'eid',"ep_name LIKE '%$ep_prod_name%'");
		
		$where .= " AND ep_prod_id IN (".implode(',',$ep_prod_ids).")";
	};
	if($ep_manu_name)
    {
		$ep_manu_ids = $db->getCol("enterprises",'eid',"ep_name LIKE '%$ep_manu_name%'");
		$where .= " AND ep_manu_id IN (".implode(',',$ep_manu_ids).")";
	}		
	
	//要获取的字段
	$fields .= "p.*,e.ep_name,t.plan_date,t.report_chuju_date";

	//要关联的表
	$join .= " LEFT JOIN sp_enterprises e ON e.eid = p.eid";
	$join .= " LEFT JOIN sp_test t ON t.id = p.test_id";


	$pd_type_total = array(0,0,0,0);
	//$where .= " AND p.status = 3 AND p.deleted=0 AND p.iso_prod_type = 1 AND p.redata_status=1";
	$where .= " AND p.deleted=0 AND p.iso_prod_type = 1 AND is_samp = 1";
	$test_org_id = current_user("id");
	$where .= "	AND p.test_org_id = '$test_org_id'";
	if( !$export ){
		$query = $db->query("SELECT p.test_review_status,COUNT(*) total FROM sp_project p WHERE 1 $where GROUP BY p.test_review_status");
		while( $rt = $db->fetch_array( $query ) ){
			$pd_type_total[$rt['test_review_status']] = $rt['total'];
		}
		$pages = numfpage( $pd_type_total[$pd_type] );
	}

	$where .= " AND p.test_review_status = $pd_type";
	
	$resdb = array();
	$sql = "SELECT $fields FROM sp_project p $join WHERE 1 $where ORDER BY p.id DESC $pages[limit]";
	//echo $sql;
	//exit;
	$query = $db->query( $sql );
	while( $rt = $db->fetch_array( $query ) ){
		$rt['audit_type_V'] = f_audit_type( $rt['audit_type'] );
		$rt['ep_prod_name'] = $db->getField('enterprises','ep_name',array('eid'=>$rt['ep_prod_id']));
		$rt['ep_manu_name'] = $db->getField('enterprises','ep_name',array('eid'=>$rt['ep_manu_id']));
		$rt['prod_name_chinese'] = $db->getField('contract_item','prod_name_chinese',array('cti_id'=>$rt['cti_id']));
		$resdb[$rt['id']] = $rt;
	}
		

	if( !$export ){
		tpl();
	} else {
		ob_start();
		tpl( 'xls/review' );
		$data = ob_get_contents();
		ob_end_clean();

		export_xls( '检测资料复核', $data );
	}