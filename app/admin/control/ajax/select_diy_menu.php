<?php
!defined('IN_SUPU') && exit('Forbidden');


/*
*OA-选择内网链接
*@lyh 2016-03-22
*
*
*/

$diy_menu=array();

$op			= getgp( 'op' );
!$op && $op = 'main';
$left_nav	= $left_nav[$op];	



foreach( $left_nav as $key=>$nav ){
	if(is_array($nav['options'])){
		$options = array_values($nav['options']);
		foreach( $nav['options'] as $kk=>$item ){	
			if( $item[2]=='1'){
				if($_SESSION['userinfo']['username']=='admin' || (false !== strpos($_SESSION['userinfo']['sys'], urltoauth($item[1])))){
					$diy_menu['father'][]	=$nav['name'];
					$diy_menu['name'][]		=$item[0];
					$diy_menu['url'][]		=$item[1];
				}
			}
		}
	}
}



tpl();
?>