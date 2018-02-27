<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *保存变更审批
 */
$cgid = getgp('cgid');
if ($cgid) {
    $cg_info     = $change->get($cgid);
    $zs_info     = $certificate->get($cg_info['zsid']);
    if(strpos($cg_info[cg_type],"97_01")!==false)
		$status="02";
	elseif(strpos($cg_info[cg_type],"97_03")!==false)
		$status="03";
	elseif(strpos($cg_info[cg_type],"97_04")!==false)
		$status="04";
	else
		$status=$zs_info[status];
	if(strpos($cg_info[cg_type],"97")===false){
		$db->update('certificate', array(
			'is_change' => 1, //是否换证
			'change_date' => $cg_info['cgs_date'], //换证时间
			'is_check' => "n",
		), array(
			'id' => $cg_info['zsid']
		));
	}else
		$db->update('certificate', array(
			'status' => $status, //
		), array(
			'id' => $cg_info['zsid']
		));
	if(strpos($cg_info[cg_type],"97_01")!==false){
		$cgs_date=date("Y-m-d");
		$cge_date=thedate_add($cgs_date,$cg_info[ztdq],"month");
	}
	$cg_up = array(
			"status"			=> '1',
			"csrt_status_b"		=> $status,
			"pass_date"			=> date("Y-m-d"),
			"cgs_date"			=> $cgs_date,
			"cge_date"			=> $cge_date
			);
    $db->update("certificate_change",$cg_up,array("id"=>$cg_info[id]));
}
$REQUEST_URI = '?m=product&c=change&a=list&status=1';
showmsg('success', 'success', $REQUEST_URI);