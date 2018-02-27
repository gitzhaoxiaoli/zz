<?php
!defined('IN_SUPU') && exit('Forbidden');
if ($a == 'index' or $a == 'line' or $a == 'top') {
    tpl($a);
} elseif ($_SESSION[appname] != 'login') {
    if ($_SESSION[appname] == "en_login")
        $left_array = $left_nav['en_enterprise']['en_application']['options'];
    else
        $left_array = $left_nav['testorg']['en_application']['options'];
    tpl("left_en");
} else {
    $op = getgp('op'); //获取点击的右上角菜单
    
    if (current_user('diy_menu') == '') {
        $op_diy_menu = 'main';
    } else {
        $op_diy_menu = current_user('diy_menu');
    }
    !$op && $op = $op_diy_menu; //首次登陆到自定义菜单
    
    
    if ('uc' != $op) {
        $left_nav = $left_nav[$op]; //非个人中心加载
        // tpl('left');
    } else { //加载个人中心，个人中心的菜单动态读取数据库
        $menus = $items = $left_nav = array();
        $query = $db->query("SELECT * FROM sp_user_menus WHERE uid = '" . current_user('uid') . "' ORDER BY vieworder");
        while ($rt = $db->fetch_array($query)) {
            if ('menu' == $rt['mtype']) {
                $menus[$rt['id']] = $rt;
            } else {
                isset($items[$rt['parent_id']]) or $items[$rt['parent_id']] = array();
                $items[$rt['parent_id']][$rt['id']] = $rt;
            }
        }
        
        
        if ($menus) {
            foreach ($menus as $mid => $menu) {
                if (!$items[$mid])
                    continue;
                $left_nav[$mid] = array(
                    'name' => $menu['name'],
                    'options' => array()
                );
                foreach ($items[$mid] as $iid => $item) {
                    $left_nav[$mid]['options'][$iid] = array(
                        $item['name'],
                        $item['jump'],
                        $item['target']
                    );
                }
            }
        }
        
        //tpl('left_menu');
    }
    
    tpl('left');
    
}
?>