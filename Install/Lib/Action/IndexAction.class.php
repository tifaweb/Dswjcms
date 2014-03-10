<?php

class IndexAction extends Action {
    public function index(){
	 $this->display();
    }

	public function insertContent($source, $s, $iLine, $index) {
		$file_handle = fopen($source, "r");
		$i = 0;
		$arr = array();
		while (!feof($file_handle)) {
			$line = fgets($file_handle);
			++$i;
			if ($i == $iLine) {
				if($index == strlen($line)-1)
					$arr[] = substr($line, 0, strlen($line)-1) . $s . "n";
				else
					$arr[] = substr($line, 0, $index) . $s . substr($line, $index);
			}else {
				$arr[] = $line;
			}
		}
		fclose($file_handle);
		return $arr; 
	}
	
 	public function install()
 	{
 		$step=$this->_get('step');
 		
 		switch($step)
 		{

 			case "1": 
 					//获取服务器信息
					$phpv = phpversion();
					$sp_os = PHP_OS;
					$sp_gd = $this->gdversion();
					$sp_server = $_SERVER['SERVER_SOFTWARE'];
					$sp_host = (empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR']);
					$sp_name = $_SERVER['SERVER_NAME'];
					$sp_max_execution_time = ini_get('max_execution_time');
					$sp_allow_reference = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
					$sp_allow_url_fopen = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
					$sp_safe_mode = (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');
					$sp_gd = ($sp_gd>0 ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
					$sp_mysql = (function_exists('mysql_connect') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');

					if($sp_mysql=='<font color=red>[×]Off</font>')
					$sp_mysql_err = TRUE;
					else
					$sp_mysql_err = FALSE;
					$this->assign('sp_name',$sp_name);//服务器域名
					$this->assign('sp_os',$sp_os);//服务器操作系统
 					$this->assign('sp_server',$sp_server);//服务器解译引擎
 					$this->assign('phpv',$phpv);//PHP版本
                  
          $this->assign('sp_allow_url_fopen',$sp_allow_url_fopen);//allow_url_fopen
          $this->assign('sp_safe_mode',$sp_safe_mode);//safe_mode
          $this->assign('sp_gd',$sp_gd);//sp_gd
          $this->assign('sp_mysql',$sp_mysql);//MySQL

 			    //检查目录的可写
 			     $results=array();
 					 $mdirs=array('Tpl','Conf','Public','Common');
 					 foreach($mdirs as $key=>$mdir)
 					 {
						$results[$key]['mdir']=$mdir;
 					 	if($this->TestWrite($mdir)) 		
 					 		$results[$key]['write']=1;
 					 	else
 					 		$results[$key]['write']=0;

   					if(is_readable($mdir))
   							$results[$key]['read']=1;
   					else
   							$results[$key]['read']=0;

 					 }
 					 $this->assign('results',$results);
 					 $this->display('step-1');
 					 break;
 			case "2":
			   $sessionprefix = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(1000,9999).chr(mt_rand(ord('A'),ord('Z')))._;
			   $this->assign('sessionprefix',$sessionprefix);
           $this->assign('baseurl',$_SERVER['HTTP_HOST']);
 					 $this->display('step-2');
 					 break;
 			case "3": 
           header("Content-type: text/html; charset=utf-8"); 
           $dbhost=$this->_post('dbhost');
           $dbuser=$this->_post('dbuser');
           $dbpwd=$this->_post('dbpwd');
           $dbname=$this->_post('dbname');
           $dbprefix=$this->_post('dbprefix');
           $adminuser=$this->_post('adminuser');
           $adminpwd=md5($this->_post('adminpwd'));
           $webname=$this->_post('webname');
		   $baseurl=$this->_post('baseurl');
		   $sessionprefix=$this->_post('sessionprefix');
           $conn = mysql_connect($dbhost,$dbuser,$dbpwd) or die("<script>alert('数据库服务器或登录密码无效，\\n\\n无法连接数据库，请重新设定！');history.go(-1);</script>");
           mysql_query("CREATE DATABASE IF NOT EXISTS `".$dbname."` default   charset   utf8;",$conn);
           mysql_select_db($dbname) or die("<script>alert('选择数据库失败，可能是你没权限，请预先创建一个数据库！');history.go(-1);</script>");
           mysql_query("set names utf8");
//写入文件
$conf="'DB_HOST'		=>'".$dbhost."',//数据库类型 
 'DB_NAME'		=>'".$dbname."',//数据库名
 'DB_USER'		=>'".$dbuser."',//用户名
 'DB_PWD'		=>'".$dbpwd."',//密码
 'DB_PREFIX'	=>'".$dbprefix."',//后缀   
";
$arrInsert = $this->insertContent("./Conf/config.php", $conf,6,1);
unlink("./Conf/config.php");
foreach($arrInsert as $value)
{
	file_put_contents("./Conf/config.php", $value, FILE_APPEND);
}   

//导入sql
$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}admin`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}admin` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`username` varchar(16) NOT NULL,
`email` varchar(255) NOT NULL,
`password` char(32) NOT NULL,
`time` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5;";
$sqls[]="INSERT INTO `{$dbprefix}admin` (`id`, `username`, `email`, `password`, `time`) VALUES
(1, 'admin', 'admin@123.com', '75634e7bc5f9c86abc6a516e0dba6808', 0);";

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}article`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) DEFAULT NULL,
  `title` varchar(30) NOT NULL,
  `introtext` mediumtext NOT NULL,
  `published` tinyint(1) DEFAULT '1',
  `catid` int(11) DEFAULT NULL,
  `user_id` int(10) DEFAULT '1',
  `order` int(11) DEFAULT '1',
  `access` tinyint(3) DEFAULT '1',
  `is_comment` tinyint(1) DEFAULT '0',
  `keyword` varchar(100) DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `addtime` char(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;";		
$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}article_add`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}article_add` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modified` char(10) DEFAULT NULL COMMENT '修改时间',
  `modified_by` int(11) DEFAULT NULL COMMENT '修改者编号',
  `hits` int(11) DEFAULT NULL COMMENT '点积数',
  `Integration` int(11) DEFAULT NULL COMMENT ' 积分',
  `comment` int(11) DEFAULT NULL COMMENT '评论数',
  `litpic` varchar(150) DEFAULT NULL COMMENT '缩览图',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;";	

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}auth_group`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `rules` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;";	
$sqls[]="INSERT INTO `{$dbprefix}auth_group` (`id`, `title`, `rules`) VALUES
(1, '管理员', ',12,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65');";	

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}auth_group_access`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_2` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";	

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}auth_rule`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `fid` tinyint(2) NOT NULL,
  `condition` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;";	
$sqls[]="INSERT INTO `{$dbprefix}auth_rule` (`id`, `name`, `type`, `fid`, `condition`) VALUES
(1, '后台首页', 0, 1, 'Admin/Index/index'),
(2, '系统设置', 0, 2, 'Admin/Index/system'),
(3, '系统参数编辑（单条）', 0, 2, 'Admin/Index/editsys'),
(4, '系统参数保存（所有）', 0, 2, 'Admin/Index/savesy'),
(5, '删除系统参数', 0, 2, 'Admin/Index/delesy'),
(6, '邮箱设置', 0, 2, 'Admin/Index/email'),
(7, '邮箱设置修改', 0, 2, 'Admin/Index/email_send'),
(8, '管理员操作记录', 0, 2, 'Admin/Index/operation'),
(9, '管理员操作记录导出', 0, 2, 'Admin/Index/adminExport'),
(10, '用户操作记录', 0, 2, 'Admin/Index/userrecord'),
(11, '用户操作记录导出', 0, 2, 'Admin/Index/userExport'),
(12, '轮播图片', 0, 3, 'Admin/Basis/shuffling'),
(13, '添加轮播图片', 0, 3, 'Admin/Basis/addsh'),
(14, '更新轮播图片', 0, 3, 'Admin/Basis/editsh'),
(15, '更新轮播图片页', 0, 3, 'Admin/Basis/editshu'),
(16, '轮播图片状态排序修改', 0, 3, 'Admin/Basis/savesh'),
(17, '友情链接', 0, 3, 'Admin/Basis/links'),
(18, '添加友情链接', 0, 3, 'Admin/Basis/addli'),
(19, '更新友情链接', 0, 3, 'Admin/Basis/editli'),
(20, '更新友情链接页', 0, 3, 'Admin/Basis/editlink'),
(21, '友情链接状态排序修改', 0, 3, 'Admin/Basis/saveli'),
(22, '数据库备份', 0, 3, 'Admin/Basis/backup'),
(23, '数据库优化', 0, 3, 'Admin/Basis/optimization'),
(24, '栏目管理', 0, 4, 'Admin/Site/index'),
(25, '添加栏目页', 0, 4, 'Admin/Site/addSite'),
(26, '删除栏目', 0, 4, 'Admin/Site/delSite'),
(27, '内容管理', 0, 4, 'Admin/Site/articleList'),
(28, '添加文章页', 0, 4, 'Admin/Site/addArticle'),
(29, '添加文章', 0, 4, 'Admin/Site/saveArticle'),
(30, '文章编辑页', 0, 4, 'Admin/Site/editArticle'),
(31, '文章栏目切换', 0, 4, 'Admin/Site/articleList'),
(32, '普通用户管理', 0, 11, 'Admin/User/index'),
(33, '普通用户删除', 0, 11, 'Admin/User/exituse'),
(34, '管理组管理', 0, 11, 'Admin/User/userGroups'),
(35, '添加管理组', 0, 11, 'Admin/User/addGroup'),
(36, '管理组用户管理', 0, 11, 'Admin/User/viewUser'),
(37, '添加管理组用户', 0, 11, 'Admin/User/saveUser'),
(38, '删除管理组用户', 0, 11, 'Admin/User/delGroupUser'),
(39, '分配权限', 0, 11, 'Admin/User/editUserGroups'),
(40, '管理员管理', 0, 11, 'Admin/User/manage'),
(41, '删除管理员', 0, 11, 'Admin/User/exitman'),
(42, '删除权限', 0, 11, 'Admin/User/exitcom'),
(43, '界面风格', 0, 2, 'Admin/Index/colour'),
(44, '界面风格刷新', 0, 2, 'Admin/Index/colourRefresh'),
(45, '界面风格设为默认', 0, 2, 'Admin/Index/setDefault'),
(46, '微信界面风格', 0, 2, 'Admin/Index/wcolour'),
(47, '微信界面风格刷新', 0, 2, 'Admin/Index/wcolourRefresh'),
(48, '微信界面风格设为默认', 0, 2, 'Admin/Index/wsetDefault'),
(49, '栏目编辑', 0, 4, 'Admin/Site/editSite'),
(50, '栏目编辑保存', 0, 4, 'Admin/Site/upda'),
(51, '添加栏目', 0, 4, 'Admin/Site/add'),
(52, '删除文章', 0, 4, 'Admin/Site/dellelist'),
(53, '联动编辑', 0, 9, 'Admin/Ganged/upda'),
(54, '添加联动', 0, 9, 'Admin/Ganged/add'),
(55, '删除联动', 0, 9, 'Admin/Ganged/exitgan'),
(56, '用户管理用户详情', 0, 11, 'Admin/User/userajax'),
(57, '普通用户密码修改', 0, 11, 'Admin/User/passajax'),
(58, '普通用户密码修改保存', 0, 11, 'Admin/User/upda'),
(59, '删除普通用户', 0, 11, 'Admin/User/exituse'),
(60, '管理员编辑', 0, 11, 'Admin/User/adminajax'),
(61, '删除管理员', 0, 11, 'Admin/User/exitman'),
(62, '权限管理', 0, 11, 'Admin/User/competence'),
(63, '添加权限', 0, 11, 'Admin/User/add'),
(64, '编辑权限', 0, 11, 'Admin/User/editajax'),
(65, '删除权限', 0, 11, 'Admin/User/exitcom');";		

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}links`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) NOT NULL,
  `url` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `time` varchar(12) NOT NULL,
  `img` varchar(25) NOT NULL,
  `order` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}operation`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}operation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `page` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `type` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ip` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='操作记录' AUTO_INCREMENT=1 ;";	

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}shuffling`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}shuffling` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `time` varchar(12) NOT NULL,
  `img` varchar(25) NOT NULL,
  `order` tinyint(1) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='轮播' AUTO_INCREMENT=9 ;";	

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}site`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` tinyint(4) DEFAULT '1',
  `pid` int(11) DEFAULT '0',
  `aid` int(11) DEFAULT NULL,
  `catpid` varchar(100) CHARACTER SET latin1 DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `keyword` varchar(100) DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `page_tpl` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `list_tpl` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `content_tpl` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `link` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `type` tinyint(1) DEFAULT '1',
  `orde` int(11) NOT NULL DEFAULT '5',
  `addtime` char(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;";	
$sqls[]="INSERT INTO `{$dbprefix}site` (`id`, `order`, `pid`, `aid`, `catpid`, `title`, `keyword`, `remark`, `page_tpl`, `list_tpl`, `content_tpl`, `link`, `user_id`, `status`, `type`, `orde`, `addtime`) VALUES
(1, 1, 0, 1, '0', '关于我们', '', '', 'page', 'list', 'page', 'about', 22, 1, 3, 7, '1385889550'),
(2, 1, 0, 2, '0', '首页', 'Dswjcms,CMS,PHP,Thinkphp,完全免费开源', '新Dswjcms是基于TP+采用Bootstrap前端开发，整合Charisma后台的CMS，软件依于GPLV2协议发布。', 'page', 'list', 'content', '/', 22, 1, 1, 1, '1384765588'),
(4, 1, 0, 4, '0', '我的账户', '', '', 'page', 'list', 'content', '/Center.html', 22, 1, 1, 4, '1384762467'),
(5, 1, 0, 5, '0', '登陆', '', '', 'page', 'list', 'content', '/Logo/login.html', 22, 1, 1, 5, '1384764322'),
(6, 1, 0, 6, '0', '注册', '', '', 'page', 'list', 'content', '/Logo/register.html', 22, 1, 1, 5, '1384764337'),
(7, 1, 0, 7, '0', '忘记密码', '', '', 'page', 'list', 'content', '/Logo/forgotpass.html', 22, 1, 1, 5, '1384764387'),
(8, 1, 0, 8, '0', '联系我们', '', '', 'page', 'list', 'page', 'contact', 22, 1, 2, 5, '1385002773');";	

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}site_add`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}site_add` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `litpic` varchar(150) DEFAULT NULL,
  `model` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `uptime` char(10) CHARACTER SET latin1 DEFAULT NULL,
  `upid` int(11) DEFAULT NULL,
  `upip` varchar(20) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;";	
$sqls[]="
	INSERT INTO `{$dbprefix}site_add` (`id`, `litpic`, `model`, `uptime`, `upid`, `upip`, `content`) VALUES

(1, NULL, 'article', '1384749803', NULL, NULL, '<p>
	Dswjcms0系列核心版是宁波市鄞州区天发网络科技公司独家发布的唯一完全免费的PHP CMS
</p>
<p>
	Dswjcms的发布只能过www.tifaweb.com或www.dswjcms.com发布最新版本和更新包
</p>
<p>
	Dswjcms除0系列外的其它版本都非完全免费源，请根据官方说明进行使用。
</p>
<p>
	Dswjcms0系列核心版允许基本该CMS发布、运营、转售、销售等行业，本公司不对Dswjcms0系统的派生版本负任何法律责任。
</p>
<p>
	基于Dswjcms0系列发布的派生版本不可直接复制Dswjcms其它系列源码销加修改发布，发布派生版本需基于GPL协议，并保留Dswjcms0系列源码中其它公司/个人和本公司的版权申明。
</p>
<p>
	Dswjcms派生版发布需注明\"基于Dswjcms\"发布，但无需在界面中显示
</p>
<p>
	Dswjcms最终解析权归宁波市鄞州区天发网络科技有限公司所有
</p>'),
(2, NULL, 'article', '1384764322', NULL, NULL, ''),
(4, NULL, 'article', '1384764366', NULL, NULL, ''),
(5, NULL, 'article', '1384764670', NULL, NULL, ''),
(6, NULL, 'article', '1384997605', NULL, NULL, ''),
(7, NULL, 'article', '1385780428', NULL, NULL, ''),
(8, NULL, 'article', '1385780428', NULL, NULL, '<p>
	只要您认可Dswjcms产品，对Dswjcms前景看好，那就加入我们吧
</p>
<p>
	我们全球诚招合作伙伴/代理商，共同创造财富，打下一片天。
</p>
<p>
	宁波市鄞州区天发网络科技有限公司
</p>
<p>
	联系人：庞经理
</p>
<p>
	手&nbsp;&nbsp; 机：13081971646
</p>
<p>
	Q&nbsp;&nbsp; Q：602132284
</p>
<p>
	地&nbsp;&nbsp; 址：宁波市鄞州区南部商务区宁波商会B605
</p>
<p>
	QQ&nbsp; 群：242198978
</p>');";

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}smtp`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}smtp` (
  `id` tinyint(1) NOT NULL,
  `smtp` varchar(255) NOT NULL,
  `validation` tinyint(1) NOT NULL,
  `send_email` varchar(255) NOT NULL,
  `password` varchar(15) NOT NULL,
  `addresser` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `send_email` (`send_email`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";	
$sqls[]="INSERT INTO `{$dbprefix}smtp` (`id`, `smtp`, `validation`, `send_email`, `password`, `addresser`) VALUES
(1, 'smtp.126.com', 1, 'tifawebcesi@126.com', 'abcd12345', '宁波天发网络');";

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}system`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}system` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `state` varchar(25) NOT NULL,
  `prompt` varchar(30) NOT NULL,
  `value` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;";	
$sqls[]="INSERT INTO `{$dbprefix}system` (`id`, `state`, `prompt`, `value`, `name`, `type`) VALUES
(1, '网站名称', '请输入网站名称', 'Dswjcms', 'sys_name', 1),
(2, '网站关键字', '请输入关键字', 'Dswjcms,CMS,PHP,Thinkphp,完全免费开源', 'sys_keyword', 1),
(3, '网站描述', '请输入描述', '新Dswjcms是基于TP+采用Bootstrap前端开发，整合Charisma后台的CMS，软件依于GPLV2协议发布。', 'sys_describe', 2),
(4, '网站LOGO', '请输入LOGO', '1388022762.5063.png', 'sys_logo', 3),
(5, '联系电话', '请输入电话', '0574-8888888', 'sys_phone', 1),
(6, '手机', '请输入手机号', '13088888888', 'sys_cellphone', 1),
(7, '地址', '请输入地址', '上海市嘉定区华亭镇', 'sys_address', 1),
(8, '邮箱', '请输入邮箱', 'pang453758463@163.com', 'sys_email', 1),
(9, '联系人', '请输入联系人', 'dscms管理员', 'sys_contact', 1),
(10, '邮编', '请输入邮编', '888888', 'sys_code', 1),
(11, '传真', '请输入传真', '0574-88888888', 'sys_fax', 1),
(12, '版权信息', '请输入版权信息', 'Copyright ©2013-2014 宁波天发网络所有', 'sys_copy', 1);
";	

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}user`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` char(32) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	

$sqls[]="DROP TABLE IF EXISTS `{$dbprefix}user_log`;";
$sqls[]="CREATE TABLE IF NOT EXISTS `{$dbprefix}user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `actionname` varchar(40) NOT NULL,
  `page` varchar(100) NOT NULL,
  `time` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	


          foreach($sqls as $sql)
          {
            mysql_query($sql);
          }
        $result = @ mysql_query($sql, $conn) or die(mysql_error());
	    while($row=mysql_fetch_array($result))
	    {
		  $datalists[]=$row;
		}
	   foreach($datalists as $datalist)
    {
        $data[$datalist['name']]=$datalist['content'];
      }
	  	  echo "安装成功<script> setTimeout(\"window.location.href='/'\",1000);</script>";
          break;
          case '4':
              $this->display('step-3');
 		}
 	
 	}
