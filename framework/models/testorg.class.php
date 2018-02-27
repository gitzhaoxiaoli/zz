<?php
/*
企业模型
*/
class testorg extends model
{
    public $_tb = 'settings_test_org'; //企业表名
    public $_pk = 'id'; //企业表主键
	var $appname="org_login"; //网站名称
	var $username; //用户名
	var $userpass; //密码
	var $authtable="account"; //验证用数据表
	var $col_username="username"; //用户名字段
	var $col_password="password"; //用户密码字段
	var $col_banned="banned"; //是否被禁止字段
	var $use_cookie=true; //使用cookie保存sessionid
	var $cookiepath='/'; //cookie路径
	var $cookietime=0; //cookie有效时间
	var $err_mysql="mysql error"; //mysql出错提示
	var $err_auth="username invalid or wrong password"; //用户名无效提示
	var $err_user="user invalid"; //用户无效提示(被封禁)

/**********企业登录相关***********/
	function isLoggedin(){ //判断是否登录
		if(isset($_COOKIE['sid'])){ //如果cookie中保存有sid
			session_id($_COOKIE['sid']);
			session_start();
			if($_SESSION['appname']!=$this->appname ) {
				return false;
			}
			//为了防止不同的程序使用同一个登录类产生冲突，加了个appname作为区分标记
			return true;
		}else{ //如果cookie中未保存sid,则直接检查session
			session_start();
			if($_SESSION['appname'] == $this->appname)
				return true;
		}
		return false;
	}
	function userAuth($username,$userpass){ //企业用户认证
		global $db;
		$this->username=$username;
		$this->userpass=$userpass;
		$user = $db->get_row("SELECT * FROM sp_settings_test_org WHERE 1 AND username = '$username' AND deleted = 0");
		if( $user ){ //找到此用户
			
			if( md5( $userpass ) == $user['userPwd'] ){ //密码匹配
				$this->userinfo=$user;
				$this->setSession();
				if(!setcookie('loginName',$user['username'], current_time('timestamp') + 60*60*24*365 )){
					writeover( ABSPATH . '/data/log2.txt', 'no');
				}
				return 'ok';
			}else{ //密码不匹配
				return '密码不匹配';
			}
		}else{ //没有找到此用户
			return '没有此用户';
		}
	}
	
	function setSession(){ //置session
		if(!isset($_SESSION)){
			$sid=uniqid('sid'); //生成sid
			session_id($sid);
			session_start();
		}
		$_SESSION['appname']=$this->appname; //保存程序名
		$_SESSION['userinfo']=$this->userinfo; //保存用户信息（表中所有字段）
		if($this->use_cookie){ //如果使用cookie保存sid
			setcookie('sid',$sid,null,$this->cookiepath);
			if(!setcookie('sid',$sid,null,$this->cookiepath)){
				$this->errReport("set cookie failed");
				$this->err="set cookie failed";
			}
		}else
			setcookie('sid','',time()-3600); //清除cookie中的sid
	}
	
	function userLogout(){ //用户注销
		session_start();
		unset($_SESSION['userinfo']); //清除session中用户信息
	
		unset($_SESSION['appname']); //清除session中程序名
	
		if(setcookie('sid','',time()-3600)) //清除cookie中的sid
	
			return 'ok';
		else
			return false;
	}
	function errReport($str){ //报错
		if($this->error_report)
			echo "ERROR: $str";
	}
    function add($args)
    {
        global $db;
        
    }
    
    function get($args, $meta = true)
    {
        
    }
    function edit($eid, $args)
    {
        
    }
	
    function del($args)
    {
        
			
    }
   
}
?>