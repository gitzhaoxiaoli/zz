<?php
!defined('IN_SUPU') && exit('Forbidden');

//人员行程



$fields = $join = $where = '';
$ctfrom_select = f_ctfrom_select();
$name = getgp( 'name' );
$ctfrom = getgp( 'ctfrom' );
$export=getgp("export");

$theYear = getgp( 'year' );
$theMonth = getgp( 'month' );


//当前使用的年/月
list( $year, $month ) = explode('-', mysql2date( 'Y-m', current_time( 'mysql' ) ) );

if( !$theYear )
$theYear = $year;

if( !$theMonth )
$theMonth = $month;

$usedDate = "{$theYear}-{$theMonth}";


//当前月份天数
$the_month_day = mysql2date( 't', $usedDate );

//周六、天设置
$out_dayzjs = array('日','一','二','三','四','五','六');
$month_zjs = array();
for( $t_day = 1; $t_day <= $the_month_day; $t_day++ ){
	$zj = mysql2date( 'w', "{$usedDate}-{$t_day}" );
	$month_zjs[$t_day] = $zj;
}

/*
 *	下拉框
 */

$year_select = $month_select = $province_select = $page_str = '';

for( $i = $theYear - 10; $i <= $theYear+2; $i++ ){
	$year_select .= "<option value=\"$i\"".($theYear == $i ? ' selected' : '' ).">$i</option>";
}

for( $i = 1; $i <= 12; $i++ ){
	$month_select .= "<option value=\"".sprintf("%02d", $i)."\"".($theMonth == $i ? ' selected' : '' ).">".sprintf("%02d", $i)."</option>";

}
$page_str .='&month='.$theMonth;
unset( $ey, $em );
if($ctfrom){
	if( $ctfrom && substr( $ctfrom, 0, $len ) == substr( current_user( 'ctfrom' ), 0, $len ) ){
		$_len = get_ctfrom_level( $ctfrom );
		$len = $_len;
	} else {
		$ctfrom = current_user( 'ctfrom' );
	}
	$last = substr($ctfrom,$len - 1,1);
	$ctfrom_e = substr( $ctfrom, 0, $len -1 ).($last+1);
	$_i = 8 - $len;
	for( $i = 0; $i < $_i; $i++ ){
		$ctfrom_e .= '0';
	}
	$where .= " AND ctfrom >= '$ctfrom' AND ctfrom < '$ctfrom_e'";

	$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );
	$page_str .= '&ctfrom='.$ctfrom;
}
if($name){
	$where = " and name like '%$name%'";
}
$where .= " and is_hire = '1' ";
//人员
$fields .= "id,name,sex,is_hire";
//$total = $db->get_var("SELECT COUNT(*) FROM sp_hr WHERE 1 $where and job_type LIKE '%1004-1%'");
//$pages = numfpage( $total, 20, "?c=$c&a=$a".$page_str );

$hrs = array();
$query = $db->query( "SELECT $fields FROM sp_hr WHERE 1 $where and job_type LIKE '%1004%' order by easycode,name asc  " );
while( $rt = $db->fetch_array( $query ) ){
	$hrs[$rt['id']] = $rt;
}

