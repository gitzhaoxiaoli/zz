<?php
!defined('IN_SUPU') && exit('Forbidden');

// $data = '[ ["January", 10], ["February", 8], ["March", 4], ["April", 13], ["May", 17], ["June", 9] , ["Out", 29] ,["Out1", 29] ,["Out2", 29] ,["Ou3", 29] ,["O4ut", 29] ]';

$query = $db->query("SELECT LEFT(e.areacode , 2) pcode ,  COUNT(*) total  FROM sp_certificate c ,sp_enterprises e WHERE c.eid = e.eid AND  `status` IN ('01' , '02') AND c.deleted = 0 GROUP BY pcode");
$data = "[";
while ($rt = $db->fetch_array($query)) {
	$data .= '[ "' . f_region_province($rt['pcode']) . '",' . $rt['total'] . '],';
}
$data .= "]";
unset($query,$rt);

tpl();