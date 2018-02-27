<?php
!defined('IN_SUPU') && exit('Forbidden');
//用例：分支配置
$step = getgp('step');
if (empty($a) || 'list' == $a) { //分支列表查询
    $resdb = array();
    $total = $db->get_var("SELECT COUNT(*) FROM sp_settings_ctfrom WHERE 1  AND deleted=0 ");
    $query = $db->query("SELECT * FROM sp_settings_ctfrom WHERE 1  AND deleted=0  ORDER BY code ASC");
    while ($rt = $db->fetch_array($query)) {
        $split = ' &nbsp; &nbsp; &nbsp;';
        $space = '';
        if ('000000' == substr($rt['code'], 2, 6)) {
            $space = '';
        } elseif ('0000' == substr($rt['code'], 4, 4)) {
            $space = $split;
        } elseif ('00' == substr($rt['code'], 6, 2)) {
            $space = $split . $split;
        } else {
            $space = $split . $split . $split;
        }
        $rt['space']        = $space;
        $rt['is_stop_V']    = ($rt['is_stop']) ? '停用' : '启用';
        $resdb[$rt['code']] = $rt;
    }
    template('setting/list_ctfrom');
} elseif ('add' == $a || 'edit' == $a) { //添加或者编辑项目所属单位配置信息
    $ct_from     = load('ctfrom');
    $upcode = getgp('upcode');
    $code   = getgp('code');
    if (!$upcode)
        $upcode = '01000000';
    if ($step) { //真正编辑的控制代码
        $name = getgp('name');
        if (empty($name))exit('empty_name');
		
        $c1 = substr($upcode, 0, 2);
        $c2 = substr($upcode, 2, 2);
        $c3 = substr($upcode, 4, 2);
        $c4 = substr($upcode, 6, 2);
        extract($_POST, EXTR_SKIP);
	 
        if (!$code) { 
			//echo 'fffffffffffffff'; 
            if ('00' != $c1 && '00' == $c2) {
                $code = $c1 . $ct_from->get_next($c1) . '0000';
            } elseif ('00' != $c2 && '00' == $c3) {
                $code = $c1 . $c2 . $ct_from->get_next($c1 . $c2) . '00';
            } elseif ('00' != $c3 && '00' == $c4) {
                $code = $c1 . $c2 . $c3 . $ct_from->get_next($c1 . $c2 . $c3);
            } else {
                $r    = $db->get_var("SELECT COUNT( DISTINCT LEFT(code,2) ) FROM sp_settings_ctfrom");
                $code = sprintf("%02d", $r + 1) . '000000';
            }
			$_POST['code']=$code;
			
			
        } 
        unset($_POST['step'], $_POST['upcode']);
  
        $db->replace('settings_ctfrom', $_POST);
 
        update_ctfrom();
        showmsg('success', 'success', "?m=com&c=setting_ctfrom&a=list");
    } else { //显示需要编辑的信息
        if ('edit' == $a) {
            $code = getgp('code');
            $row  = $ct_from->get($code);
            extract($row, EXTR_SKIP);
            $c1 = substr($row['code'], 0, 2);
            $c2 = substr($row['code'], 2, 2);
            $c3 = substr($row['code'], 4, 2);
            $c4 = substr($row['code'], 6, 2);
            if ('00' != $c1 && '00' == $c2) {
                $upcode = '00000000';
            } elseif ('00' != $c2 && '00' == $c3) {
                $upcode = $c1 . '000000';
            } elseif ('00' != $c3 && '00' == $c4) {
                $upcode = $c1 . $c2 . '0000';
            } else {
                $upcode = $c1 . $c2 . $c3 . '00';
            }
            $is_stop_Y = $is_stop_N = '';
            if ($row['is_stop']) {
                $is_stop_Y = 'checked';
            } else {
                $is_stop_N = 'checked';
            }
        }
        if ($upcode && $upcode != '00000000') {
            $upfrom = f_ctfrom($upcode, true);
        }
    }
    template('setting/edit_ctfrom');
} elseif ('del' == $a) {
    $code = getgp('code');
    if ('000000' == substr($code, 2, 6)) {
        $len = 2;
    } elseif ('0000' == substr($code, 4, 4)) {
        $len = 4;
    } elseif ('00' == substr($code, 6, 2)) {
        $len = 6;
    } else {
        $len = 8;
    }
    $where .= " AND LEFT(code, $len) = '" . substr($code, 0, $len) . "'";
    $sql = "SELECT code from sp_settings_ctfrom where 1 $where";
    $res = $db->query($sql);
    while ($rs = $db->fetch_array($res)) {
        $sql = "update sp_settings_ctfrom set deleted=1 where code=$rs[code]";
        $db->query($sql);
    }
    update_ctfrom();
    showmsg('success', 'success', "?m=com&c=setting_ctfrom&a=list");
}
?>