<?php
!defined('IN_SUPU') && exit('Forbidden');
//@HBJ 2013-9-18 手动运行任务  
$run_path=CTL_DIR.'corn/'."{$_REQUEST['run_script']}.php";
if (file_exists($run_path)) {
    require_once($run_path);
    echo '已经手动运行：' . $_REQUEST['run_script'];
	showmsg('success', 'success', '?app=com&m=sys&a=list');
} else {
    echo '任务不存在';
}