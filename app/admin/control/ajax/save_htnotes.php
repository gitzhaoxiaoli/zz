<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*保存合同回访记录
*/
    // 提交
    // p($_POST);die;
    // p($_SERVER);die;
    $new_htnotes = array('eid'=>$_POST['eid'],'note'=>$_POST['htnotes']);
    $db->insert('enterprises_note', $new_htnotes);
         // 提示 类型 跳转地址
    showmsg('success','success',$_SERVER['HTTP_REFERER'].'#tab-htnotes');