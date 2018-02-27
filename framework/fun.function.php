<?php


/**
 * [sendWmsg 发送微信]
 * @param  [type]  $to      [接收人id]
 * @param  [type]  $content [内容]
 * @param  integer $from    [发送人id]
 * @return [type]           [无]
 */
function sendWmsg($to,$content){
    global $db;
    // 用id 从人员表查 tel 唯一标识
    if(is_array($to)){
        $_to = array();
        foreach ($to as $key => $value) {
            $tel = $db -> getField("hr","tel",array("id"=>$value));
            if(!$tel)continue;
            $_to[] = $tel;
        }
    }else{
        $tel = $db -> getField("hr","tel",array("id"=>$to));
        if($tel)$_to = $tel;
    }
    // 如果 不存在 返回 false
    if(!$_to)return false;
    $agent_id = get_option("agent_id");
    require_once ('api/wchat/Token.php');
    require_once "api/wchat/SendMessage.php";
    $corpsecret = get_option("corpsecret");
    $tokenobj = new Token($corpsecret);
    $token = $tokenobj -> getToken();
    $sendMessage = new SendMessage($token);
    $res = $sendMessage->sendText($agent_id,$content,$_to);
    $res = json_decode($res,true);
    if($res['success'] == 1){
        // 发送成功 写入日志
        wlog($to,$content);
    }
    return $res['success'];

}

/**
 * [wlog 记录微信日志]
 * @param  [type] $to      [接收人id]
 * @param  [type] $content [内容]
 * @param  [type] $from    [发送人id]
 * @return [type]          [无]
 */
function wlog($to,$content,$from = 0){
    if(!$from)
        $from = current_user("uid");
    global $db;
    if(is_array($to))
        foreach ($to as $key => $value) {
            if($value)
            $db->insert("wechat_log",array("`from`"=>$from,"`to`"=>$value,"note"=>$content));
        }
    else
        $db->insert("wechat_log",array("`from`"=>$from,"`to`"=>$to,"note"=>$content));


}

/**
 * [deluser 删除人员]
 * @param  [type] $obj [uid]
 * @return [type]       []
 */
function delUser($uid,$groupid){
    if(!$uid or !$groupid)return false;
    require_once ('api/wchat/Token.php');
    require_once ('api/wchat/User.php');
    $tokenobj = new Token();
    $token = $tokenobj -> getToken();
    $user = new User($token);
    $res = json_decode($user->getUserByID($uid),true);
    if($group = $res['department']){
        $_k = array_search($groupid,$res['department']);
        if($_k !== false)
            unset($group[$_k]);

    }
    if(!$group){
        $group = 1;
    }
    return $user -> updateUser($uid,'',$group);
}


/**
 * [addUser 添加人员]
 * @param [type]  $userid     [人员id]
 * @param integer $groupid [组id]
 */
function addUser($userid, $name, $groupid, $mobile = FALSE){
    require_once ('api/wchat/Token.php');
    require_once ('api/wchat/User.php');
    $tokenobj = new Token();
    $token = $tokenobj -> getToken();
    $user = new User($token);
    $res = json_decode($user->getUserByID($userid),true);
    if($res['success'] == '1'){
        $group = $res['department'];
        $groupid && $group[] = $groupid;
        $group = array_unique($group);
        // 人员存在 更新组就可以了
        $results = $user -> updateUser($userid,$name,$group,'',$mobile);

    }else{
        //人员不存在 添加 
        $results = $user -> createUser($userid, $name, $groupid, $mobile);
    }
    //要请关注
    $user -> inviteConcern($userid);
    return $results;
       
}


/**
 * 获取组内的所有人员
 * @param  [type]  $groupid [组ID]
 * @param  integer $type    [0是全部人员 1 是已关注人员]
 * @return [type]           [array人员列表]
 */
function getUserList($groupid,$type = 0){

require_once ('api/wchat/Token.php');
require_once "api/wchat/User.php";
$tokenobj = new Token();
$token = $tokenobj -> getToken();
$user = new User($token);
if(!$type){
    $userList = $user -> getUserList($groupid);
    $userList = json_decode($userList,true);
    return $userList['userlist'];
}
$userList = $user -> getUserList($groupid,0,1);
$userList = json_decode($userList,true);
return $userList['userlist'];

}