<?php
!defined('IN_SUPU') && exit('Forbidden');
//系统下载操作，企业，人员
$class = $_GET['class'] ? $_GET['class'] : 'enterprise';

$att = load("attachment");
if ($class == 'enterprise') {
    $file_dir = get_option('upload_ep_dir');
} elseif ($class == 'zz') {
    $file_dir   = get_option('upload_zz_dir');
    $att->table = 'attachments_pro';
} elseif ($class == 'pro') {
    $file_dir = get_option('upload_pro_dir');
} else {
    $file_dir   = get_option('upload_hr_dir');
    $att->table = 'hr_archives';
}
$att->attachdir = $file_dir; //配置下载路径

if ('down' == $a) { //单个文档下载
    $aid = getgp('aid');
    
    $att->down($aid);
} elseif ('batdown' == $a) { //批量下载文档
    $aids = getgp('aid');
    if ($aids) {
        $aids = array_unique($aids);
        $att->batdown($aids);
    } else {
        echo "<script>alert('请至少选择一个 ! !');history.go(-1);</script>";
    }
    
} else if ('del' == $a) { //删除上传的文档
    if ($_SESSION[appname] == 'en_login') {
        $uid = $_SESSION[userinfo][eid];
    } else
        $uid = $_SESSION[userinfo][id];
    if ($aid = getgp('aid')) {
        $a_info = $att->get($aid);
        if ($a_info[create_uid] == $uid) {
            $att->del($aid);
            echo "<script>history.go(-1);</script>";
        }
    }
    echo "<script>alert('没有删除！');history.go(-1);</script>";
    
}

?>