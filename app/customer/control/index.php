<?php
!defined('IN_SUPU') && exit('Forbidden');
$left_array = $left_nav['en_enterprise']['en_application']['options'];
$hostdir="data/wordTpl";
$filenames=showArray(showFile($hostdir));
foreach($filenames as $v){
	$name = str_replace("/","",strrchr($v,"/"));
	$left_array[] = array($name,$v,1);
}
// p($left_array);
// p($filenames);
// exit;
tpl($a);
?>