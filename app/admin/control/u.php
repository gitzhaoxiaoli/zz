<?php

@exec("arp -a",$array); //执行arp -a命令，结果放到数组$array中 
p($array);
p($_SERVER["REMOTE_ADDR"]);
foreach($array as $value){ 
//匹配结果放到数组$mac_array 
if(strpos($value,$_SERVER["REMOTE_ADDR"]) && preg_match("/(:?[0-9A-F]{2}[:-]){5}[0-9A-F]{2}/i",$value,$mac_array)){ 
$mac = $mac_array[0]; 
break; 
} 
} 
echo $mac; 



?>