<?php
/**
 * 导出企业列表 
 */

$data = array("企业名称","地址","项目编号","申请日期","合同登记日期","款到日期","受理日期","现场审核人日","审核费用","现场审核日期","审核组长","认证决定日期","发证日期","证书编号","快递编号","一监审费用","一监审现场审核日期","一监审组长","一监审合格通知日期","二监审费用","二监审现场审核日期","二监审组长","二监审合格通知日期");

$query = $db->query("SELECT e.ep_name,e.ep_addr,cti.cti_code,ct.accept_date,ct.approval_date FROM `sp_contract_item` cti LEFT JOIN sp_contract ct ON ct.ct_id = cti.cti_id LEFT JOIN sp_enterprises e ON e.eid = cti.eid WHERE cti.deleted = 0");
while($rt = $db->fetch_array($query)){
	

}
?>