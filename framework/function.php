<?php
//ajax异步传输使用
function print_json($array)
{
    exit(json_encode($array));
}

 
//===========================字符串处理=======================================
/*
 *	函数名：getgp
 *	说  明：获取 $_GET 或 $_POST 内的变量
 *	参  数：$key 数组的下标 $method 为 G 则取 $_GET 数组 为 P 则取 $_POST 数组
 *	返回值：如果存在则返回元素，不存在刚返回 null
 */
function getgp($key,$method=null) {
	if ($method == 'G' || $method != 'P' && isset($_GET[$key])) {
		return $_GET[$key];
	}else{
		if(is_array($_POST[$key]))
			return $_POST[$key];
		else
			return trim($_POST[$key]);
	}
	
}
/*
 * 函数名：sub_Str
 * 说  明：把字符串根据$arg参数截取前一部分
 * 参  数：$data,$arg
 * 返回值：返回截取后的字符串
 */
function sub_Str($data,$arg){
         $tmp_arr = explode($arg,$data);
         
         return $tmp_arr[0];
	};
	
/*
 * 函数名：getRandChar
 * 说  明：获取随机字符串
 * 参  数：$length 长度
 * 返回值：字符串
 */	
function getRandChar($length){
   $str = null;
   $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
   $max = strlen($strPol)-1;

   for($i=0;$i<$length;$i++){
    $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
   }

   return $str;
}
/*
 * 函数名：xml_str
 * 说  明：把字符转义为实体字符
 * 参  数：$str
 * 返回值：返回转义后的字符串
 应用：导出word中编辑特殊字符
 */
function xml_str($str){
	$arr_search = array('<','>','&','\'','"');
	$arr_replace = array('＜;','＞;','＆;','＇','＂');
	$str = str_ireplace($arr_search,$arr_replace,$str);
	return $str;
}

/*
 * 函数名：getFirstChar
 * 说  明：获取中文首字母
 * 参  数：$str
 * 返回值：首字母 （单个）
 * eg: 首字母=》S
 */
function getFirstChar($str)
{ 
    $str= iconv("UTF-8","gb2312", $str);//如果程序是gbk的，此行就要注释掉 
    if (preg_match("/^[\x7f-\xff]/", $str)) 
    { 
        $fchar=ord($str{0}); 
        if($fchar>=ord("A") and $fchar<=ord("z") )return strtoupper($str{0}); 
        $a = $str; 
        $val=ord($a{0})*256+ord($a{1})-65536; 
        if($val>=-20319 and $val<=-20284)return "A"; 
        if($val>=-20283 and $val<=-19776)return "B"; 
        if($val>=-19775 and $val<=-19219)return "C"; 
        if($val>=-19218 and $val<=-18711)return "D"; 
        if($val>=-18710 and $val<=-18527)return "E"; 
        if($val>=-18526 and $val<=-18240)return "F"; 
        if($val>=-18239 and $val<=-17923)return "G"; 
        if($val>=-17922 and $val<=-17418)return "H"; 
        if($val>=-17417 and $val<=-16475)return "J"; 
        if($val>=-16474 and $val<=-16213)return "K"; 
        if($val>=-16212 and $val<=-15641)return "L"; 
        if($val>=-15640 and $val<=-15166)return "M"; 
        if($val>=-15165 and $val<=-14923)return "N"; 
        if($val>=-14922 and $val<=-14915)return "O"; 
        if($val>=-14914 and $val<=-14631)return "P"; 
        if($val>=-14630 and $val<=-14150)return "Q"; 
        if($val>=-14149 and $val<=-14091)return "R"; 
        if($val>=-14090 and $val<=-13319)return "S"; 
        if($val>=-13318 and $val<=-12839)return "T"; 
        if($val>=-12838 and $val<=-12557)return "W"; 
        if($val>=-12556 and $val<=-11848)return "X"; 
        if($val>=-11847 and $val<=-11056)return "Y"; 
        if($val>=-11055 and $val<=-10247)return "Z"; 
    }  
    else 
    { 
        return false; 
    } 
}
 
/*
 * 函数名：getFirstChar
 * 说  明：获取中文首字母
 * 参  数：$str
 * 返回值：首字母 （多个个）
 * eg: 首字母=》SZM
 */ 
function getFirstNameChar($zh){
    $ret = "";
    for($i = 0; $i < mb_strlen($zh,"UTF-8"); $i++){
        $s1 = mb_substr($zh,$i,1,"UTF-8");
		$ret.=getFirstChar($s1);
    }
    return $ret;
}

function pregStr($pattern,$subject){
	preg_match($pattern,$subject,$res);
	return $res[0];
	
}

/*
 * 函数名：cny
* 功  能：cny(1234) 壹仟贰佰叁拾肆圆 将金额小写转大写
* 参  数：金额
* 返回值：字符串
*/

function cny($ns) { 
    static $cnums=array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"), 
        $cnyunits=array("圆","角","分"), 
        $grees=array("拾","佰","仟","万","拾","佰","仟","亿"); 
    list($ns1,$ns2)=explode(".",$ns,2); 
    $ns2=array_filter(array($ns2[1],$ns2[0])); 
    $ret=array_merge($ns2,array(implode("",_cny_map_unit(str_split($ns1),$grees)),"")); 
    $ret=implode("",array_reverse(_cny_map_unit($ret,$cnyunits))); 
    return str_replace(array_keys($cnums),$cnums,$ret); 
}