//检查数据库是否可以链接
public function dbconnect()
{
	$dbhost=$this->_get('dbhost');
  $dbuser=$this->_get('dbuser');
  $dbpwd=$this->_get('dbpwd');
  $dbname=$this->_get('dbname');
  header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
  $conn = @mysql_connect($dbhost,$dbuser,$dbpwd);
  if($conn)
  {
  if(empty($dbname)){
    echo "<font color='green'>信息正确</font>";
  }else{
    $info = mysql_select_db($dbname,$conn)?"<font color='red'>数据库已经存在，系统将覆盖数据库</font>":"<font color='green'>数据库不存在,系统将自动创建</font>";
    echo $info;
  }
  }
  else
  {
      echo "<font color='red'>数据库连接失败！</font>";
  }
  @mysql_close($conn);
  exit();

} 	
//检查目录是否可写
private function TestWrite($d)
{
    $tfile = '_dedet.txt';
    $d = preg_replace("#\/$#", '', $d);
    $fp = @fopen($d.'/'.$tfile,'w');
    if(!$fp) return false;
    else
    {
        fclose($fp);
        $rs = @unlink($d.'/'.$tfile);
        if($rs) return true;
        else return false;
    }
}
private function gdversion()
{
  //没启用php.ini函数的情况下如果有GD默认视作2.0以上版本
  if(!function_exists('phpinfo'))
  {
      if(function_exists('imagecreate')) return '2.0';
      else return 0;
  }
  else
  {
    ob_start();
    phpinfo(8);
    $module_info = ob_get_contents();
    ob_end_clean();
    if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info,$matches)) {   $gdversion_h = $matches[1];  }
    else {  $gdversion_h = 0; }
    return $gdversion_h;
  }
}


}