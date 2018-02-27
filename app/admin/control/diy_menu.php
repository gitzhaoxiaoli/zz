<?php
!defined('IN_SUPU') && exit('Forbidden');

/*
*个人中心->设置菜单
*
*
*
*/

$step = getgp( 'step' );
if( empty( $a ) || 'index' == $a ){  

	//@lyh 2016-03-21 获取本人登陆的菜单，UC个人中心 MAIN体系认证
	$diy_menu = $db->getField('hr','diy_menu',array('id'=>current_user('id')));
	${'jumptype_'.$diy_menu} = 'checked';

	//
	$menus = $items = array();
	$query = $db->query( "SELECT * FROM sp_user_menus WHERE uid = '".current_user('uid')."' and deleted=0 ORDER BY vieworder" );
	while( $rt = $db->fetch_array( $query ) ){
		if( 'menu' == $rt['mtype'] ){
			$menus[$rt['id']] = $rt;
		} else {
			isset( $items[$rt['parent_id']] ) or $items[$rt['parent_id']] = array();
			$items[$rt['parent_id']][$rt['id']] = $rt;
		}
	} 
	tpl( 'diy_menu' );
} elseif( 'add' == $a || 'edit' == $a ){
	$menu = load( 'menu' );
	$parent_id	= (int)getgp( 'parent_id' );

	if( $step ){
		$name		= getgp( 'name' );
		$vieworder	= (int)getgp( 'vieworder' );
		$jump		= getgp( 'jump' );
		$target		= getgp( 'target' );

		if( 'add' == $a ){
			if( $parent_id ){
				$menu->add_item( $parent_id, $name, $jump, $target, $vieworder );
			} else {
				$menu->add_menu( $name );
			}
		} else {
			$mid = (int)getgp( 'mid' );
			$new_item = array(
				'parent_id' => $parent_id,
				'name'		=> $name,
				'jump'		=> $jump,
				'target'	=> $target,
				'vieworder'	=> $vieworder
			);
			$menu->edit( $mid, $new_item );
		}

		showmsg( 'success', 'success', "?c=$c&a=index" );

	} else {
		$target_rightmain = $target__blank = '';
		if( 'edit' == $a ){
			$mid = (int)getgp( 'mid' );
			$row = $menu->get( array( 'id' => $mid ) );
			extract( $row, EXTR_SKIP );
			$parent_id = $row['parent_id'];

			${'target_'.$target} = 'selected';
		}



		tpl( 'diy_menu_edit' );
	}

} elseif( 'jumpset' == $a ){

	$jump = getgp( 'jump' );

	$db->update( 'hr', array( 'diy_menu' => $jump ), array( 'id' => current_user('id')) );

	showmsg( 'success', 'success', "?c=diy_menu" );
} elseif( 'del' == $a ){

	$parent_id = (int)getgp( 'parent_id' );	//父ID
	$mid		= (int)getgp( 'mid' );

	//删除当前链接 “菜单项”
	$db->update( 'user_menus', array( 'deleted' => 1 ), array( 'id' => $mid,'uid' => current_user('id')) );

	//如果选择是  “菜单”，同时删除该菜单下的全部 “菜单项”		//删除该组下全部链接
	if($parent_id==0){	
		$db->update( 'user_menus', array( 'deleted' => 1 ), array( 'parent_id' => $mid,'uid' => current_user('id')) );
	}

	showmsg( 'success', 'success', "?c=diy_menu" );

}
?>