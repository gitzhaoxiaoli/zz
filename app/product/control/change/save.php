<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *保存变更
 */
$pid_radio  = getgp("pid_radio");
$zsid       = getgp("zsid");
$changeitem = getgp('changeitem');
$cg_af = getgp('cg_af');
$cg_bf = getgp('cg_bf');
// p($_POST);
// die;
$cg_type_report=array();
foreach ($changeitem as $key => $value) {
    
	if($value=='97_01'){
		$cert_value=getgp("certpasue_value");
		$ztdq=getgp("ztdq");
	}
	elseif($value=='97_03'){
		$cert_value=getgp("certrecall_value");
		}
	elseif($value=='97_04'){
		$cert_value=getgp("certlogout_value");
	}
	$cg_type_report[]="03".substr($value, 0, 2);
}
    $zs_info   = $certificate->get($zsid);
    $cg_date  = getgp('cg_date');
    $cg_reason  = getgp('cg_reason');
    $cg_pinfo  = $db->get_row("select id,audit_type from sp_project where cti_id='$zs_info[cti_id]' and iso_prod_type = 0 order by id desc limit 1");
    $default   = array(
        'zsid' => $zsid, //证书id
        'cg_pid' => $cg_pinfo[id],	//变更关联项目id
        'iso' => $zs_info['iso'], //体系
        'audit_type' => $cg_pinfo['audit_type'], //审核类型
        'audit_ver' => $zs_info['audit_ver'], //标准版本
        'ctfrom' => $zs_info['ctfrom'], //合同来源
        'cg_type' => join("|",$changeitem), //变更类型
        'cg_type_report' => join("|",$cg_type_report), //上报类型
        'cg_reason' => $cg_reason, //
        'ztdq' => $ztdq, //
        'csrt_status_a' => $zs_info[status], //
        'cert_value' => $cert_value, //暂停撤销原因
        'cg_meta' => $value, //变更字段
        'cg_af' => $cg_af, //变更前
        'cg_bf' => $cg_bf, //变更后
        'cg_date' => getgp('cg_date'), //变更日期
        'status' => '0', //状态
        'note' => getgp('note')
    );
    $change->add($default);

// 日志
do {
    log_add($_POST['eid'], 0, '证书变更', NULL, serialize($default));
} while (false);
showmsg('success', 'success', "?m=product&c=change&a=list&status=0&c=change");