function _cny_map_unit($list,$units) { 
    $ul=count($units); 
    $xs=array(); 
    foreach (array_reverse($list) as $x) { 
        $l=count($xs); 
        if ($x!="0" || !($l%4)) $n=($x=='0'?'':$x).($units[($l-1)%$ul]); 
        else $n=is_numeric($xs[0][0])?$x:''; 
        array_unshift($xs,$n); 
    } 
    return $xs; 
}

//===========================时间函数=======================================
/*
 *	函数名：current_time
 *	说  明：获取当前时间
 *	参  数：$type 类型 mysql = yyyy-mm-dd hh:ii:ss 形式 timestamp 则为 时间戳形式
 *			$gmt 是否格林威治时间
 *	返回值：正整数
 */
function current_time($type)
{
    //@HBJ 2013年9月11日 17:24:29 解决时间不正确的问题
    /* @wangp 不需要的代码 2013-09-28 9:13
    date_default_timezone_set('PRC');
    ini_set('date.timezone','Asia/Shanghai');
    $gmt_offset = 0;*/
    switch ($type) {
        case 'mysql':
            return date('Y-m-d H:i:s');
            break;
        case 'timestamp':
            return time();
            break;
        default:
            return date("Y-m-d");
            break;
    }
}

/*
 *	函数名：getEpName
 *	说  明：获取企业名称
 *	参  数：
 *	返回值：
 */

function getEpName($eid){
	if(!$eid)return;
	global $db;
	return $db->getField("enterprises","ep_name",array("eid"=>$eid));
	
}


/**
 * 能过eid取联系人 返回数组
 * [get_person description]
 * @param  [type] $eid [description]
 * @return [type]      [description]
 */
function get_person($eid){
    global $db;
    $query = $db->query("SELECT meta_name,meta_value FROM sp_metas_ep WHERE ID = '$eid' AND used = 'enterprise' AND meta_name like 'person%'");
    while($rt = $db->fetch_array($query)){
        $data[$rt[meta_name]] = explode("||:||", $rt[meta_value]);

    }
    unset($query,$rt);
    $person_array = array();
    foreach ($data[person] as $key => $v) {
        foreach (array_keys($data) as $_v) {
            $temp[$_v] = $data[$_v][$key];
        }
        $person_array[] = $temp;
    }
    return $person_array;
}
    




//===========================文件处理函数=======================================
/*
 * 函数名：writeover
 * 功  能：写文件
 * 参  数：$filename 文件名 $data 文件内容 $iflock 是否锁定文件 $check 是否校验 $chmod 是否用 chmod 设置权限
 * 返回值：无
 */
function writeover($filename, $data, $method = "rb+", $iflock = 1, $check = 1, $chmod = 1)
{
    $check && strpos($filename, '..') !== false && exit('Forbidden');
    touch($filename);
    $handle = fopen($filename, $method);
    if ($iflock) {
        flock($handle, LOCK_EX);
    }
    fwrite($handle, $data);
    if ($method == "rb+")
        ftruncate($handle, strlen($data));
    fclose($handle);
    $chmod && @chmod($filename, 0777);
}
//数组处理
function deal_arr($arr, $require)
{
    $tmp = array();
    foreach ($arr[$require] as $k => $v) {
        if (!$v)
            continue;
        $keys = array_keys($arr);
        foreach ($keys as $key) {
            if (!$arr[$key][$k])
                continue;
            $tmp[$k][$key] = $arr[$key][$k];
        }
    }
    return $tmp;
}
//调试类
function debug($msg)
{
    throw new error($msg);
}


/**
 * 将字符串按照其中的分隔符换行显示
 * @param string $str 需要处理的字符串
 * @param array $options 字符串中的分隔符
 * @return string
 */
function LongToBr($str, $options = array())
{
    $separator = '';
    foreach ($options as $option) {
        $str = str_replace($option, $options[0], $str);
    }
    $str_array = explode($options[0], $str);
    $string    = '';
    foreach ($str_array as $key => $str) {
        $string .= $str;
        if ($key < count($str_array) - 1) {
            $string .= '<br />';
        }
    }
    return $string;
}
/*
 *	函数名：p
 *	说  明：调试函数：开发时输出错误信息，应用控制器层：错误登记一般
 *	参  数：
 *	返回值：
 */
function p()
{
    $args = func_get_args();
    echo '<pre>';
    //多个参数循环输出
    foreach ($args as $arg) {
        if (is_array($arg)) {
            print_r($arg);
            echo '<br>';
        } else if (is_string($arg)) {
            echo $arg . '<br>';
        } else {
            var_dump($arg);
            echo '<br>';
        }
    }
    echo '</pre>';
}
/*
 *	函数名：print_const
 *	说  明：打印 自定义常量
 *	参  数：
 *	返回值：
 */

function print_const(){
	$const=get_defined_constants(TRUE);
	p($const[user]);
}
//数据库错误调试
function halt($msg = '', $sql = '')
{
    $c_file = '控制器文件：' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    p($c_file);
    $action = $_GET['a'];
    p('控制器方法：' . $action);
    p($_POST);
    echo 'Query Error:<br />' . $msg;
    if ($msg)
        echo '<br/>';
    echo $err_str = mysql_error();
    $output      = current_time('mysql') . "<br />" . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . " $msg\r\n$err_str \r\n\r\n";
    $server_date = date("Y-m-d");
    $filename    = $server_date . "_SQL.htm";
    //if(DEBUG)
    // writeover( LOG_DIR.$filename, $output, 'ab+' ); //暂时不生成错误记--录
    exit();
}
 

