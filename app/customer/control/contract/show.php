<?php
!defined('IN_SUPU') && exit('Forbidden');
$pd_types=array("未评定","通过","待定","不通过");
$t_status=array("","待派人","待审批","已审批");
$test_res=array("未检验","合格","需整改","不合格");
$cti_id = (int)getgp( 'cti_id' );
	if( $cti_id ){
		$cti = $db->find_one("contract_item",array("cti_id"=>$cti_id));
		$cti[ep_name] = $db->getField("enterprises","ep_name",array("eid"=>$cti[eid]));
		$cti[ep_manu] = $db->getField("enterprises","ep_name",array("eid"=>$cti[ep_manu_id]));
		$cti[ep_prod] = $db->getField("enterprises","ep_name",array("eid"=>$cti[ep_prod_id]));
		$ep_prod_id = $cti[ep_prod_id];
		$projects = array();
		$fields = $join = '';
		$fields .= "*";
		//$fields .= ",cert.certno,cert.status as c_status,cert.s_date,cert.e_date,cert.id as zsid";


		$join .= " LEFT JOIN sp_task t ON t.id = p.tid";
		// $join .= " LEFT JOIN sp_test t1 ON t1.id = p.test_id";
		$sql = "SELECT $fields FROM sp_project p  $join WHERE p.cti_id = '$cti_id' and p.deleted = 0 and p.iso_prod_type = 1";

		$query = $db->query( $sql );
		while( $rt = $db->fetch_array( $query ) ){
			$rt['audit_type'] = f_audit_type( $rt['audit_type'] );
			$rt['check_result'] = read_cache("check_result", $rt['check_result'] );
			$rt['tb_date'] = mysql2date( 'Y-m-d', $rt['tb_date'] );
			$rt['te_date'] = mysql2date( 'Y-m-d', $rt['te_date'] );
			$rt['auditor_tb_date'] = mysql2date( 'Y-m-d', $rt['auditor_tb_date'] );
			$rt['auditor_te_date'] = mysql2date( 'Y-m-d', $rt['auditor_te_date'] );
			$rt['tb_date'] = mysql2date( 'Y-m-d', $rt['tb_date'] );
			$rt['te_date'] = mysql2date( 'Y-m-d', $rt['te_date'] );
			$rt['pd_type']=$pd_types[$rt['pd_type']];
			$rt['is_qualified']=$test_res[$rt['is_qualified']];
			if($rt[tid]){
				$_query = $db->query("SELECT * FROM `sp_task_audit_team` WHERE `tid` = '$rt[tid]' AND `iso_prod_type` = '1' AND `deleted` = '0'");
				while( $_rt = $db->fetch_array( $_query ) ){
					if($_rt[role] == '1001')
						$rt[leader] = $_rt[name];
					else
						$rt[auditors][] = $_rt[name];
				}
				
				$rt['auditor']=join(" ",$rt[auditors]);
			}
			$cert_info=$db->get_row("SELECT cert.certno,cert.status as c_status,cert.s_date,cert.e_date,cert.id as zsid FROM `sp_certificate` cert WHERE `cti_id` = '$rt[cti_id]'  order by e_date desc");
			if($cert_info){
				$rt = array_merge($rt,$cert_info);
			}
			
			unset($cert_info);
			if($rt[zsid])
				$rt[change]=$db->get_results("select cg_type,cgs_date,cge_date,certpasue_value2,cg_reason,cg_af,cg_bf,note from sp_certificate_change where 1 AND zsid='$rt[zsid]' and deleted = 0");
			//p($rt[change]);
			$rt=chk_arr($rt);
			
			$projects[] = $rt;$rt=NULL;
		}
	}
	tpl( 'contract/show' );