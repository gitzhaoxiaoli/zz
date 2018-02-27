<?php
// $s = "app/product/control/word/070401-02.php";
// echo str_replace(array("/",".php"),"",strrchr($s,"/"));
// exit;
$hostdir="app/product/view/word";
$filenames=showArray(showFile($hostdir));
$pid = 1;
echo "<ul>";
foreach($filenames as $k){
	// $a = str_replace(array("/",".php"),"",strrchr($k,"/"));
	// echo "<li><a href = '?m=product&c=word&a=$a&pid=$pid' target='_blank'>$a</a></li>";
	echo "<li>$k</li>";
}
echo "</ul>";
// $a = showFile($hostdir);
// p($a);
// p($filenames);
// exit;

?>