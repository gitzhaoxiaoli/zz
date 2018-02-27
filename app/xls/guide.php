<?php
//@zys 2016-07-07
//企业信息导出
set_time_limit(0);
!defined('IN_SUPU') && exit('Forbidden');
require_once ROOT.'/data/cache/audit_ver.cache.php';

$data_arr= array(
    array(
	    '企业ID',
		'企业名称',
		'企业地址',
        '项目编号',
        '申请日期',
        '合同登记日期',
        '受理日期',
        '初次现场审核人日',
        '初次现场审核日期',
        '初次审核费用',
        '初次审核组长',
        '初次发证日期',
		'初次证书编号',
		'初次快递编号',
		'初次换证日期',
		'初次换证快递编号',
		'一监审核费用',
		'一监现场审核日期',
		'一监审核组长',
		'一监审核合格日期',
		'二监审核费用',
		'二监现场审核日期',
		'二监审核组长',
		'二监审核合格日期',
		
    )
    
);
$sql="select id,ct_id,cti_id,cert_name,s_date,certno,change_date,cert_addr,cti_code from sp_certificate where deleted='0' and status in(01,02)";
// echo $db->sql;die;
$query=$db->query($sql);
while($data=$db->fetch_array($query)){

	//申请日期--(合同登记企业申请日期)
	$data_ht=$db->get_row("SELECT ct_id,accept_date,create_date,approval_date from sp_contract WHERE ct_id='$data[ct_id]' AND deleted='0'");
	// p($data_ht);
	// 二阶段
	$data_p1=$db->get_row("SELECT id,tid,st_num from sp_project WHERE cti_id='$data[cti_id]' AND audit_type = '1003' AND deleted='0'");
	// p($data_p1);die;
	$data_t1=$db->get_row("SELECT tb_date from sp_task WHERE id='$data_p1[tid]' AND deleted='0'");

	$data_c1=$db->get_row("SELECT ct_id,cost from sp_contract_cost WHERE ct_id='$data[cti_id]' AND cost_type='1001' AND deleted='0'");
	// p($data_c1[cost]);
	// p($data_c1);
	$data_te1=$db->get_row("SELECT name from sp_task_audit_team WHERE pid ='$data_p1[id]' AND role='1001' AND deleted='0'");
	// p($data_te1[name]);


	// 监督一
	$data_p2=$db->get_row("SELECT id,tid,st_num,sp_date from sp_project WHERE cti_id='$data[cti_id]' AND audit_type = '1004-1' AND deleted='0'");

	$data_t2=$db->get_row("SELECT tb_date from sp_task WHERE id='$data_p2[tid]' AND deleted='0'");
	// p($data_t2);die;
	$data_c2=$db->get_row("SELECT cost from sp_contract_cost WHERE ct_id='$data[cti_id]' AND cost_type='1002' AND deleted='0'");

	$data_te2=$db->get_row("SELECT name from sp_task_audit_team WHERE pid ='$data_p2[id]' AND role='1001' AND deleted='0'");



	// 监督二
	$data_p3=$db->get_row("SELECT id,tid,st_num,sp_date from sp_project WHERE cti_id='$data[cti_id]' AND audit_type = '1004-2' AND deleted='0'");

	$data_t3=$db->get_row("SELECT tb_date from sp_task WHERE id='$data_p3[tid]' AND deleted='0'");

	$data_c3=$db->get_row("SELECT cost from sp_contract_cost WHERE ct_id='$data[cti_id]' AND cost_type='1003' AND deleted='0'");


	$data_te3=$db->get_row("SELECT name from sp_task_audit_team WHERE pid ='$data_p3[id]' AND role='1001' AND deleted='0'");


	$data_arr[] = array(
	    $data[id],
		$data[cert_name],
		$data[cert_addr], // 地址
		$data[cti_code], // 项目编号
		$data_ht[approval_date], // 申请日期
		$data_ht[create_date], // '合同登记日期',
		$data_ht[accept_date], // '受理日期',
		$data_p1[st_num], // '初次现场审核人日',
		$data_t1[tb_date], // '初次现场审核日期',
		$data_c1[cost], // '初次审核费用',--------------------------
		$data_te1[name], // '初次审核组长',
		$data[s_date], //'初次发证日期',
		$data[certno], //'初次证书编号',
		'', //'初次快递编号',----------------------

		$data[change_date], //'初次换证日期',
		'', //'初次换证快递编号', -------------------
		$data_c2[cost], //'一监审核费用', ------------------
		$data_t2[tb_date], // '一监现场审核日期',
		$data_te2[name], // '一监审核组长',
		$data_p2[sp_date], // '一监审核合格日期',

		$$data_c3[cost], // '二监审核费用', -------
		$data_t3[tb_date], // '二监现场审核日期', ------
		$data_te3[name], // '二监审核组长', ----
		$data_p3[sp_date], // '二监审核合格日期',----
		
    );

}
// p($data_arr);
echo export_excel($data_arr,"企业信息表");
exit;
?>
