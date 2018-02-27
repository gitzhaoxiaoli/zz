<?php

if ($step) {
    if (!$db->get_var("select eid from sp_enterprises where en_username='{$_POST['add']['en_username']}'")) {
        $_POST['add']['en_password'] = md5($_POST['add']['en_password']);
		
        $eid=load('enterprise')->add($_POST['add'], false);
        //日志
		$af_info = serialize(load('enterprise')->get($eid));
        log_add(0, $eid, "客户注册", NULL, $af_info);
        //showmsg('success', 'success', '?m=en_user&a=reg');
        showmsg('success', 'success', '?m=en_user&a=login');
    } else {
        showmsg('重复用户名！', 'error', '?m=en_user&a=reg');
    }
}
tpl("en_user/reg");