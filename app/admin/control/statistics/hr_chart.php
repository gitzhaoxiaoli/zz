<?php
!defined('IN_SUPU') && exit('Forbidden');

// $data = '[ ["January", 10], ["February", 8], ["March", 4], ["April", 13], ["May", 17], ["June", 9] , ["Out", 29] ,["Out1", 29] ,["Out2", 29] ,["Ou3", 29] ,["O4ut", 29] ]';

$query = $db->query("SELECT iso , LEFT(audit_code , 2) code , COUNT(*) total  FROM sp_hr_audit_code  WHERE   deleted = 0 GROUP BY code");
$_data = array();
while ($rt = $db->fetch_array($query)) {
	$_data[f_iso($rt['iso'])][$rt['code']] = array($rt['code'] , $rt['total']);
}
unset($query,$rt);
// p($_data);
// exit;

foreach ($_data as $iso => $val) {
	$str = "[";
	foreach ($val as $v) {
		$str .= "[" . join(",",$v) . "],";
	}
	$str .= "]";
	$data[$iso] = $str;
}

// pe($s);
// $data = '[[14,2],[17,3],[29,100],[33,7],[36,1],[38,2]]';
tpl();