<?php
/*
企业模型
*/
class enterprise extends model
{
    public $_tb = 'enterprises'; //企业表名
    public $_pk = 'eid'; //企业表主键
	var $appname="en_login"; //网站名称
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
		$user = $db->get_row("SELECT * FROM sp_enterprises WHERE 1 AND username = '$username' AND deleted = 0");
		if( $user ){ //找到此用户
			
			if( md5( $userpass ) == $user['passwd'] ){ //密码匹配
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
        $eid = $db->insert('enterprises', $args);
        //处理附加属性
        if ($metas = getgp('meta')){
			foreach($metas as $key => $val){
				if(is_array($val)){
					$metas[$key] = implode('||:||',$val);
				}
			}
			$metas = array_map('strip_tags', $metas);
		}
        if ($metas) {
            $ADDSQL = array();
            foreach ($metas as $meta => $value) {
                $ADDSQL[] = "( '$eid', '$meta', '$value', 'enterprise' )";
            }
            if ($ADDSQL) {
                $sql = "INSERT INTO sp_metas_ep ( ID, meta_name, meta_value, used ) VALUES " . implode(',', $ADDSQL);
                $sql .= " ON DUPLICATE KEY UPDATE meta_value = VALUES( meta_value )";
                $db->query($sql);
            }
        }
        return $eid;
    }
    //获取企业列表:企业列表查询，合同登记列表
    function ep_list()
    {
    }
    //用例：合同列表查询，合同登记列表
    function ep_page_list($args)
    {
        //拼接系统默认搜索条件
    }
    function _list_sql()
    {
    }
    //获取企业信息
    function get($args, $meta = true)
    {
        if (empty($args) || !is_array($args))
            return false;
        global $db;
        $where  = $db->sqls($args, 'AND');
        $result = $db->get_row("SELECT * FROM sp_{$this->_tb} WHERE $where");
        $metas  = ($meta) ? $this->meta($result['eid']) : array();
        if ($metas)
            $result = array_merge($result, $metas);
        return $result;
    }
    function edit($eid, $args)
    {
        if (empty($eid))
            return false;
        global $db;
        $args     = parse_args($args);
        $old_info = $this->get(array(
            'eid' => $eid
        ), false);
        unset($old_info['update_date'], $old_info['update_uid']);
        $n_arr = array_diff_assoc($args, $old_info);
        $o_arr = array();
        foreach (array_keys($n_arr) as $key) {
            $o_arr[$key] = $old_info[$key];
        }
        if ($n_arr) {
            $n_arr['update_date'] = current_time('mysql');
            $n_arr['update_uid']  = current_user('uid');
            $db->update('enterprises', $n_arr, array(
                'eid' => $eid
            ));
        }
        if (isset($n_arr['ctfrom'])) {
            $ctfrom = $n_arr['ctfrom'];
            //同步合同来源
            $db->update('contract', array(
                'ctfrom' => $ctfrom
            ), array(
                'eid' => $eid
            ));
            $db->update('contract_item', array(
                'ctfrom' => $ctfrom
            ), array(
                'eid' => $eid
            ));
            $db->update('project', array(
                'ctfrom' => $ctfrom
            ), array(
                'eid' => $eid
            ));
            $db->update('task', array(
                'ctfrom' => $ctfrom
            ), array(
                'eid' => $eid
            ));
			$db->update('task_audit_team', array(
                'ctfrom' => $ctfrom
            ), array(
                'eid' => $eid
            ));
            $db->update('certificate', array(
                'ctfrom' => $ctfrom
            ), array(
                'eid' => $eid
            ));
            $db->update('attachments', array(
                'ctfrom' => $ctfrom
            ), array(
                'eid' => $eid
            ));
        }
        //处理附加联系人属性
        if ($metas = getgp('meta')){
			
			foreach($metas as $key => $val){
				if(is_array($val)){
					/* if(in_array($key,array('person','person_mph','person_bumen','person_job','person_tel','person_email','person_note'))){
						foreach($val as $k=>$v){
							if($v == ''){
								unset($val[$k]);
							}
						}
					} */
				$metas[$key] = implode('||:||',$val);
				}
			}
			$metas = array_map('strip_tags', $metas);
		}  
		/* p($metas);
		exit; */
        if ($metas) {
            foreach ($metas as $meta => $value) {
                $this->meta($eid, $meta, $value);
            }
        }
    }
	 //通过企业ID获取企业表中的某个字段信息
	public function getEpFieldById($eid, $field = 'ep_name')
    {
        $fieldVal = $this->getField($this->tbl, $field, array(
            'eid' => $eid
        ));
        return $fieldVal;
    }
    function del($args)
    {
        if (empty($args) || !is_array($args))
            return false;
        global $db;
        $args = parse_args($args);
        $eid  = $args['eid'];
        $db->update('enterprises', array(
            'deleted' => 1
        ), $args);
		$db->update('contract', array(
                'deleted' => '1'
            ), array(
                'eid' => $eid
            ));
		$db->update('contract_item', array(
			'deleted' => '1'
		), array(
			'eid' => $eid
		));
		$db->update('project', array(
			'deleted' => '1'
		), array(
			'eid' => $eid
		));
		$db->update('task', array(
			'deleted' => '1'
		), array(
			'eid' => $eid
		));
		$db->update('task_audit_team', array(
			'deleted' => '1'
		), array(
			'eid' => $eid
		));
		$db->update('certificate', array(
			'deleted' => '1'
		), array(
			'eid' => $eid
		));
		$db->update('attachments', array(
			'deleted' => '1'
		), array(
			'eid' => $eid
		));
		$this->del_union($eid);
		$this->del_site($eid);
		//删除资质证书
		$db->delete("attachments_pro",array("eid"=>$eid));
		//删除附表
		$db->del("metas_ep",array("ID"=>$eid));
			
    }
    function meta($eid, $meta_name = '', $meta_value = '')
    {
        if (empty($eid))
            return false;
        global $db;
        $result = '';
        if ($meta_name && $meta_value) {
            $old_metas = $this->meta($eid);
            if (isset($old_metas[$meta_name])) {
                if ($meta_value != $old_metas[$meta_name]) {
                    $db->update("metas_ep", array(
                        "meta_value" => $meta_value
                    ), array(
                        "ID" => $eid,
                        "meta_name" => $meta_name
                    ));
                }
            } else {
                $db->insert("metas_ep", array(
                    "meta_value" => $meta_value,
                    "ID" => $eid,
                    "meta_name" => $meta_name,
                    "used" => "enterprise"
                ));
            }
        } elseif ($meta_name) {
            $result = $db->get_var("SELECT meta_value FROM sp_metas_ep WHERE ID = '$eid' AND meta_name = '$meta_name' AND used = 'enterprise'");
        } else {
            $result = array();
            $query  = $db->query("SELECT * FROM sp_metas_ep WHERE ID = '$eid' AND used = 'enterprise'");
            while ($rt = $db->fetch_array($query)) {
                $result[$rt['meta_name']] = $rt['meta_value'];
            }
        }
        return $result;
    }
    function union_count($eid, $number = 0)
    {
        return $this->_count($eid, 'union', $number);
    }
    function site_count($eid, $number = 0)
    {
        return $this->_count($eid, 'site', $number);
    }
	//删除子公司信息
	function del_union($eid){
		global $db;
		$sql = "select eid from sp_enterprises where parent_id='$eid' and deleted=0";
		$res = $db->query($sql);
		while($row=$db->fetch_array($res)){
			$this->del( array( 'eid' => $row['eid'] ) );
			$this->union_count($eid,-1);
			// 日志
			do {
				log_add($row['eid'], 0, "[说明:客户信息-删除]", NULL, NULL);
			}while(false);
		}
	}
	//删除分场所
	function del_site($eid){
		global $db;
		//删除子公司信息
		$sql = "SELECT * FROM `sp_enterprises_site` WHERE `eid` = '$eid'";
		$res = $db->query($sql);
		while($row=$db->fetch_array($res)){
			$db->delete("enterprises_site",array( 'id' => $row['es_id'] ) );
			$this->site_count($eid,-1);
			// 日志
			do {
				log_add($row['eid'], 0, "[说明:客户信息-删除]", NULL, NULL);
			}while(false);
		}
	}
    function _count($eid, $field, $number = 0)
    {
        if (empty($eid) || empty($field))
            return false;
        global $db;
        $db->query("UPDATE sp_enterprises SET {$field}_count = {$field}_count + $number WHERE eid = '$eid'");
        return true;
    }
}
?>