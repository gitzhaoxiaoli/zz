<?php
!defined('IN_SUPU') && exit('Forbidden');
/**
 * 微信 发送信息
 */

require_once "framework/fun.function.php";
$uid = getgp("uid");
if($_POST){
    $uids = explode("|", $uid);
    
    $content = $_POST['message'];
    $res = sendWmsg($uids,$content);
    if($res)
        echo "<script type='text/javascript'>alert('发送信息成功！');</script>";
    else{
         echo "<script type='text/javascript'>alert('发送信息失败！');</script>";
    }
    //showmsg("success","success","?c=wchat&a=wlist");
}

tpl();