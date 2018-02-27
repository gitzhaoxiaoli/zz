<?php
!defined('IN_SUPU') && exit('Forbidden');

// $data = '[ ["January", 10], ["February", 8], ["March", 4], ["April", 13], ["May", 17], ["June", 9] , ["Out", 29] ,["Out1", 29] ,["Out2", 29] ,["Ou3", 29] ,["O4ut", 29] ]';

$query = $db->query("SELECT ep_amount FROM sp_enterprises WHERE deleted = 0 ");
$_data = array();
while ($rt = $db->fetch_array($query)) {
	if ($rt['ep_amount'] < 50) {
		$_data['<50'] ++; 
	} elseif ($rt['ep_amount'] >= 50 && $rt['ep_amount'] < 100) {
		$_data['<100'] ++;
	} elseif ($rt['ep_amount'] >= 100 && $rt['ep_amount'] < 500) {
		$_data['<500'] ++;
	} elseif ($rt['ep_amount'] >= 500 && $rt['ep_amount'] < 1000) {
		$_data['<1000'] ++;
	} else {
		$_data['>=1000'] ++;
	}
}
unset($query,$rt);
// p($_data);
// exit;
$data = array();
foreach ($_data as $k => $v) {
	$data[] = array('label' => $k , 'data' => $v);
}

// pe($data);
$data = json_encode($data , true);

tpl();