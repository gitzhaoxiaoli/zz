<?php
!defined('IN_SUPU') && exit('Forbidden');

// $data = '[ ["January", 10], ["February", 8], ["March", 4], ["April", 13], ["May", 17], ["June", 9] , ["Out", 29] ,["Out1", 29] ,["Out2", 29] ,["Ou3", 29] ,["O4ut", 29] ]';

$query = $db->query("SELECT LEFT(areacode , 2) pcode , COUNT(*) total FROM sp_hr WHERE deleted = 0 AND is_hire = 1 AND job_type like '%1004%' GROUP BY pcode");
$data = "[";
while ($rt = $db->fetch_array($query)) {
	$data .= '[ "' . f_region_province($rt['pcode']) . '",' . $rt['total'] . '],';
}
$data .= "]";
unset($query,$rt);

tpl();