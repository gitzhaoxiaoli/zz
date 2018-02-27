<?php
//编译数据
//工厂模式：计算组织关系数量

if($args['org_logs']){ 
	$org_logs=$args['org_logs']; 
}

//读取数据库中的数据
foreach($org_logs as $v){
	$tmp_org=explode('-',$v);
	
	$items[$v]=$db->get_results("select * from sp_org_log WHERE app_id='$tmp_org[0]' AND manu_id='$tmp_org[1]' AND fac_id='$tmp_org[2]' AND  deleted=0");
	
	
	 
	
}

 

 
