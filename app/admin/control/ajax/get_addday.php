<?php
!defined('IN_SUPU') && exit('Forbidden');

/*
*计算注册到期
*/
$type = getgp("type");
if($type == 'pro'){
	
	$cti_id = getgp("cti_id");
	$cti = $db->get_row("SELECT ep_site_related,cert_form FROM `sp_contract_item` WHERE `cti_id` = '$cti_id'");
	extract($cti);
	if($ep_site_related == '1001')
		$e_date = $db->get_var("SELECT e_date FROM `sp_certificate` WHERE `certno` = '$cert_form' AND `deleted` = '0' AND `iso_prod_type` = '1'");
	else
		$e_date = get_addday(getgp('s_date') , 60 , -1);
	print_json(array(
        'day' => $e_date
		)
	);
}
$day = get_addday(getgp('s_date') , getgp('month') , getgp('day'));
    print_json(array(
        'day' => $day
));