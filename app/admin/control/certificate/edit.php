<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *证书登记
 */
 /**
 * 产生CNAS证书编号 流水号部分为*号
 * 06913Q12270R0S
 03213S20123R1M
 03214E2026R0M
 03214Q20025R0M
 * 1-3位069机构号 4-5位13年份 6-7位Q1版本号 891011位2270流水号 后三位R是固定值0复评次数S体系人数规模(sml小中(50<中的<=1000人)大)
 * @param string $iso 体系
 * @param int $renum 复评次数
 * @param int $total 体系人数
 * @param int $code   流水号
 * @return string
 */
function chns_auro_no($iso, $renum, $total, $code)
{
    $iso2ver = array(
        'A10' => 'IP',
    );
    $no      = '';
    $no .= substr(get_option('zdep_id'), -3);
    $no .= $iso2ver[$iso];
    $no .= date('y');
	$code ++ ;
	$code = '0***';//.sprintf("%04d", $code);
    $no .= $code;
    $no .= 'R';
    $no .= $renum;
   if ($total <= 50) {
        $no .= 'S';
    } elseif ($total > 1000) {
        $no .= 'L';
    } else {
        $no .= 'M';
    }
    return $no;
}

//换证原因
	if($pid=getgp("pid")){
		$p_info=$db->find_one("project",array("id"=>$pid));
		$e_info=$db->find_one("enterprises",array("eid"=>$p_info[eid]));
		$zs_info=$db->find_one("certificate",array("cti_id"=>$p_info['cti_id'],"eid"=>$p_info['eid'],"status"=>'01',"deleted"=>0));
	}
	$f=0;
	if($zsid)
		$f=1;
	else{
		!$zs_info && $f=1;
	}
	if($f){//添加和修改
		if($zsid){
			$row = $certificate->get($zsid);
			extract(chk_arr($row), EXTR_OVERWRITE );
		}else{
			$new_cert = array(
							'eid'			=> $p_info['eid'],	//企业id
							'ct_id'			=> $p_info['ct_id'],	//合同id
							'cti_id'		=> $p_info['cti_id'],	//合同项目id
							'iso'			=> $p_info['iso'],	//体系
							'audit_ver'		=> $p_info['audit_ver'],	//体系版本
							'mark'			=> $p_info['mark'],	//标志
							'audit_code'	=> $p_info['audit_code'],	//审核代码
							'cert_name'		=> $e_info['ep_name'],
							'cert_name_e'	=> $e_info['ep_name_e'],
							'cert_scope' 	=> $p_info['scope'],
							'cert_scope_e' 	=> $p_info['scope_e'],
							//'s_date' 	=> $p_info['sp_date'],
						);
			extract($new_cert, EXTR_OVERWRITE );
		}
		if(empty($certno)) {
			
			$renum = $db->get_var("select renum from sp_contract_item where cti_id='$cti_id' ");
			$total = $db->get_var("select total from sp_contract_item where cti_id='$cti_id' ");
			//$iso = $db->get_var("select iso from sp_certificate where id='$zsid' ");
			$year=date('Y');
			$ss = substr(get_option('zdep_id'),-3);
     		$sort=$db->get_var("SELECT MAX(sort) FROM sp_certificate WHERE certno NOT LIKE '%-%' AND LEFT(s_date,4) = '$year'");
			$certno = chns_auro_no($iso, $renum, $total,$sort);
				
		}

		//取总经理审批时间
		if($s_date=='0000-00-00' || !$s_date){
			$pd_info = $db->get_row("select sp_date from sp_project where id='$pid' ");
			$s_date = $pd_info['sp_date'];
			$e_date = get_addday($pd_info['sp_date'], 36,-1);
			$first_date = '';
		}
		$e_ct_id = $ct_id;
		//$tid = $db->get_var("select tid from sp_assess where id='$pd_id' ");

		//子证书的父id
		$parent_id = getgp('parent_id');
		if( $parent_id ){
			$where = " AND eid = '$parent_id'";
			$main_certno = $certno;
			$certno = '';
			$old_eid = $eid;
			$new_eid = $parent_id;
			$is_check = 'e';
			$cert_addr=NULL;
			$cert_addr_e=NULL;
			$cert_scope=$db->get_var("SELECT scope FROM `sp_contract_num` WHERE `eid` = '$parent_id' and ct_id='$ct_id' and type='1'");

		} else {
			$old_eid = $new_eid = $eid;
			$where = " AND eid = '$eid'";
		}
		$en_info = $db->get_row("select * from sp_enterprises where 1 $where");
		extract($en_info , EXTR_SKIP);
		if($report_date=='0000-00-00'  || !$report_date){
			$report_date = date("Y-m-d");
		}
		if($change_date=='0000-00-00'  || !$report_date){
			$change_date = '';
		}
		 
	}else{
	extract($p_info);
	
	}
	//显示子证书 应急证书
	if($is_check=='y') $show_en=true;

	$old_zsid = $new_zsid = $zsid;

	//证书状态： 已登记，未登记，未登记完
	if($is_check=='e'){
		$str_check = "<input type='hidden' name='old_check'  value='$is_check'/><input type='checkbox' name='is_check'  value='y'/>已保存&nbsp;";
	}else if($is_check=='n'){
		$str_check = "<input type='hidden' name='old_check'  value='$is_check'/><input type='checkbox' name='is_check' value='e'/>已保存&nbsp;";
	}else if($is_check=='y'){
		$str_check = "<input type='hidden' name='old_check'  value='$is_check'/><input type='hidden' name='is_check' value='y'/>";
	}

	if(getgp('parent_id')){
		$str_check = NULL;//不出现是否登记完的复选框
	}

	if($is_check=='y'){
		$sql = "select eid,ep_name from sp_enterprises where parent_id='$eid' and deleted = '0'";
		$res = $db->query($sql);
		$sub_certs = array();
		while($p_info = $db->fetch_array($res)){
			$sub_certs[] = $p_info;
		}
	}

	//已有证书信息列表
	$sql = "select * from sp_certificate where eid='$eid' and status >0 and iso='$iso' order by e_date desc";
	$res = $db->query($sql);
	$certs = array();
	while($p_info = $db->fetch_array($res)){
		$p_info['status'] = f_certstate($p_info['status']);
		$certs[] = $p_info;
	}
	$cti=$db->get_row("SELECT * FROM `sp_contract_item` WHERE `cti_id` = '$cti_id'");
	if($cti[is_turn]){
		$is_change=1;
		$old_certno=$cti['old_cert_no'];
		$old_cert_name=$cti['cacn_code'];
		$change_type="03";
		
		
	}	
	$certreplace_select = str_replace( "value=\"$change_type\">", "value=\"$change_type\" selected>" , $certreplace_select );
	$is_change_select = '<option value="0">否</option><option value="1">是</option>';
	$is_change_select = str_replace( "value=\"$is_change\">", "value=\"$is_change\" selected>" , $is_change_select );


	    
	tpl();