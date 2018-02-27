<?php
// 认证方案 编辑  @cyf 2016-5-4

$tid = getgp("tid");
$ep_prod_id = getgp("ep_prod_id"); //生产企业ID

if($_POST){

	// $_POST 提交
	$is_samp = array_map ( 'intval', getgp ( 'is_samp' ) );
	$is_check = array_map ( 'intval', getgp ( 'is_check' ) );
	$st_num = array_map ( 'trim', getgp ( 'st_num' ) );
	$protocol_status = getgp ( 'protocol_status' );
// p($protocol_status);
	$p_idarr = $_POST['p_idarr'];
	foreach ($p_idarr as $key => $value) {
		$up_project = array(
			"protocol_status" =>$protocol_status,
			"is_samp"=>$is_samp[$value],
			"is_check"=>$is_check[$value],
			"st_num"=>$st_num[$value],
		);
		// p($up_project);die;
		if($p_idarr){
			$db->update("project",$up_project,array('id'=>$value));
		}
	}
	showmsg("success","success","?m=product&c=contract&a=protocol_list");
}else{

	// 说明:当任务号不为空的时候取同一个TID信息 当任务号为空的时候取生产企业的ID 批量操作
	// @zys 2016-5-12
	if($tid){
		$where = " AND p.tid = '$tid'";
	}else{
		$where = " AND p.ep_prod_id = '$ep_prod_id' AND p.tid = 0";
	}
	// 说明:sp_contract_item这个表暂时没有用到
	$query = $db->query("SELECT * FROM `sp_contract_item` i LEFT JOIN sp_project p ON i.cti_id=p.cti_id WHERE 1 $where AND i.deleted= '0' ");
	
	while($rt = $db->fetch_array($query)){
		$rt['ep_prod_name'] = $db->getField('enterprises','ep_name',array('eid'=>$rt['ep_prod_id']));
		$reb[]=$rt;
	}
// P($reb);
tpl();
	
}
		
