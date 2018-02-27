<?php
!defined('IN_SUPU') && exit('Forbidden');
 
$et		= load( 'enterprise' );
$ct		= load( 'contract' );
$ctc	= load( 'cost' );
$ctcf	= load( 'finance' );
$audit  = load('audit');
$detail = load('contract_cost_detail');
//收费类型数组
$cost_type_arr = array(
                   'cost'         =>   '--请选择--',
				   'cost_first'   =>   '初次',
				   'cost_one'     =>   '监督',
				     );
 

$ctfrom_select = f_ctfrom_select();
$province_select = f_province_select();//省分下拉 (搜索用)

$audit_type_select=f_select('audit_type');
$iso_select=f_select('iso');
$currency_select=f_select('currency');

 
$status_arry = array('n'=>'未交完','y'=>'已交完');
if($status_arry){
	foreach( $status_arry as $code => $item ){
		$status_select .= "<option value=\"$code\">$item</option>";
	}
}

//引入模块控制下的方法
$action=CTL_DIR.$c.'/'.$a.'.php'; 
if(file_exists($action)){
	include_once($action); 
}else{
	echo '该方法不存在，请检查对应程序';
	echo '<br />方法名称：'.$a;	
} 


?>