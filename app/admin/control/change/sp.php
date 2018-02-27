<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *保存变更审批
 */
$cgid = getgp('cgid');
if ($cgid) {
    $cg_info     = $change->get($cgid);
    $zs_info     = $certificate->get($cg_info['zsid']);
    $zsid        = $zs_info['id'];
    $eid         = $zs_info['eid'];
    $change_name = "";
    switch ($cg_info['cg_meta']) {
        case '01':
            $db->update('enterprises', array(
                'ep_name' => $cg_info['cg_bf'],
                'ep_oldname' => $cg_info['cg_af']
            ), array(
                'eid' => $eid
            ));
            $change_name = "cert_name";
            break;
        case '02':
            $change_name = "cert_addr";
            //软件操作还要修改企业地址
            break;
        case '03':
        case '04':
        case '05':
        case '06':
        case '08':
            break;
        case '97_01':
        case '97_02':
        case '97_03':
        case '97_04':
            $db->update('certificate', array(
                'status' => $cg_info['cg_bf']
            ), array(
                'id' => $zsid
            ));
            break;
    }
    if ($change_name){
        $db->update('certificate', array(
            $change_name => $cg_info['cg_bf'],
            'is_change' => 1, //是否换证
            'change_date' => $cg_info['cgs_date'], //换证时间
            'is_check' => 'n'
        ), array(
            'id' => $zsid
        ));
        $db->update("project",array("ifchangecert"=>1),array("id"=>$cg_info[cg_pid]));
    }
		//更改变更状态
    $sql = "UPDATE sp_certificate_change SET status='1', pass_date='" . mysql2date('Y-m-d', current_time('mysql')) . "' WHERE id = '$cgid'";
    $db->query($sql);
}
$REQUEST_URI = '?c=change&a=list&status=1';
showmsg('success', 'success', $REQUEST_URI);