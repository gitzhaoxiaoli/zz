<?php 
/**
 * 网站撤销公告导入
 */
@set_time_limit(0);
//查看是否已经导入数据
$sle_sql = "SELECT * FROM sp_CucCertgg WHERE LEFT (ggdate,7) = '".date('Y-m')."'";
$tmp_res = $db->find_results("CucCertgg"," AND LEFT (ggdate,7) = '".date('Y-m')."'",'id');
if(!empty($tmp_res)){
	exit;
}

//获取需要导入的数据
$ggtitle = date('Y')."年第".date("m")."期撤销认证证书的公告（体系）";//标题

$ggdate  = date("Y-m-d");//发布日期

$ggfirst = "根据《中华人民共和国认证认可条例》、CNAS-CC01：2011《 管理体系认证机构要求》及我中心制订的《认证证书的保持、扩大、缩小、暂停、恢复和撤销管理程序》中相关规定，中联认证中心（CUC）撤销以下客户管理体系认证证书：";//发布前内容

$ggend   = "<p>各客户自注销之日起不得在宣传中声称获得我中心的认证，同时应立即停止使用任何引用认证资格的广告材料。</p><p>&nbsp;</p><p>&nbsp;&nbsp;特此公告。</p>";//发布结束内容

//获取企业列表
$gglist  = '';

$where = $join = $fileds = "";

$join   = " LEFT JOIN sp_certificate sc ON sc.id = scc.zsid";

$where = " AND sc.status = 03 AND sc.iso_prod_type = 0 AND RIGHT ( LEFT('scc.cgs_date', 7) ,2) = ".DATE("m");

$fileds = "  scc.id,scc.cgs_date,sc.cert_name,sc.audit_ver,sc.certno,sc.cert_scope,sc.cert_addr";

$sql = "SELECT $fileds FROM sp_certificate_change scc $join WHERE 1 $where";
//echo $sql."<br />";
$res = $db->query($sql);
$array = array();
$i = 1;
while($rt = $db->fetch_array($res))
 {
     $gglist  .= "<p>".$i."：企业名称：".$rt['cert_name']."<br  />";
	 $gglist  .= "&nbsp;&nbsp;认证标准：".read_cache('audit_ver',$rt['audit_ver'])."&nbsp;&nbsp;注册号：".$rt['certno']."&nbsp;&nbsp;撤销日期：".$rt['cgs_date']."<br  />";
     $gglist  .= "&nbsp;&nbsp;地址：".$rt['cert_addr']."<br />";
	 $gglist  .= "&nbsp;&nbsp;证书范围：".$rt['cert_scope']."<br /></p>";
	$i++;	
 }
//写入数据表
$insert_sql = "INSERT INTO sp_CucCertgg ( ggtitle,ggdate,ggfirst,gglist,ggend ) VALUES ('".$ggtitle."','".$ggdate."','".$ggfirst."','".$gglist."','".$ggend."')";
$db->query( $insert_sql );
?>