/*
 *	函数名：sp_var_export
 *	说  明：输出数组为代码格式
 *	参  数：$input 要输出的数据 $indent 数组结果分隔符
 *	返回值：代码格式的 数组
 */
function sp_var_export($input, $indent = '')
{
    switch (gettype($input)) {
        case 'string':
            return "'" . str_replace(array(
                "\\",
                "'"
            ), array(
                "\\\\",
                "\'"
            ), $input) . "'";
        case 'array':
            $output = "array(\r\n";
            foreach ($input as $key => $value) {
                $output .= $indent . "\t" . sp_var_export($key, $indent . "\t") . ' => ' . sp_var_export($value, $indent . "\t");
                $output .= ",\r\n";
            }
            $output .= $indent . ')';
            return $output;
        case 'boolean':
            return $input ? 'true' : 'false';
        case 'NULL':
            return 'NULL';
        case 'integer':
        case 'double':
        case 'float':
            return "'" . (string) $input . "'";
    }
    return 'NULL';
}
/**********************************************
 *											  *
 *				运行计划任务				  *
 *											  *
 **********************************************/
function run_cron(){
	global $db;

	$query = $db->query( "SELECT * FROM sp_cron WHERE is_open = '1' AND next_time <= '".current_time('mysql')."'" );
	while( $_d = $db->fetch_array( $query ) ){
		$next_time = '';
		$_next_time=$_d['next_time'];
		$_d['next_time']=date("y-m-d ").mysql2date(" H:i:s",$_d['next_time']);
		switch( $_d['loop_type'] ){
			case 'month':
				$next_time = thedate_add( $_d['next_time'], 1, 'month' );
				break;
			case 'week'	:
				$next_time = thedate_add( $_d['next_time'], 1, 'week' );
				break;
			case 'day'	:
				$next_time = thedate_add( $_d['next_time'], 1, 'day' );
				break;
			case 'hour'	:
				$next_time = thedate_add( $_d['next_time'], 1, 'hour' );
				break;
			case 'now'	:
			default		:
				list( $now_day, $now_hour, $now_minute ) = explode( '-', $_d['loop_time'] );
				$now_type = 'minute';

				if( $now_day ){
					$now_type = 'day';
					$now_time = $now_day;
				} elseif( $now_hour ){
					$now_type = 'hour';
					$now_time = $now_hour;
				} else {
					$now_type = 'minute';
					$now_time = $now_minute;
				}
				$next_time = thedate_add( current_time('mysql'), $now_time, $now_type );
				break;
		} // end switch(.....
		if( file_exists( APP_DIR ."/cron/{$_d['run_script']}.php") && current_time('mysql') >= $_next_time ){
			$db->update( 'cron',
					array( 'modifed_time' => current_time('mysql'), 'next_time' => $next_time ),
					array( 'cron_id' => $_d['cron_id'] ) );
			require_once( APP_DIR ."/cron/{$_d['run_script']}.php" );
			
		}

	} // end while( $rt = $db->fetch_array(.....

}
//输出Execl文件
function export_xls($filename, $data)
{
    $filename = iconv('UTF-8', 'gbk', $filename) . '_' . mysql2date("Y-m-d", current_time('mysql')) . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=" . $filename);
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $data;
}
/**
 * 输出到浏览器文件
 * @param string $filename 文件名
 * @param string $filedir 路径
 * 
 */
function export_word($filename, $filedir)
{
	ob_end_clean();
    $output = readover($filedir);//
	header("Content-type: application/octet-stream");
	header("Accept-Ranges: bytes");
	header("Content-Disposition: attachment; filename=" . iconv( 'UTF-8', 'gbk', $filename ) );
	echo $output;
}
/**
 * 日志记录
 * @param integer $eid 企业ID(没有则传入0)
 * @param integer $uid 用户ID(没有则传入0)
 * @param string(200) $content 日志内容
 * @param string(65535) $af_str 改前内容
 * @param string(65535) $bf_str 改后内容
 * @return integer 新日志主键id
 */
function log_add($eid='', $uid='', $content, $af_str='', $bf_str='')
{
    global $db;
    $data = array(
        'eid' => $eid,
        'uid' => $uid,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'content' => $content,
        'af_str' => $af_str,
        'bf_str' => $bf_str,
    );
    return $db->insert('log', magic_gpc($data, 1));
}
/*
 *	函数名：parse_args
 *	说  明：解析参数(可以是 字符串|数组|对象）返回数组
 *	参  数：$args 要解析的字符串，数据或对象 $default 可选 有默认值的返回值
 *	返回值：数组
 */
function parse_args($args, $defaults = '')
{
    if (is_object($args))
        $r = get_object_vars($args);
    elseif (is_array($args))
        $r =& $args;
    else
        parse_str($args, $r);
    if (is_array($defaults))
        return array_merge($defaults, $r);
    return $r;
}
/**
 * 修正tab切换连接
 * @param boolean $echo 是否输出
 * @return string
 */
