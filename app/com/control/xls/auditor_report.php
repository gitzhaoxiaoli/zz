<?php
!defined('IN_SUPU') && exit('Forbidden');

$begindate = getgp('s_date');//取数开始日期
$enddate = getgp('e_date');//取数截止日期
$get_data_arr = $data_arr = array();

function get_audit_type($audit_type){
	if($audit_type=='1001'||$audit_type=='1002'||$audit_type=='1003'){
		return '01';
	}else if($audit_type=='1004-1'||$audit_type=='1004-2'||$audit_type=='1004-3'){
		return '0301';
	}else if($audit_type=='1007'){
		return '02';
	}else if($audit_type=='1008'||$audit_type=='1009'||$audit_type=='1010'){
		return '0302';
	}else{
		return '0302';
	}
}

$sql = "select uid from sp_hr_qualification hq left join sp_hr h on h.id=hq.uid where hq.qua_type in ('01','02','04') and hq.status='1' and (h.is_hire='1' or h.is_hire='2') group by uid";
$res = $db->query($sql);
while($row = $db->fetch_array($res)){
	$uid_s[] = $row['uid'];
}

function get_day($a,$b){
	$day = (strtotime(substr($b,0,10))-strtotime(substr($a,0,10)))/86400 + 1;
	$shangwu = intval(substr($a, 11,2));
	$xiawu = intval(substr($b, 11,2));
	if($shangwu>=12){
		$day -= 0.5;
	}
	if($xiawu<=12){
		$day -= 0.5;
	}
	return $day;

}
$arr['a1'] = 'KC-001';
$uid_str = implode("','",$uid_s);
$sql = "select uid,iso,audit_code from sp_hr_audit_code where uid in('".$uid_str."')";
$res = $db->query($sql);
while($row = $db->fetch_array($res)){
	$code_s[$row['uid']][$row['iso']][] = $row['audit_code'];
}

$sql = "select * from sp_task_auditor where iso !='' and taskBeginDate >='$begindate' and taskEndDate <= '$enddate' and uid in ('".$uid_str."') ";
$resres = $db->query($sql);
while($rows = $db->fetch_array($resres)){
	$iso_s = explode(',',$rows['iso']);
	$count = count($iso_s);
	$day = get_day($rows['taskBeginDate'], $rows['taskEndDate']);
	foreach($iso_s as $v){
		$datas[$rows['uid']][$v] += $day;
		if($count>1){
			$datas[$rows['uid']]['jiehe'][$v] += $day;
		}
	}

}

$sql = "select uid,iso,qua_type,qua_no,name,cts_date,cte_date,sex,birthday,is_hire,card_no,code from sp_hr_qualification hq left join sp_hr h on h.id=hq.uid where hq.qua_type in ('01','02','04') and hq.status='1' and (h.is_hire='1' or h.is_hire='2') ";
$res = $db->query($sql);



//输出Execl文件
/* */
 $filename = iconv( 'UTF-8', 'GB2312', '检查组信息表_').mysql2date( "Y-m-d", nowTime( 'mysql' ) ).".xls";

 header("Content-Type: application/vnd.ms-excel");
 header("Content-Disposition: attachment; filename=" . $filename );
 header("Pragma: no-cache");
 header("Expires: 0");


?>
<meta
	http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
TD {
	font-size: 12px;
	vertical-align: middle;
	vnd .ms-excel.numberformat: @;
}
</style>
<table border="1">
	<tr>
		<td>1.认证机构信息号</td>
		<td>2.认证体系代码</td>
		<td>3.姓名</td>
		<td>4.性别</td>
		<td>5.出生年月</td>
		<td>6.职称</td>
		<td>7.在职状况</td>
		<td>8.居住地址</td>
		<td>9.身份证号</td>
		<td>10.在机构内人员编号</td>
		<td>11.现在工作单位</td>
		<td>12.学历</td>
		<td>13.毕业学校</td>
		<td>14.所学专业</td>
		<td>15.本年度总检查人日数</td>
		<td>16.本年度该体系参加检查人日数</td>
		<td>17.在机构内所负责的工作</td>
		<td>18.注册资格(适用时:注明所注册的专业)</td>
		<td>19.在机构内所审定的专业</td>
		<td>20.聘用日期</td>
		<td>21.解聘日期</td>
		<td>22.上报信息所属年度</td>
		<td>23.检查报告</td>
	</tr>
	<?php
!defined('IN_SUPU') && exit('Forbidden');
	while($row=$db->fetch_array($res)){
		if($row['sex']==1){
			$row['sex'] = '男';
		}else{
			$row['sex'] = '女';
		}
		if($row['is_hire']==1){
			$row['is_hire'] = '在职';
		}else{
			$row['is_hire'] = '离职';
		}
		$sql = "select * from sp_hr_experiencej where up_uid='$row[uid]' order by e_date desc limit 1 ";
		$j_info = $db->get_row($sql);
		$xueli = read_cache('education',$j_info['xueli']);
		if($xueli){$xueli='大学本科';}
		$zong = 0;
		foreach($datas[$row['uid']] as $k2=>$v2){
			if($k2!='jiehe'){
				$zong += $v2;
			}
		}
		if($datas[$row['uid']]['jiehe'][$row['iso']]){
			$jiehe = "(结合检查:".$datas[$row['uid']]['jiehe'][$row['iso']].")";
		}else{
			$jiehe = '';
		}
		echo "<tr>";
		echo "<td>".'KC-001'."</td>";
		echo "<td>".read_cache('iso',$row['iso'])."</td>";
		echo "<td>".$row['name']."</td>";
		echo "<td>".$row['sex']."</td>";
		echo "<td>".$row['birthday']."</td>";
		echo "<td>职称</td>";
		echo "<td>".$row['is_hire']."</td>";
		echo "<td>".$row['areacode']."</td>";
		echo "<td>".$row['card_no']."</td>";
		echo "<td>".$row['code']."</td>";
		echo "<td>凯新</td>";
		echo "<td>".$j_info['school']."</td>";
		echo "<td>".$xueli."</td>";
		echo "<td>".$j_info['major']."</td>";
		echo "<td>".$zong."</td>";
		echo "<td>".$datas[$row['uid']][$row['iso']]." ".$jiehe."</td>";
		echo "<td>暂无</td>";
		echo "<td>".read_cache('qualification',$row['qua_type'])."</td>";
		echo "<td>".implode(';',$code_s[$row['uid']][$row['iso']])."</td>";
		echo "<td>".$row['cts_date']."</td>";
		echo "<td>".$row['cte_date']."</td>";
		echo "<td>".substr($begindate,0,10)."</td>";
		echo "</tr>";
	}

	?>
</table>

