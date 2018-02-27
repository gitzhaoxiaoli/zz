<?php
!defined('IN_SUPU') && exit('Forbidden');
$tid            = (int) getgp('tid');
$pid            = (int) getgp('pid');
$comment_b_name = getgp('comment_b_name');
foreach ($comment_b_name as $pd_id => $uid) {
    $db->update('project', array(
        'comment_b_name' => $comment_b_name[$pd_id]
    ), array(
        'id' => $pd_id
    ));
}
showmsg('success', 'success', "?c=assess&a=edit&pd_id=$pd_id&tid=$tid#tab-hr");
 