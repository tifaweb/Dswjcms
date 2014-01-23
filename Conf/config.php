<?php
return array(
//'配置项'=>'配置值'
 'DB_TYPE'   => 'mysql', // 数据库类型
 'DB_PORT'   => 3306, // 端口        
  'APP_GROUP_LIST' => 'Home,Admin,Api,Win', //项目分组设定
 'TMPL_EXCEPTION_FILE'=>'./Tpl/Home/Logo/error.html',// 定义公共错误模板
 'URL_404_REDIRECT'=>__ROOT__.'/error.html',
 'DS_PATH'=>'',
 'AUTH_CONFIG'=>array(
	'AUTH_ON' => true, //认证开关
	'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
	'AUTH_GROUP' => 'ds_auth_group', //用户组数据表名
	'AUTH_GROUP_ACCESS' => 'ds_auth_group_access', //用户组明细表
	'AUTH_RULE' => 'ds_auth_rule', //权限规则表
	'AUTH_USER' => 'ds_admin'//用户信息表
 ),
 'URL_ROUTER_ON'   => true, //开启路由
 //'DATA_CACHE_TYPE'=>'Memcache',
 'URL_ROUTE_RULES' => array( //定义路由规则
 		'Borrow/index/:mid\s'        			=> 'Borrow/index',
		'Center/invest/:mid\s'        			=> 'Center/invest',
		'Center/loan/:mid\s'         			=> 'Center/loan',
		'Center/security/:mid\s'         		=> 'Center/security',
		'Center/fund/:mid\s'          			=> 'Center/fund',
		'Center/approve/:mid\s'      			=> 'Center/approve',
		'Center/basic/:mid\s'         			=> 'Center/basic',
		'Center/emailVerifyConfirm/:email_audit'=> 'Center/emailVerifyConfirm',
		'Center/stationexit/:id\s'         		=> 'Center/stationexit',
		'Loan/invest/:id\d'						=> 'Loan/invest',
		'Integral/page/:id\d'					=> 'Integral/page',
		'Admin/Index/editsys/:id\d'				=> 'Admin/Index/editsys',
		'Admin/Basis/editlin/:id\d'				=> 'Admin/Basis/editlin',
		'Admin/Basis/delelin/:id\d'				=> 'Admin/Basis/delelin',
		'Admin/Basis/editint/:id\d'				=> 'Admin/Basis/editint',
		'Admin/Basis/deleint/:id\d'				=> 'Admin/Basis/deleint',
		'Admin/Basis/editshu/:id\d'				=> 'Admin/Basis/editshu',
		'Admin/Basis/delesh/:id\d'				=> 'Admin/Basis/delesh',
		'Admin/Basis/editlink/:id\d'			=> 'Admin/Basis/editlink',
		'Admin/Basis/deleli/:id\d'				=> 'Admin/Basis/deleli',
		'Admin/Loan/review_page/:id\d'			=> 'Admin/Loan/review_page',
		'Admin/Fund/withdrawal_page/:id\d'		=> 'Admin/Fund/withdrawal_page',
		'Admin/Fund/recharge_page/:id\d'		=> 'Admin/Fund/recharge_page',
		'Admin/Integral/editgoo/:id\d'			=> 'Admin/Integral/editgoo',
		'Admin/Integral/delego/:id\d'			=> 'Admin/Integral/delego',
		'Admin/Integral/delivery/:id\d'			=> 'Admin/Integral/delivery',
		'Admin/Ganged/index/:id\d'				=> 'Admin/Ganged/index',
		'Admin/Ganged/exitgan/:id\d'			=> 'Admin/Ganged/exitgan',
		'Admin/Integralconf/index/:id\d'		=> 'Admin/Integralconf/index',
		'Admin/Integralconf/exitgan/:id\d'		=> 'Admin/Integralconf/exitgan',
		'Admin/Audit/vip/:id\d'					=> 'Admin/Audit/vip',
		'Admin/Audit/exitgan/:id\d'				=> 'Admin/Audit/exitgan',
	),
	//系统变量不要更改，更改将引响系统正常动作
	'DS_ENTERPRISE'			=>	'点石为金借贷系统',
	'DS_EN_ENTERPRISE'		=>	'dswjjd',
	'DS_TOP_POWERED'		=>	'Powered by Dswjcms!',
	'DS_POWERED'			=>	'<p class="pull-left">Powered by <strong><a href="http://www.dswjcms.com" target="_blank">Dswjcms!</a></strong> <em>X1.0</em><br/>&copy; 20013-2014 <a href="http://www.tifaweb.com" target="_blank">Tf Inc.</a></p>',
);
?>