<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *证书登记
 */
$is_change_select = '<option value="0">否</option><option value="1">是</option>';

function chns_auro_no($iso, $prod_id)
{
	global $db;
	$sort = $db->get_var("SELECT MAX(sort) FROM sp_certificate WHERE deleted = 0 AND iso ='$iso'");
	$sort ++;
	$sort = sprintf("%06d",$sort);
	$no = "";
    if($iso == "B01"){
		$no = date("Y")."17".$prod_id.$sort;
		
	}else{
		$no = "CTC".date("y").$prod_id.$sort;
	}
    return $no;
}
// 证书新登记
	if($pid=getgp("pid")){
		$f = 1;
		$p_info=$db->find_one("project",array("id"=>$pid));
		
		//已有证书信息列表
		$sql = "select * from sp_certificate where ep_prod_id='$p_info[ep_prod_id]' and status >0 and iso='$p_info[iso]' AND deleted = 0 order by e_date desc";
		$res = $db->query($sql);
		$certs = array();
		while($r = $db->fetch_array($res)){
			$r['status'] = f_certstate($r['status']);
			$certs[] = $r;
		}

		// 判断是否变更
		$cti = $db->find_one("contract_item",array("cti_id"=>$p_info[cti_id]));
		if($cti['app_type'] =='1'){
			$c_info = $cti = $db->find_one("change_app",array("cti_id"=>$p_info[cti_id]));
			$zs_info=$db->find_one("certificate",array("id"=>$c_info['zsid']));
			extract($zs_info);
			$is_change_select = str_replace( "value=\"1\">", "value=\"1\" selected>" , $is_change_select );
			$old_certno = $zs_info[certno];
			$change_date = $s_date = $p_info[sp_date];
			$is_change = 1;
			$zsid = $id;
			tpl();
			exit;
		}else{
			$zs_info=$db->find_one("certificate",array("cti_id"=>$p_info['cti_id'],"status"=>'01',"deleted"=>0));

		}
		// exit(p($zs_info));

		if($zs_info){
			
			$f = 0;
			tpl();
			exit;
			
		}
		$e_info=$db->find_one("enterprises",array("eid"=>$p_info[eid]));//委托人
	    
		 $cert_name = $e_info['ep_name'];
		 $cert_name_e = $e_info['ep_name_e'];
		 $cert_addr = $e_info['ep_addr'];
		 $cert_addr_e = $e_info['ep_addr_e'];
		
		$ep_manu = $db->find_one('enterprises',array('eid'=>$p_info['ep_manu_id']));//生产者
		 $manu_name = $ep_manu['ep_name'];
		 $manu_name_e = $ep_manu['ep_name_e'];
		 $manu_addr = $ep_manu['ep_addr'];
		 $manu_addr_e = $ep_manu['ep_addr_e']; 
		  
		$ep_prod = $db->find_one('enterprises',array('eid'=>$p_info['ep_prod_id']));//生产企业
		 $pro_name = $ep_prod['ep_name'];
		 $pro_name_e = $ep_prod['ep_name_e'];
		 $pro_addr = $cti['ep_prod_addr'];
		 $pro_addr_e = $cti['ep_prod_addr_e'];
		 
	    $cert_scope  = $cti['scope'];//产品型号
	    $cert_scope_e  = $cti['scope_e'];//产品型号
		$prod_ver   = $cti['prod_ver'];//产品标准
		$prod_id   = $cti['prod_id'];//产品小类
		$prod_name_chinese=$cti['prod_name_chinese'];//产品名称
		$prod_name_english=$cti['prod_name_english'];
		
		$certno = chns_auro_no($cti[iso], $prod_id );
		$s_date = $p_info['sp_date'];
		$e_date = get_addday($p_info['sp_date'], 60,-1);
		$_cert = $db->get_row("SELECT s_date,first_date FROM `sp_certificate` WHERE `eid` = '$p_info[eid]' AND `deleted` = '0' AND `iso_prod_type` = '1' AND `prod_id` = '1001' order by e_date ");
		if($_cert[first_date] && $_cert[first_date] != '0000-00-00')
			$first_date = $_cert[first_date];
		else
			$first_date = $_cert[s_date];
		tpl();
		exit;
	}
	if($zsid){
		
		$row = $certificate->get($zsid);
		extract(chk_arr($row), EXTR_OVERWRITE );
		$certreplace_select = str_replace( "value=\"$change_type\">", "value=\"$change_type\" selected>" , $certreplace_select );
		$is_change_select = str_replace( "value=\"$is_change\">", "value=\"$is_change\" selected>" , $is_change_select );
		//已有证书信息列表
		$sql = "select * from sp_certificate where ep_prod_id='$ep_prod_id' and status >0 and iso='$iso' AND deleted = 0 order by e_date desc";
		$res = $db->query($sql);
		$certs = array();
		while($r = $db->fetch_array($res)){
			$r['status'] = f_certstate($r['status']);
			$certs[] = $r;
		}
		$f = 1;
		tpl();
	}
	
	
	
	