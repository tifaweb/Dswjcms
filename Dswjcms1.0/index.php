<?php
define('THINK_PATH', './framework/');
define('APP_NAME', 'App');
define('APP_PATH', './');
define('APP_DEBUG', true);	//调试模式
define('RUNTIME_PATH',APP_PATH.'temp/');
define('HTML_PATH','./htm');
/*
自定义常量
*/
define('DS_ENTERPRISE', '点石为金借贷系统');
define('DS_EN_ENTERPRISE', 'dswjjd');
define('DS_NUMbER', '138777912333');	//授权号，删除可能引响系统稳定性
require(THINK_PATH.'/ThinkPHP.php');
?>