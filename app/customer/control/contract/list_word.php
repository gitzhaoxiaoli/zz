<?php
!defined('IN_SUPU') && exit('Forbidden');
$hostdir="data/wordTpl";
$filenames=showArray(showFile($hostdir));
$a = showFile($hostdir);
echo "<h2>WORD列表</h2>";
p($filenames);
echo "<ul>";
foreach($filenames as $item){
echo "<li><a href='$item'><span class='blue'>".str_replace("/","",strrchr($item,"/"))."</span></a></li>";
}
echo "</ul>";
// tpl();