<?php
/**
 * 单一入口文件
 */
define('ROOT', dirname(__FILE__)); //系统根目录
define('CONF', ROOT . '/data/'); //配置目录
define('DEBUG',0); //是否开启调试模式  
require_once ROOT . '/framework/core.php'; //框架引导文件

