<?php

$hostdir="uploads/files";
$filenames=showArray(showFile($hostdir));
$arr=array_keys($filenames);
function printArray($filenames,$arr){
	foreach($filenames as $k=>$item){
		if(!is_numeric($k))
			if(in_array($k,$arr))
				echo "<li><span class='red'>".$k."</span></li>";
			else
				echo "<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$k."</li>";
		if(is_array($item)){
			echo printArray($item);
			}
		else
			if(!strpos($item,'Thumbs.db') and !strpos($item,'~$'))
			echo "<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='$item'><span class='blue'>".str_replace("/","",strrchr($item,"/"))."</span></a></i>";
		



	}
}
tpl("file_list");

?>