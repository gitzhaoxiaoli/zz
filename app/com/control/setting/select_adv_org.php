<?php
!defined('IN_SUPU') && exit('Forbidden');
//选择合同来源
$ctfrom_infos=$db->get_results(" SELECT * FROM sp_settings_adv_org where deleted='0' and is_stop='0'");
tpl();
 