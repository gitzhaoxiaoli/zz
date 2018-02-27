<?php
//组织关系日志功能 
class org_log{
	
	//保存日志
	function save(){
		global $db; 
		//新增日志
		$data=$_POST['data']; 
		foreach($data as $org=>$notes){
			//计算组织关系
			$tmp_org=explode('-',$org);
			$new['app_id']=$tmp_org[0];
			$new['manu_id']=$tmp_org[0];
			$new['fac_id']=$tmp_org[0];
			$new['cerate_uid']=nowUsr('uid'); //创建人
			$new['add_uname']=nowUsr('username');
			$new['cerate_date']=nowTime('mysql'); //创建时间
 			foreach($notes as $note){
				if(!$note)continue;
				$new['log_note']=$note; 
				$db->insert('org_log',$new); 
				 
			} 
		} 
		//修改日志
		$old=$_POST['org_log_old'];
		foreach($old as $log_id=>$note){ 
			$db->update('org_log',array('log_note'=>$note),array('log_id'=>$log_id));
		}
		 
		//修改日志 
	}
	function delete(){
		global $db;
		$db->update('org_log',array('deleted'=>'1'),array('log_id'=>$_GET['log_id']));
		
	} 
}
//执行操作
org_log::$a();

  echo "<script>history.go(-1);</script>";
 