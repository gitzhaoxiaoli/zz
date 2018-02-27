<?php
    !defined('IN_SUPU') && exit('Forbidden');

    $cti_id = getgp(cti_id);
    $db->update("contract_item",array("deleted"=>1),array("cti_id" => $cti_id));
    showmsg('success', 'success', "?m=product&c=contract&a=approval");