if( $hrs ){
	$uids = array_keys( $hrs );
	/*
	 //注册资格
	 $sql = "SELECT uid,iso,qua_type FROM sp_hr_qualification WHERE status = '1' AND uid IN (".implode(',',$uids).")" ;
	 $query = $db->query( $sql);
	 while( $rt = $db->fetch_array( $query ) ){
		isset( $hrs[$rt['uid']]['qua_types'] ) or $hrs[$rt['uid']]['qua_types'] = array();
		$hrs[$rt['uid']]['qua_types'][] = f_iso( $rt['iso'] ) . "： " . f_qua_type( $rt['qua_type'] );
		}
		*/

	$sql = "SELECT a.note,a.data_for,a.tid,a.iso,a.uid,a.taskBeginDate,a.taskEndDate,a.role,e.ep_name,e.ep_shortname FROM sp_task_audit_team a LEFT JOIN sp_enterprises e ON e.eid = a.eid WHERE 1 AND a.uid IN (".implode(',',$uids).") AND ( ( YEAR(a.taskBeginDate) = '$theYear' AND MONTH(a.taskBeginDate) = '$theMonth') OR  (YEAR(a.taskEndDate) = '$theYear' AND MONTH(a.taskEndDate) = '$theMonth') ) AND a.deleted = 0";

	$query = $db->query( $sql );

	while( $rt = $db->fetch_array( $query ) ){
		$_query=$db->query("SELECT iso FROM `sp_project` WHERE `tid` = '$rt[tid]' AND `deleted` = '0' order by iso");
		$_iso=array();
		while($_rt=$db->fetch_array($_query)){
			$_iso[]=f_iso($_rt[iso]);
		
		
		
		}
		$iso = join(",",array_unique($_iso));
		$ep_shortname=$rt[ep_shortname];
		switch( $rt['data_for'] ){
			case 2	: $title = "碳核查：$rt[note]\n"; break;
			// case 2	: $title = "经销商考评：$rt[ep_name]\n"; break;
			// case 3	: $title = "二方审核：$rt[ep_name]\n"; break;
			case 4	: $title = "培训：$rt[note]\n"; break;
			// case 5	: $title = "TS业务：$rt[ep_name]\n"; break;
			case 5	: $title = "请假：$rt[note]\n"; break;
			case 0	:
			default	: $title = "审核企业：$rt[ep_name]\n"; $note="$rt[ep_shortname]\n";break;
		}
		if($rt['data_for']=='0')
			$title .= "组内身份：".read_cache("audit_role",$rt[role])."\n"."审核体系：".$iso."\n";
		$s_date = mysql2date("Y年m月d日",$rt[taskBeginDate]);
		$e_date = mysql2date("Y年m月d日",$rt[taskEndDate]);

		$title .= "开始时间：$s_date\n";
		$title .= "结束时间：$e_date";
		
		isset( $hrs[$rt['uid']]['tasks'] ) or $hrs[$rt['uid']]['tasks'] = array();
		isset( $hrs[$rt['uid']]['days'] ) or $hrs[$rt['uid']]['days'] = array();
		list( $taskBeginDate, $taskBeginTime ) = explode( ' ', $rt['taskBeginDate'] );
		list( $taskEndDate, $taskEndTime ) = explode( ' ', $rt['taskEndDate'] );
		//开始/结束 日期
		list( $beginYear, $beginMonth, $beginDay ) = explode( '-', $taskBeginDate );

		list( $endYear, $endMonth, $endDay ) = explode( '-', $taskEndDate );


		//@wangp 派人类型 2013-09-18 11:33
		switch( $rt['data_for'] ){ 
			case 1	: $pt = 'emKP'; break;
			case 2	: $pt = 'bcKP'; break;
			case 3	: $pt = 'bcSH'; break;
			case 4	: $pt = 'emTS'; break;
			case 5  : $pt = 'bcTS'; break;
			case 6	: $pt = 'bcQJ'; break;
			case 0	:
			default : $pt = 'bcISO'; break;
		}

		if( in_array( $theMonth, array( $beginMonth, $endMonth ) ) ){
			//开始日
			$day_start = 0;
			if( $theMonth == $beginMonth ){
				$day_start = $beginDay;
				if( $beginMonth == $endMonth ){
					$day_end = $endDay;
				} else {
					$day_end = $the_month_day;
				}
			} else {
				$day_start = 1;
				$day_end = $endDay;
			}

			//开始/结束 时间
			list( $beginHour, $beginMin ) = explode( ':', $taskBeginTime );
			list( $endHour, $endMin ) = explode( ':', $taskEndTime );
			$bh = intval( $beginHour );
			$eh = intval( $endHour );
			//echo $taskBeginDate.'_'.$taskEndDate.'_'.$bh.'_'.$eh.'<br>';
			if( $day_start < $day_end ){
				for( $_day = (int)$day_start; $_day <= $day_end; $_day++ ){
					if( $_day == (int)$day_start ){
						$class = get_day_class( $bh, 17 );
					} elseif( $_day == (int)$day_end ){
						$class = get_day_class( 8, $eh );
					} else {
						$class = get_day_class();
					}
					//echo $class."-".$pt."<bR>";
					//@wangp 跟据派人类型 使用对应的样式 2013-09-18 13:13
					$hrs[$rt['uid']]['days'][$_day][$class] = "<a class=\"$class $pt\" title=\"$title\" href=\"javascript:;\"></a>";
				}
			} else {
				$class = get_day_class( $bh, $eh );
				//@wangp 跟据派人类型 使用对应的样式 2013-09-18 13:13
				$hrs[$rt['uid']]['days'][(int)$day_start][$class] = "<a class=\"$class $pt\" title=\"$title\" href=\"javascript:;\"></a>";
			}
		}

		$hrs[$rt['uid']]['tasks'][] = $rt;

	}


}
if(!$export){
	tpl( 'audit/list_hr_plan' );

} else {
		ob_start();
		tpl( 'xls/list_hr_plan' );
		$data = ob_get_contents();
		ob_end_clean();

		export_xls( '人员行程', $data );
}

function get_day_class( $bh = 8, $eh = 17 ){
	$bh = intval( $bh );
	$eh = intval( $eh );
	if( !$bh || !$eh ) return false;
	if( $bh < 9 && $eh > 16 ){
		$class = 'all';
	} elseif( $bh > 12 ){
		$class = 'pm';
	} else {
		$class = 'am';
	}
	return $class;
}

?>