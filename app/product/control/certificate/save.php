<?php
!defined('IN_SUPU') && exit('Forbidden');


//保存证书
// exit(p($_POST));
  	$certno = getgp("certno");
  	$zsid = getgp("zsid");
  	$pid = getgp("pid");
  	$cti_id = getgp("cti_id");
	//判断证书号是否重复
  	$sql = "select * from sp_certificate where id != '$zsid' and certno='$certno' and status in ('01','02')   AND deleted=0";
	$info = $db->get_row($sql);
	if($info){
		echo "<script type='text/javascript'>alert('证书号码不能重复！');history.go(-1);</script>";die();
	}
	

		$default = $_POST;
		unset($default[zsid]);
		
		if($zsid){
			$certificate->edit($zsid, $default);
			$default = $certificate->get(array("id"=>$zsid));
			}
		else{
			$sort = abs(substr($certno,-6));
			if($pid){
				$p_info=$db->find_one("project",array("id"=>$pid));
				$new_cert = array(
						'eid'			=> $p_info['eid'],	//
						'ep_manu_id'	=> $p_info['ep_manu_id'],	//
						'ep_prod_id'	=> $p_info['ep_prod_id'],	//
						'ct_id'			=> $p_info['ct_id'],	//合同id
						'cti_id'		=> $p_info['cti_id'],	//合同项目id
						'pid'			=> $p_info['id'],	//
						'iso'			=> $p_info['iso'],	//体系
						'ct_code'	=> $p_info['ct_code'],	//
						'cti_code'	=> $p_info['cti_code'],	//
						'ctfrom'	=> $p_info['ctfrom'],	//
						'prod_id'	=> $p_info['prod_id'],	//
						'sort'	=> $sort,	//
						'iso_prod_type'	=> 1,	//
						'status'	=> "01",	//
								);	
				$default=array_merge($default,$new_cert);
				// exit(p($default));
				$db->update("progress",array(	
								"step12"	=> date("Y-m-d H:i:s"),
								"status"	=> "12",
							),array("pid"=>$pid));
			}
			$zsid = $certificate->add($default);
			
		}		
		$db->update("project",array("ifchangecert"=>2),array("id"=>$pid));
		$db->update("contract_item",array("prod_name_chinese"=>$prod_name_chinese,"prod_name_english"=>$prod_name_english,"scope"=>$cert_scope,"scope_e"=>$cert_scope_e),array("cti_id"=>$cti_id));
		$db->update("enterprises",array("ep_name_e" => $cert_name_e,"ep_addr_e" => $cert_addr_e),array("eid" => $default[eid]));//委托人
		$db->update("enterprises",array("ep_name_e" => $manu_name_e,"ep_addr_e" => $manu_addr_e),array("eid" => $default[ep_manu_id]));//生产者
		$db->update("enterprises",array("ep_name_e" => $pro_name_e,"ep_addr_e" => $pro_addr_e),array("eid" => $default[ep_prod_id]));//生产企业
		$sms_arr=array("eid"=>$eid,
						"temp_id"=>$zsid,
						"flag"=>1);
		 
		
	
	$sms=load("sms");
	$sms_info=$sms->get(array("temp_id"=>$sms_arr[temp_id],"flag"=>1));
	if($sms_info[id])
		$sms->edit($sms_info[id],$sms_arr);
	else
		$sms->add($sms_arr);
			//跳转页面
		$REQUEST_URI='?m=product&c=certificate&a=list';

	showmsg( 'success', 'success', $REQUEST_URI );