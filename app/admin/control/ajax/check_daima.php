<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*组织代码检测
*/

$work_code = getgp('work_code');
    $total = $db->get_var("SELECT COUNT(*) FROM sp_enterprises WHERE work_code = '$work_code'");
    echo $total;
    exit();