<?php
!defined('IN_SUPU') && exit('Forbidden');
//word控制器文件名与方法名一致 
//业务需求：每个企业的导出模板基本都不一致，但是控制器类似

$ctl_doc= DOCTPL_PATH .'control/' .$a.'.php';
if(file_exists($ctl_doc)){
	require_once $ctl_doc; 
}else{
	echo '导出word控制器不存在';
	echo $ctl_doc;	
} 