function gettourl($echo = true, $type = '')
{
    $str = '';
    foreach ($_GET as $key => $value) {
        if (!in_array($key, array(
            'm',
            'a',
            'status',
            'svStatus',
            'pd_type',
            'is_sms',
            'redata_status',
            'is_hire',
            'hr_exp',
            'is_check',
            'audit_finish',
            'is_bao',
            'is_finance',
            'is_download'
        ))) {
            $str .= "&" . $key . "=" . $value;
        }
    }
    if ($echo) {
        echo $str;
    } else {
        return $str;
    }
}
 
 /*
 * 函数名：sysinfo
 * 功  能：输出系统配置信息
 * 参  数：要显示的选项键名 $show
 * 返回值：无
 */
function sysinfo($show)
{
    if (empty($show))
        return false;
    switch ($show) {
        case 'sysurl':
        case 'home_url':
        case 'url': //系统路径
        case 'siteurl':
            $output = sys_url();
            break;
        case 'template_directory':
        case 'template_url':
            $output = get_template_directory_uri();
            break;
        case 'stylesheet_directory':
            $output = get_stylesheet_directory_uri();
            break;
        case 'stylesheet_url':
            $output = get_stylesheet_uri();
            break;
        case 'charset':
            $output = get_option('charset');
            break;
        case 'regname':
            $output = get_option('regname');
            break;
        case 'softname':
            $output = get_option('softname');
            break;
        default:
            $output = '';
            break;
    }
    echo $output;
}

//===========================地址函数=======================================
/*
 *	函数名：sys_url
 *	说  明：获取程序运行的URL
 *	参  数：$page 指定路径
 *	返回值：返回程序运行的URL路径
 */
function sys_url($path = '')
{
    $url = "http://$_SERVER[HTTP_HOST]" . substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
    if (!empty($path) && is_string($path) && strpos($path, '..') === false)
        $url .= '/' . ltrim($path, '/');
    return $url;
}

 
 
/////////////////////////////时间处理函数////////////////////////////////////////////////////
/*
 *	函数名：mysql2date
 *	说  明：将mysql日期格式转为指定的日期格式
 *	参  数：$format 目标日期格式
 *			$date 日期时间
 *	返回值：正整数
 */
function mysql2date($format, $date)
{
    if (empty($date))
        return false;
    $i = strtotime($date);
    return date($format, $i);
}
/*
 *	函数名：thedate_add
 *	说  明：在指定的日期上增另指定的时间
 *	参  数：$date 日期
 *			$int 整数
 *			$r 类型：year = 年 month = 月 day = 天 week = 周 hour = 时 minute = 分 second = 秒
 *	返回值：日期
 */
function thedate_add($date, $int, $r)
{
    $rs = array(
        'year',
        'month',
        'day',
        'week',
        'hour',
        'minute',
        'second'
    );
    if (empty($date) || empty($int) || !in_array($r, $rs))
        return false;
    return date('Y-m-d H:i:s', strtotime($date . " $int $r"));
}
/*
 * 函数名：get_addday
 * 功  能：日期 + $month 月 -$day 天
 * 参  数：$nowtiem 当前时间 $month 要加/减的月数 $day 要加/减的天数
 * 返回值：加/减后的 日期
 */
function get_addday($nowtime, $month, $day = 0)
{
    $nowtime = explode('-', $nowtime);
    $mktime  = mktime(0, 0, 0, $nowtime['1'] + $month, $nowtime['2'] + $day, $nowtime['0']);
    $time    = date("Y-m-d", $mktime);
    return $time;
}

function mkdate($s_date, $e_date)
{
   $s_date     = strtotime($s_date);
    $e_date     = strtotime($e_date);
    $time       = $e_date - $s_date;
	$time       = $time / (3600 * 24);
	$t=$time-(int)$time;
	if($t==0)
		$res=1;
	elseif($t<0.3)
		$res=0.5;
	elseif($t>0.3)
		$res=1;
	
	return (int)$time+$res;
}

/**
 * 时间差计算
 *
 * @param $s_date , $e_date 
 * @return String Time Elapsed
 * @eg  time2Units("2015-11-4 08:00","2015-11-29 17:00"); 25天9小时
 */
function time2Units ($s_date,$e_date)
{
   $time = strtotime($e_date) - strtotime($s_date);
   if($time < 0) return false;
   // $year   = floor($time / 60 / 60 / 24 / 365);
   // $time  -= $year * 60 * 60 * 24 * 365;
   // $month  = floor($time / 60 / 60 / 24 / 30);
   // $time  -= $month * 60 * 60 * 24 * 30;
   // $week   = floor($time / 60 / 60 / 24 / 7);
   $time  -= $week * 60 * 60 * 24 * 7;
   $day    = floor($time / 60 / 60 / 24);
   $time  -= $day * 60 * 60 * 24;
   $hour   = floor($time / 60 / 60);
   $time  -= $hour * 60 * 60;
   $minute = floor($time / 60);
   $time  -= $minute * 60;
   $second = $time;
   $elapse = '';

   $unitArr = array('年'  =>'year', '个月'=>'month',  '周'=>'week', '天'=>'day',
                    '小时'=>'hour', '分钟'=>'minute', '秒'=>'second'
                    );

   foreach ( $unitArr as $cn => $u )
   {
       if ( $$u > 0 )
       {
           $elapse .= $$u . $cn;
           //break;
       }
   }

   return $elapse;
}

/*
 * 函数名：format_date
 * 功  能：格式化时间
 * 参  数：$date  （2014-03-8 13:00）
 * 返回值：天数 2014年03月8日 下午
 */
