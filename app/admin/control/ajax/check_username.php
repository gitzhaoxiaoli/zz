<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*检测用户账号是否重名
*/

$username = getgp('username');
    $uid = getgp('uid');
    $total = $db->get_var("SELECT COUNT(*) FROM sp_hr WHERE username = '$username' AND  id != '$uid'");
    echo $total;
    exit();