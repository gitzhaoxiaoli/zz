<?php
!defined('IN_SUPU') && exit('Forbidden');

if($work_code=$_GET['work_code']){
	$res=$db->find_one('enterprises',array('work_code'=>$work_code));
	if(!$res[ep_name]){
		$_code=str_replace("-","",trim($work_code));
		$orgClass=getOrgInfo($_code);
		if($orgClass->message=='success'){
			$orgInfos=$orgClass->orgInfos;
			require( ROOT . '/data/cache/prod_type.cache.php' );
			require( ROOT . '/data/cache/nature.cache.php' );
			$new_prod_type = array();
			foreach($prod_type_array as $k=>$item){
				$new_prod_type[$item[name]] = $k;
			}
			$new_nature = array();
			foreach($nature_array as $k=>$item){
				$new_nature[$item[name]] = $k;
			}
		$b_name = str_replace("公司","",$orgInfos->businessTypeName);
		$res=array(
			'ep_name'		=>$orgInfos->orgName,
			'areacode'		=>$orgInfos->areaCode,
			'areaaddr'		=>$orgInfos->areaName,
			'delegate'		=>$orgInfos->legalName,
			'ep_addr'		=>$orgInfos->orgAddress,
			'capital'		=>$orgInfos->registeredCapital,
			'ep_addrcode'	=>$orgInfos->zipCode,
			'prod_type'		=>$new_prod_type[$b_name],
			'nature'		=>$new_nature[$orgInfos->businessTypeName],
			'work_code'		=>$work_code,
			);
		}
	}
}else{
	
	$res = array("status"=>"error");
}

print_json($res);




 