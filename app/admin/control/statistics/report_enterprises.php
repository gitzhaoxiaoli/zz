<?php
!defined('IN_SUPU') && exit('Forbidden');
/**
 * 企业名称（有效+暂停）	企业人数	领域	标准	证书号	证书状态
 */
@set_time_limit(0);
@ini_set('memory_limit', '512M');


$data = array(array('企业名称（有效+暂停）','企业人数','领域','标准','证书号','证书状态'));
$query = $db->query("SELECT iso , audit_ver , certno , c.`status`,e.ep_amount , c.cert_name , c.eid  FROM sp_certificate c ,sp_enterprises e WHERE c.eid = e.eid AND c.`status` IN ('01' , '02') AND c.deleted = 0");

while ($rt = $db->fetch_array($query)) {
	$data[$rt['eid']]['ep_name'] = $rt['cert_name'];
	$data[$rt['eid']]['ep_amount'] = $rt['ep_amount'];
	$data[$rt['eid']]['iso'][] = f_iso($rt['iso']);
	$data[$rt['eid']]['audit_ver'][] = f_audit_ver($rt['audit_ver']);
	$data[$rt['eid']]['certno'][] = $rt['certno'];
	$data[$rt['eid']]['status'][] = f_certstate($rt['status']);

}
unset($query,$rt);
// p($data);

$_data = array();
foreach ($data as $k => $v) {
	
	foreach ($v as $_k => $_v) {
		if (is_array($_v)) {
			$v[$_k] = join("\r\n" , $_v);
		}
	}
	$data[$k] = $v;
}

// pe($data);
export_excel($data ,  '企业信息');
?>