function format_date($date)
{
    $str = explode(' ', $date);
    $arr = explode('-', $str[0]);
    $res = $arr[0] . "年" . $arr[1] . "月" . $arr[2] . "日";
    if ($str[1])
        if (strtotime($str[1]) < strtotime("13:00:00"))
            $res .= " 上午";
        else
            $res .= " 下午";
    return $res;
}
 
/*
 *	函数名：magic_gpc
 *	说  明：对字符串或数组进行转义
 *	参  数：$string 要转义的字符串
 *			$force 是否强制转义
 *			$strip 是否删除由 addslashes() 函数添加的反斜杠
 *	返回值：如果存在则返回元素，不存在刚返回 null
 */
function magic_gpc($string, $force = 0, $strip = false)
{
    if (!MAGIC_QUOTES_GPC || $force) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = magic_gpc($val, $force, $strip);
            }
        } else {
            $string = addslashes($strip ? stripslashes($string) : $string);
        }
    }
    return $string;
}
/*
 *	函数名：lang_info
 *	说  明：语言包信息
 *	参  数：$template 模板名 $msg 要使用的信息
 *	返回值：所使用的语言的 字符串
 */
function lang_info($template, $msg)
{
    global $lang;
    static $langs = array();
    if (!isset($langs[$template])) {
        require LANG . $template . '.lang.php';
        $langs[$template] = true;
    }
    if (!empty($lang[$msg])) {
        return $lang[$msg];
    } else {
        return $msg;
    }
}
/*
 *	函数名：curent_user
 *	说  明：获取当前用户信息
 *	参  数：$field 要获取的字段名
 *	返回值：$field 不为空时获取对应的字段值 为空时返回全部
 */
function current_user($field = '')
{
	
    if ('uid' == $field)
        $field = 'id';
	
    return isset($_SESSION['userinfo'][$field]) ? $_SESSION['userinfo'][$field] : '';
}
/*
 *	函数名：getHr
 *	说  明：获取一个用户信息
 *	参  数：$code 人员编码
 *	返回值：数组
 */


function getHr($code){
	if(!$code) return;
	global $db;
	return $db->get_row("SELECT * FROM `sp_hr` where code='$code' AND  deleted=0");
	
	
}
/*
 * 函数名：get_ctfrom_level
 * 功  能：合同来源 树形深度
 * 参  数：$code
 * 返回值： 层数
 */
function get_ctfrom_level($code = '00000000')
{
    if ('00' == substr($code, 0, 2)) {
        $len = 0;
    } elseif ('000000' == substr($code, 2, 6)) { //顶级
        $len = 2;
    } elseif ('0000' == substr($code, 4, 4)) { //二级
        $len = 4;
    } elseif ('00' == substr($code, 6, 2)) { //三级
        $len = 6;
    } else {
        $len = 8;
    }
    return $len;
}
/*
 * 函数名：create_dir
 * 功  能：创建目录
 * 参  数：$path 要创建的目录 支持多层
 * 返回值：无
 */
function create_dir($path)
{
    $path = dirname($path);
    if (!is_dir($path)) {
        create_dir($path);
        @mkdir($path);
        @chmod($path, 0777);
        @fclose(@fopen($path . '/index.html', 'w'));
        @chmod($path . '/index.html', 0777);
    }
}
/*
 * 函数名：readover
 * 功  能：读文件
 * 参  数：$filename 文件名 $method 打开模式
 * 返回值：文件的内容
 */
function readover($filename, $method = 'rb')
{
    strpos($filename, '..') !== false && exit('Forbidden');
    $filedata = '';
    if (($handle = @fopen($filename, $method)) && file_exists($filename)) {
        flock($handle, LOCK_SH);
        $filedata = @fread($handle, filesize($filename));
        fclose($handle);
    }
    return $filedata;
}
 
/**
 * 将?m=enterprise&a=add等网址转化成sp_hr中sys字段需要的格式
 * @param string $str 需处理的字符
 * @param string $ma 返回单独的m值或a值
 * @return string
 */
function urltoauth($str, $ma = 'ma')
{
    if (strpos($str, 'c=') !== false or strpos($str, 'a=') !== false or strpos($str, 'm=') !== false) {
        preg_match('/m=([0-9a-zA_Z]*)/', $str, $m);
        preg_match('/c=([0-9a-zA_Z]*)/', $str, $c);
        preg_match('/a=([0-9a-zA_Z]*)/', $str, $a);
		if(!$m[1])
			$m[1]="admin";
		if(!$c[1])
			$c[1]="index";
		if(!$a[1])
			$a[1]="index";
        switch ($ma) {
            case 'm':
                return $m[1];
			case 'c':
                return $c[1];
            case 'a':
                return $a[1];
                break;
            default:
                return $m[1]."-".$c[1] . ':' . $a[1];
        }
    }
    return $str;
}

/**
 * 是否具有权限
 * @return boolean
 */
