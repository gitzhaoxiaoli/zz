<?php


$upload_file_date = getgp('upload_file_date');
$tid = (int)getgp('tid');
// p($tid);
// echo 123;die;

if(!is_null($upload_file_date)){
    if($upload_file_date) {
        $db->update("task", array("upload_file_date" => date("Y-m-d H:i:s")), array("id" => $tid));
        echo "ok";
    }else{
        $db->update("task", array("upload_file_date" => ""), array("id" => $tid));
        echo "ok";
    }
}else{
    echo "error";
}