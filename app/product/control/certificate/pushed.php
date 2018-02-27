<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*应暂停项目列表
*/
	extract( $_GET, EXTR_SKIP );

	$fields = $join = $where = '';
	//企业名称
	$ep_name = trim($ep_name);
	if( $ep_name ){
		$where .= " AND e.ep_name like '%$ep_name%'";
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


	//企业编号
	if( $ep_code=trim($ep_code) ){
		$eid = $db->get_var("SELECT eid FROM sp_enterprises WHERE code = '$code' and deleted=0");
		if( $eid ){
			$where .= " AND p.eid = '$eid'";
		} else {
			$where .= " AND p.id < -1";
		}
	}

	//合同来源限制
	$len = get_ctfrom_level( current_user( 'ctfrom' ) );

	if( $ctfrom && substr( $ctfrom, 0, $len ) == substr( current_user( 'ctfrom' ), 0, $len ) ){
		$len =  get_ctfrom_level( $ctfrom );
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
	$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );
	unset( $len );


	if( $audit_type ){ //审核类型
		$where .= " AND p.audit_type = '$audit_type'";
	}
	if($fac_code=trim($fac_code)){
    $res = $db->find_one("enterprises",array('fac_code'=>$fac_code),"eid");
    $where .= "AND ep_prod_id = '$res[eid]'";
}


	if( $final_date_start ){ // 起
		$where .= " AND p.final_date >= '$final_date_start'";
	}
	if( $final_date_end ){ // 止
		$where .= " AND p.final_date <= '$final_date_end'";
	}

	if( $cti_code=trim($cti_code) ){ //合同项目编码
		
		$where .= " AND p.cti_code = '$cti_code'";
	}

 	if( $iso ){ //认证体系
		$where .= " AND p.iso = '$iso'";
	}


	$where .= " AND p.deleted = 0 AND p.iso_prod_type = 1";

	$day_date = date('Y-m-d');
	$join .= " left join sp_enterprises e on e.eid=p.ep_prod_id ";
	$where .= "  and p.status in('0','5') and final_date<'$day_date' and p.audit_type not in ('".join("','",$allow_type)."') ";

	if( !$export ){
		$total = $db->get_var("select COUNT(*) from sp_project p $join where 1 $where ");
		$pages = numfpage( $total );
	}
	$sql = "select e.ep_name,p.eid,p.cti_id,p.cti_code,p.ct_code, p.ctfrom,p.prod_id,p.final_date,p.status,p.audit_ver,p.audit_type from sp_project p $join where 1 $where  $pages[limit] ";
	$res = $db->query($sql);
	while($row = $db->fetch_array($res)){
		$sql = "select id as zsid ,certno,e_date from  sp_certificate where  cti_id='$row[cti_id]' AND deleted = 0 order by e_date desc limit 1";
		$c_info= $db->get_row($sql);//证书id，编辑的链接使用
		$c_info && $row=array_merge($c_info,$row);
		$datas[] = chk_arr($row);
	}
	if( !$export ){
		tpl('certificate/pushed');
	} else {
		ob_start();
		tpl( 'xls/list_pushed' );
		$data = ob_get_contents();
		ob_end_clean();

		export_xls( '应暂停项目', $data );
	}