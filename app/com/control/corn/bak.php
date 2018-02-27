<?php 
load('back')->init(get_conf('db_host'), get_conf('db_user'), get_conf('db_pwd'), get_conf('db_name'));
load('back')->backup();