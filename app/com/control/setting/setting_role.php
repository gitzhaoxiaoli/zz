<?php
!defined('IN_SUPU') && exit('Forbidden');


//用户权限信息

$user_role=$db->find_one('settings',array('id'=>$_GET['id']));
//权限明细缓存
include CACHE_PATH . 'role_detail.cache.php';
	
if ($_POST) {
 
    $check_sys = getgp('check_sys');
  
  	//获取所有
  
    
    $sys       = @implode("|", $check_sys);
	$role_detail_array[$user_role['code']]=array('code'=>$sys,'name'=>$user_role['name'],'type'=>'role_detail','is_stop'=>$user_role['is_stop']); 
	
	 $cache_string = "<?php\r\n\$role_detail_array = " . sp_var_export($role_detail_array) . ";?>";
    file_put_contents(CACHE_PATH . 'role_detail.cache.php', $cache_string);
	
	
	 
 
}
//
 $user_role_name=$db->getField('settings','name',array('id'=>$_GET['id']));

//查询用户信息与权限列表 
//加载系统配置
$set_auth=get_conf('','set');
//加载左侧导航
include CONF.'menu.cqm.php'; 
$left_nav=array_merge($left_nav,$set_auth);
  
template('setting/setting_role');