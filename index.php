<?php
//require './temp/~runtime.php';// 替换入口文件为编译缓存文件
define('APP_DEBUG', true);	//调试模式
define('THINK_PATH', './framework/');
define('APP_NAME', 'App');
define('APP_PATH', './');
define('RUNTIME_PATH',APP_PATH.'temp/');
define('HTML_PATH','./htm');
define('DS_ENTERPRISE', '点石为金借贷系统');
define('DS_EN_ENTERPRISE', 'dswjjd');
define('DS_NUMBER', '110');	//授权号，删除可能引响系统稳定性
define('TIFAWEB_DSWJCMS','Dswjcms');	//后台访问地址
require(THINK_PATH.'/ThinkPHP.php');	

?>