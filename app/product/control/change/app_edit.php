<?php
!defined('IN_SUPU') && exit('Forbidden');
 require_once( ROOT . '/data/cache/app_change.cache.php' );//变更类别
 //p($app_change_array);
 

// 变更类别
if($app_change_array){
	$changeitem_li = '';
	foreach($app_change_array as $key=>$value){
		$changeitem_li .= "<li><label><input type='checkbox' name='type[]' value='$key'  />".$key.'.'.$value['name']."</label></li>";
		
	}
} 

$id=$_GET['id'];


if($_POST){

           $_POST['type']=serialize($_POST['type']);
		   if($_POST[status]){
			   $_POST[sp_user] = current_user("name");
			   $_POST[sp_date] = date("Y-m-d");
			   
		   }
           if($id){
            $db->update("change_app",$_POST,array('id'=>$id));

           }else{

			$db->insert("change_app",$_POST);
	 }
	showmsg("success","success","?m=product&c=change&a=app_list");
}else{



$res=$db->find_one('change_app',array(
       'id'=>$id
));



$num=unserialize($res['type']);
if(!empty($num)){
foreach ($num as $key => $value) {
	//$changeitem_li=str_replace("value='8'", "value=8 checked", $changeitem_li);
	$changeitem_li=str_replace("value='$value'", "value=$value checked", $changeitem_li);
}
}
	tpl();

}



?>