function auth()
{
    global $left_nav;
    $sysControlArray = array();
    if (is_array($left_nav))
    foreach ($left_nav as $left_nav_array) {
        if (is_array($left_nav_array))
        foreach ($left_nav_array as $left_nav_array_nav) {
            if (isset($left_nav_array_nav['options']))
                foreach ($left_nav_array_nav['options'] as $key => $options_array) {
                    /* $sysControlArray[] = urltoauth($options_array[1]);
                    if (isset($options_array[3])) {
                        $explode = explode('|', $options_array[3]);
                        foreach ($explode as $explode) {
                            $sysControlArray[] = urltoauth($explode);
                        }
                    } */
					$sysControlArray[] = urltoauth($options_array[1]);
					unset($options_array[1]);
					foreach($options_array as $k=>$val)
					{
						if( strpos($val,'?') && strpos($val,'c=')){
							$explode = explode('|', $val);
							foreach ($explode as $explode) {
                            $sysControlArray[] = urltoauth($explode);
						   }
						}
					}
					
                }
				
        }
    }
	//p($sysControlArray);
    // 不在权限设置范围之内是有权限的
    if (!in_array(urltoauth($_SERVER['REQUEST_URI']), $sysControlArray)) {
        return true;
    }

    // admin		永远有权限
    if ($_SESSION['userinfo']['username'] == 'admin') {
        return true;
    }
    // m或a不同时存在的按标识处理
    if (empty($_GET['c']) or empty($_GET['a'])) {
        return true;
    }
    // 特殊m不检查权限
    $c = array(
        'login',
        'home',
        'plugin',
        'ajax',
		'attachment'
    );
    if (in_array($_GET['c'], $c)) {
        return true;
    }
    // 权限控制
    if (strpos($_SESSION['userinfo']['sys'], urltoauth($_SERVER['REQUEST_URI'])) !== false) {
        return true;
    }
    return false;
}
 
/*
 * 函数名：chk_arr
 * 功  能：判断数组中有array("0000-00-00","0000-00-00 00:00:00","0.00") 返回空
 * 参  数：$arr 数组
 * 返回值：$arr 数组
 */
function chk_arr(&$arr)
{
    if (!is_array($arr))
        return false;
    foreach ($arr as $k => &$val) {
        if (is_array($val))
            chk_arr($val);
        else {
            if (in_array($val, array(
                "0000-00-00",
                "0000-00-00 00:00:00",
                "1970-01-01",
				"0.00"
            )))
                $arr[$k] = "";
        }
    }
    return $arr;
}


/*
 * 函数名：getOrgInfo
* 功  能：通过组织机构代码，获取组织信息
* 参  数：$orgCode 字符串 727536586
* 返回值： 对象
*/

function getOrgInfo($orgCode){
	$ws		=	"http://codeplat.cnca.cn/codecheckws/CodeCheckServicePort?wsdl"; 
	$client =	new SoapClient( $ws); 

	//$orgCode	="727536586";	//需要查询的 组织机构代码
	// $orgCode	="101159619";	//需要查询的 组织机构代码
	$orgUser	=get_option("orgUser");		//账号
	$orgPasd	=get_option('orgPasd');	//密码
	$orgToken	=get_option('orgToken');	//固定密钥

	$dt_psd		=	file_get_contents(DATA_DIR."orgcode.log");	//获取动态密钥

	$jm_dt_psd	=	ecryptdString($orgPasd,$dt_psd);		//加密密码(动态密钥)
	$param2		=	array('arg0' => array('systemCode'=>$orgUser,'password'=>$jm_dt_psd,'orgCode'=>$orgCode));
	$result		=	$client->searchDMInfo($param2);	//echo $result->return->message."<br>";

	if ($result->return->message!='success'){

		$jm_gd_psd	=	ecryptdString($orgPasd,$orgToken);	//加密密码(固定密钥)
		$param1		=	array('arg0' => array('systemCode'=>$orgUser,'password'=>$jm_gd_psd));
		$result		=	$client->searchKEY($param1);//echo $result->return; 获取动态密钥

		$open=fopen(DATA_DIR."orgcode.log","w" );
		fwrite($open,$result->return);
		fclose($open);

		$jm_dt_psd	=	ecryptdString($orgPasd,$result->return);//加密密码(动态密钥)
		$param2		=	array('arg0' => array('systemCode'=>$orgUser,'password'=>$jm_dt_psd,'orgCode'=>$orgCode));
		$result		=	$client->searchDMInfo($param2);
		
	}

/* 
switch ($result->return->message)
{
	case "error:01":
	  echo "01组织机构代码格式不正确";
	  break;
	case "error:03":
	  echo "03缺少必要参数";
	  break;
	case "error:04":
	  echo "04密码解密错误";
	  break;
	case "error:07":
	  echo "07系统标识错误";
	  break;
	case "error:08":
	  echo "08密码错误";
	  break;
	case "error:09":
	  echo "09请求次数超过上限";
	  break;
	case "error:11":
	  echo "11没有符合该条件数据";
	  break;
	case "error:12":
	  echo "12数据条超出限制";
	  break;
	case "error:13":
	  echo "13上传对象在本系统无此记录";
	  break;
	case "success":
	  echo "接口调用成功";
}
 */
return $result->return;

}
/* * 实现AES加密 * $text : 要加密的字符串 */ 
function ecryptdString($text,$mykey){
	$key =pack("H*", $mykey); 
	$pad = 16 - (strlen($text) % 16); 
	$text .=str_repeat(chr($pad), $pad); 
	return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_ECB)); 
} 
	
/* * 实现AES解密 此函数用不到 */ 
function decryptString($crypttext,$mykey){ 
	$key =pack("H*", $mykey); 
	$crypttext=pack("H*",$crypttext); 
	$text =mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $crypttext, MCRYPT_MODE_ECB); 
	$pad = 16 - (strlen($text) % 16);
	$text .=str_repeat(chr($pad), $pad);
	return $text; 
}


