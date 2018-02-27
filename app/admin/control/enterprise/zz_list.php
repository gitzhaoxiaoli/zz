<?php
!defined('IN_SUPU') && exit('Forbidden');
//搜索
$where = '';
extract($_GET, EXTR_SKIP); //获取搜索项 
//企业名称
if($ep_name){
	$tmp_sql = "SELECT eid FROM sp_enterprises WHERE deleted = 0 AND ep_name LIKE '%$ep_name%'";
	$tmp_query = $db->query($tmp_sql);
	$eids = array();
	$tmp = 0;
	while($tmp_rt = $db->fetch_array($tmp_query))
	{
		$eids[$tmp] = $tmp_rt['eid'];
		$tmp++;
	}
	array_merge($eids,array(-1));
	$where .= " AND eid IN ('".implode("','",$eids)."')";
}
//资质证书名称
if($name){
	$where .= " AND name LIKE '%$name%'";
}
//证书起始时间
if($zz_sdate){
	$where .= " AND zz_sdate >= '".$zz_sdate."'";
}
//证书起始时间
if($zz_edate){
	$where .= " AND zz_edate <= '".$zz_edate."'";
}
//月份下拉
$yuefei_array = array
   (
      '6' => '六个月',
      '5' => '五个月',
      '4' => '四个月',
      '3' => '三个月',
      '2' => '二个月',
      '1' => '一个月',
      '99' => '当月',
   );
$yuefei = (int)$yuefei ? $yuefei : 6;
if($yuefei == 99){
	$where .= " AND zz_edate <= '".date("Y-m")."-31'";
}else{
		$where .= " AND zz_edate <= '".substr(thedate_add(date("Y-m-d"),$yuefei,'month'),0,10)."'";
}
//分页
 $total = $db->get_var("SELECT COUNT(*) FROM sp_attachments_pro WHERE deleted = 0  $where");
 $pages = numfpage($total);
//资质证书到期查询
$sql = "SELECT * FROM sp_attachments_pro WHERE deleted = 0  $where $pages[limit]";

$query = $db->query($sql);
$res = array();
while($rt = $db->fetch_array($query)){
	$rt['ep_name'] = $db->getField('enterprises','ep_name',array('eid'=>$rt['eid']));
	$res[$rt['id']] = $rt;
}
tpl();
?>