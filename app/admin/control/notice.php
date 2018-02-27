<?php
!defined('IN_SUPU') && exit('Forbidden');
$notice = load('notice');
$url    = dirname(dirname(__FILE__));
$a      = getgp('a');
if ('add' == $a) {
    $tip_msg = '发布公告';
    tpl('notice/notice_edit');
} else if ($a == 'edit') {
    $tip_msg = '编辑公告';
    tpl('notice/notice_edit');
} else if ($a == 'save') {
    $id               = getgp('id');
    $title               = getgp('title');
	if ($id) {
            $notice->edit($id, array("title"=>$title));
        } else 
            $id = $notice->add(array("title"=>$title));
    //上传公告文档
    $upload           = load('upload');
    $upload->savePath = get_option('upload_notice_dir');
    $value            = array();
    if ($upload->upload()) {
        $info = $upload->getUploadFileInfo();
        if ($_FILES['fileurl']['name']) {
                $filename = $info[0]['savename'];
                $notice->edit($id, array(
                    'filename' => $filename
                ));
            }
        
    }
	showmsg('success', 'success', '?c=notice&a=list');
	
} else if ('del' == $a) {
    $id = getgp('id');
    if ($id) {
        $notice->del($id);
    }
    $REQUEST_URI = '?c=notice&a=list';
    showmsg('success', 'success', $REQUEST_URI);
} else if ($a == 'list') {
    $fields = $join = $where = '';
    $join   = " INNER JOIN sp_hr hr ON hr.id = n.update_uid";
    $datas  = array();
    foreach ($_POST as $k => $v) {
        ${$k} = getgp($k);
    }
    $where = " and n.status >= 0 ";
    if ($name) {
        $sql  = "select id from sp_hr where name like '%$name%' ";
        $uids = array();
        $res  = $db->query($sql);
        while ($row = $db->fetch_array($res)) {
            $uids[] = $row['id'];
        }
        if ($uids) {
            $uid_str = implode("','", $uids);
            $where .= " and n.update_uid in ('$uid_str') ";
        } else {
            $where .= " and n.update_uid='' ";
        }
    }
    if ($title) {
        $where .= " and n.title  like '%$title%' ";
    }
    if ($s_date) {
        $where .= " and n.up_date >= '$s_date' ";
    }
    if ($e_date) {
        $where .= " and n.up_date <= '$e_date' ";
    }
    $total = $db->get_var("SELECT COUNT(*) FROM sp_notice n $join WHERE 1 $where");
    $pages = numfpage($total, 20, "?c=$c&a=$a");
    $sql   = "SELECT n.*,hr.name author FROM sp_notice n $join WHERE 1 $where ORDER BY n.id DESC $pages[limit]";
    $query = $db->query($sql);
    while ($rt = $db->fetch_array($query)) {
        $rt['filename'] = substr($rt['filename'], strlen($rt['id']) + 1);
        $datas[]        = $rt;
    }
    tpl('notice/notice_list');
} else if ($a == 'download') {
    $id          = getgp('id');
    $notice_info = $notice->get($id);
    $file_dir    = get_option('upload_notice_dir');
    $path        = $file_dir . $notice_info['filename'];
    $path        = iconv('UTF-8', 'GB2312', $path);
    header('Last-Modified: ' . date('D, d M Y H:i:s', time()) . ' GMT');
    header('Expires: ' . date('D, d M Y H:i:s', time()) . ' GMT');
    header('Cache-control: max-age=86400');
    header('Content-Encoding: none');
    //@HBJ 2013-9-18 解决各个浏览器下载兼容问题
    $filename         = $notice_info['title'] . substr(strrchr($notice_info['filename'], '.'), 0);
    $encoded_filename = urlencode($filename);
    $encoded_filename = str_replace("+", "%20", $encoded_filename);
    $ua               = $_SERVER["HTTP_USER_AGENT"];
    if (preg_match("/MSIE/i", $ua)) {
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } elseif (preg_match("/Firefox/i", $ua)) {
        header('Content-Disposition: attachment; filename*="utf8/' . $filename . '"');
    } else {
        header('Content-Disposition: attachment; filename="' . $filename . '"');
    }
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: binary");
    ob_clean();
    flush();
    echo file_get_contents($path);
    exit;
}
?>