/*
 * 函数名：excel_read
* 功  能：读取excel
* 参  数：$filePath excel 文件
* 返回值： 数组
*/

function excel_read($filePath){
	//首先导入PHPExcel
	require_once ROOT.'/theme/Excel/PHPExcel.php'; 
	//建立reader对象
	$PHPReader = new PHPExcel_Reader_Excel2007();
	if(!$PHPReader->canRead($filePath)){
		$PHPReader = new PHPExcel_Reader_Excel5();
		if(!$PHPReader->canRead($filePath)){
			echo 'no Excel';
			return ;
		}
	}
	//建立excel对象，此时你即可以通过excel对象读取文件，也可以通过它写入文件
	$PHPExcel = $PHPReader->load($filePath);
	//获取工作表的数目
	$sheetCount = $PHPExcel->getSheetCount();
	$data=array();
	for($i=0;$i<$sheetCount;$i++){
		$j=$i+1;
		/**读取excel文件中的第一个工作表*/
		$currentSheet = $PHPExcel->getSheet($i);
		/**取得最大的列号*/
		$allColumn = $currentSheet->getHighestColumn();
		// if($allColumn!='A') $allColumn++;
		$allColumn ++;
		/**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow();
		//循环读取每个单元格的内容。注意行从1开始，列从A开始
		for($rowIndex=1;$rowIndex<=$allRow;$rowIndex++){
			for($colIndex='A';$colIndex!=$allColumn;$colIndex++){
				$addr = $colIndex.$rowIndex;
				$cell = $currentSheet->getCell($addr)->getValue();
				if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
					$cell = $cell->__toString();
				
				$data["Sheet".$j][$rowIndex][$colIndex]=trim($cell);
			}

		}
		
	}
	// unset($PHPExcel,$PHPReader);
	return $data;
}

/*
 *	函数名：export_excel
 *	说  明：输出excel
 *	参  数：@param $data 要输的数组 2维数组
 *	返回值：
 */
function export_excel($data,$title){
	require_once ROOT.'/theme/Excel/PHPExcel.php';
	require_once ROOT.'/theme/Excel/PhpExcel/Writer/Excel5.php';
	require_once ROOT.'/theme/Excel/PhpExcel/IOFactory.php'; 
	$objExcel = new PHPExcel(); 
	$objExcel->setActiveSheetIndex(0);  
	// 设置工作薄名称
	$objExcel->getActiveSheet()->setTitle($title);
	$objActSheet = $objExcel->getActiveSheet();
	$i=1;
	foreach($data as $_val){
		$k='A';
		foreach($_val as $val){
			$objActSheet->setCellValueExplicit($k.$i,$val,PHPExcel_Cell_DataType::TYPE_STRING);
			$k++;
			}
		$i++;
	}

	$filename = $title."-".date("Y-m-d").'.xls';
	ob_end_clean();//清除缓冲区,避免乱码
	header("Content-Type: application/force-download");  
	header("Content-Type: application/octet-stream");  
	header("Content-Type: application/download");  
	header('Content-Disposition:inline;filename="'.iconv( 'UTF-8', 'gbk', $filename ).'"');  
	header("Content-Transfer-Encoding: binary");  
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");  
	header("Pragma: no-cache");  
	$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
	$objWriter->save('php://output');  
}


/**对excel里的日期进行格式转化*/ 
function GetData($val){ 
$jd = GregorianToJD(1, 1, 1970); 
$gregorian = JDToGregorian($jd+intval($val)-25569);
$gregorian=date("Y-m-d",strtotime($gregorian)); 
return $gregorian;/**显示格式为 “年-月-日” */ 
} 

/*
 * 	函数名：get_conf
 * 	说  明：获取系统配置
 * 	参  数：$config 选项名
 * 	返回值：成功返回 选英 失败返回 false
 */

function get_conf($item='', $type = 'config') { 
    $sysConfig = include CONF . $type . '.php'; 
	if($item){
		return $sysConfig[$item];
	}else{
		
		return $sysConfig;
	}
	
	
 
}

/*
 * 	函数名：mailTo
 * 	说  明：发送邮件
 * 	参  数：$address  发件人地址 $title 标题 $body 内容 $file 附件
 * 	返回值：成功返回 true 选英 失败返回 错误信息
 */

function mailTo($address,$title,$body,$file='',$fileName=''){
	require_once(STYLESHEET_DIR."PHPMailer/class.phpmailer.php"); //下载的文件必须放在该文件所在目录
	$mail = new PHPMailer(); //建立邮件发送类
	$mail->IsSMTP(); // 使用SMTP方式发送
	$mail->Host = "smtp.163.com"; // 您的企业邮局域名
	$mail->SMTPAuth = true; // 启用SMTP验证功能
	$mail->Username = get_option('mailUser'); // 邮局用户名(请填写完整的email地址)
	$mail->Password = get_option('mailPwd'); // 邮局密码
	$mail->Port=25;
	$mail->From = get_option('mailUser'); //邮件发送者email地址
	$mail->FromName = get_option('mailFromName');
	if(is_array($address)){
		foreach($address as $add){
			$mail->AddAddress("$add");
		}
		
	}else
		$mail->AddAddress("$address");
	if($file)
		$mail->AddAttachment($file,$fileName); // 添加附件
	$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
	$mail->Subject = $title; //邮件标题
	$mail->Body = $body; //邮件内容
	// $mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
	$mail->CharSet = "utf-8"; //设置字符集编码
	if(!$mail->Send())
	{
		$msg="邮件发送失败. <p>";
		$msg.="错误原因: " . $mail->ErrorInfo;
		return $msg;
	}else{

		return true;
	}


/*************************************************

附件：
phpmailer 中文使用说明（简易版）
A开头：
$AltBody--属性
出自：PHPMailer::$AltBody
文件：class.phpmailer.php
说明：该属性的设置是在邮件正文不支持HTML的备用显示
AddAddress--方法
出自：PHPMailer::AddAddress()，文件：class.phpmailer.php
说明：增加收件人。参数1为收件人邮箱，参数2为收件人称呼。例 AddAddress("eb163@eb163.com","eb163")，但参数2可选，AddAddress(eb163@eb163.com)也是可以的。
函数原型：public function AddAddress($address, $name = '') {}
AddAttachment--方法
出自：PHPMailer::AddAttachment()
文件：class.phpmailer.php。
说明：增加附件。
参数：路径，名称，编码，类型。其中，路径为必选，其他为可选
函数原型：
AddAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream'){}
AddBCC--方法
出自：PHPMailer::AddBCC()
文件：class.phpmailer.php
说明：增加一个密送。抄送和密送的区别请看[SMTP发件中的密送和抄送的区别] 。
参数1为地址，参数2为名称。注意此方法只支持在win32下使用SMTP，不支持mail函数
函数原型：public function AddBCC($address, $name = ''){}
AddCC --方法
出自：PHPMailer::AddCC()
文件：class.phpmailer.php
说明：增加一个抄送。抄送和密送的区别请看[SMTP发件中的密送和抄送的区别] 。
参数1为地址，参数2为名称注意此方法只支持在win32下使用SMTP，不支持mail函数
函数原型：public function AddCC($address, $name = '') {}
AddCustomHeader--方法
出自：PHPMailer::AddCustomHeader()
文件：class.phpmailer.php
说明：增加一个自定义的E-mail头部。
参数为头部信息
函数原型：public function AddCustomHeader($custom_header){}
AddEmbeddedImage --方法
出自：PHPMailer::AddEmbeddedImage()
文件：class.phpmailer.php
说明：增加一个嵌入式图片
参数：路径,返回句柄[,名称,编码,类型]
函数原型：public function AddEmbeddedImage($path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream') {}
提示：AddEmbeddedImage(PICTURE_PATH. "index_01.jpg ", "img_01 ", "index_01.jpg ");
在html中引用
AddReplyTo--方法
出自：PHPMailer:: AddRepl
*************************************************/	
}

/*
挂件：兼具插件的功能：

插件：有控制器有模板
挂件：一般是只有模板
 */ 
function widget($args) {
    if (empty($args))
        return false;
    $default = array(
        'app' => 'com',
        'm' => 'widget',
        'a' => 'ep_Info', //挂架标识
        'width' => 750
    );
        
    global $db;
    $args = parse_args($args, $default);
	
 
    //显示选项  
    $ctl_path = $args['app'] . '/control/' . $args['m'] . '/' . $args['a'] . '.Widget.php';
    if (file_exists($ctl_path)) { //挂架控制器
        include $args['app'] . '/control/' . $args['m'] . '/' . $args['a'] . '.Widget.php';  
    } 
    ob_start();
    $located = $args['app'] . '/view/' . $args['m'] . '/' . $args['a'] . '.Widget.htm';
    require $located;
    $result = ob_get_contents();
    ob_end_clean();
    echo $result;
}

/*
 * 	函数名：showFile
 * 	说  明：读出$dir 文件夹下所有文件目录
 * 	参  数：$dir    文件夹
 * 	返回值：成功返回 数组
 */


function showFile($dir)
{
	$encode = mb_detect_encoding($dir, array('UTF-8','GB2312')); 
	if ($encode =='UTF-8'){ 
		$dir=iconv("UTF-8","gb2312",$dir);
	} 
    $files = array();
    if (is_dir($dir)) {
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != "..") {
                    if (is_dir($dir . "/" . $file)) {
                        $files[$file] = showFile($dir . "/" . $file);
                    } else {
                        $files[] = $dir . "/" . $file;
                    }
                }
            }
        }
    }
    closedir($handle);
    return $files;
}

/*
 * 	函数名：showArray
 * 	说  明：将$arr 中的 gb2312 转uft8
 * 	参  数：$$arr    数组
 * 	返回值：成功返回 数组
 */

function showArray($arr){
	$temp=array();
	foreach($arr as $k=>$v){
		if(is_array($v)){
			$temp[iconv("gb2312","UTF-8",$k)]=showArray($v);
		}else{
			$temp[iconv("gb2312","UTF-8",$k)]=iconv("gb2312","UTF-8",$v);
		}
	}
	return $temp;
}

/*
 * 	函数名：https_request 访问 url
 * 	说  明：将$arr 中的 gb2312 转uft8
 * 	参  数：$url 地址 $data  $_POST 数据
 * 	返回值：成功返回 数组
 */


function https_request($url , $data = array()) {
	$curl=curl_init();
	curl_setopt($curl,CURLOPT_URL,$url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	if($data){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$res=curl_exec($curl);
	curl_close($curl);
	return $res;
}

?>