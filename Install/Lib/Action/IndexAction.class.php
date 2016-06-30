<?php
// +----------------------------------------------------------------------
// | dswjcms
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.tifaweb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
// +----------------------------------------------------------------------
// | Author: 宁波市鄞州区天发网络科技有限公司 <dianshiweijin@126.com>
// +----------------------------------------------------------------------
// | Released under the GNU General Public License
// +----------------------------------------------------------------------
defined('THINK_PATH') or exit();
class IndexAction extends Action {
    public function index(){
		$this->assign('state',F('state'));
		$this->display();
    }
	
	//环境检测
	public function Detection(){
		//检查读写权限
		$mdirs=array('Tpl','Conf','Public','Lib/Plugin');
		 foreach($mdirs as $key=>$mdir){
			if(!$this->TestWrite($mdir)) {
				$info='
				<div class="problem">问题：目录无读写权限</div>
							<div class="solution">解决方案：liunx下设置项目所有目录权限为777</div>
							<div class="tutorial"><a target="_blank" href="https://www.baidu.com/s?cl=3&wd=linux%E8%AE%BE%E7%BD%AE%E7%9B%AE%E5%BD%95%E6%9D%83%E9%99%90%E4%B8%BA777" title="点击查看设置方法">设置方法</a></div>
				';
				$this->ajaxReturn(0,$info,0);
			}
			if(!is_readable($mdir)){
				$info='
					<div class="problem">问题：目录无读写权限</div>
								<div class="solution">解决方案：liunx下设置项目所有目录权限为777</div>
								<div class="tutorial"><a target="_blank" href="https://www.baidu.com/s?cl=3&wd=linux%E8%AE%BE%E7%BD%AE%E7%9B%AE%E5%BD%95%E6%9D%83%E9%99%90%E4%B8%BA777" title="点击查看设置方法">设置方法</a></div>
					';
				$this->ajaxReturn(0,$info,0);
			 }

		 }
		 //检查扩展 
		 if (!extension_loaded("gd")) {
			 $info='
					<div class="problem">问题：gd库未安装</div>
								<div class="solution">解决方案：php.ini中，查找“php_gd2”，将前面的#去除，并重启apache</div>
					';
			$this->ajaxReturn(0,$info,0);
		}
		if (!extension_loaded("mysql")) {
			 $info='
					<div class="problem">问题：mysql未安装</div>
								<div class="solution">解决方案：php.ini中，查找"php_mysql"，将前面的#去除，并重启apache</div>
					';
			$this->ajaxReturn(0,$info,0);
		}
		if (!extension_loaded("curl")) {
			 $info='
					<div class="problem">问题：curl库未安装</div>
								<div class="solution">解决方案：php.ini中，查找"php_curl"，将前面的#去除，并重启apache</div>
					';
			$this->ajaxReturn(0,$info,0);
		}
		//检查环境是否支持伪静态
		if (function_exists('apache_get_modules'))
		  {
			$aMods = apache_get_modules();
			$bIsRewrite = in_array('mod_rewrite', $aMods);
		  }
		  else
		  {
			$bIsRewrite = (strtolower(getenv('HTTP_MOD_REWRITE')) == 'on');
		  }
		  if(!$bIsRewrite){
			 $info='
					<div class="problem">问题：Apache mod_rewrite模块未开启</div>
								<div class="solution">解决方案：<br/>1、Apache配置文件httpd.conf中查找mod_rewrite.so，将LoadModule rewrite_module modules/mod_rewrite.so前面的#去掉，重启apache<br/>2、Apache配置文件httpd.conf中查找AllowOverride None 将None改为 All，重启apache
								</div>
					';
			 $this->ajaxReturn(0,$info,0);
		  }
		  //检查当前项目是否在项目根目录或进行多域名配置
		  if(!$this->TestWrite('Public/css/hdocs.css')){
			 $info='
					<div class="problem">问题：当前环境不支持系统安装</div>
								<div class="solution">解决方案：<br/>1、请将项目移到环境根目录，即可以通过http://localhost/index.php直接访问项目。<br/>
								2、将环境进行多域名站点配置，支持通过子域名XX.abc.com的形式访问站点。
								3、如购买的服务器是虚拟主机，请重写伪静态规则。
								</div>
					';
			 $this->ajaxReturn(0,$info,0);
	      }
		  //检测是否是IIS环境
		  if(is_numeric(strpos($_SERVER['SERVER_SOFTWARE'],"iis")) || is_numeric(strpos($_SERVER['SERVER_SOFTWARE'],"Iis"))){
			   $info='
					<div class="problem">问题：项目不支持IIS环境</div>
								<div class="solution">解决方案：更换成APACHE或nginx
								</div>
					';
			 $this->ajaxReturn(0,$info,0);
	      }
		  $this->ajaxReturn(1,1,1);
		 
	}
	
	//数据库检测
	public function Dbmysql(){
		$dbhost=$this->_post('dbhost');
		$dbuser=$this->_post('dbuser');
		$dbpwd=$this->_post('dbpwd');
		$dbname=$this->_post('dbname');
		$conn = @mysql_connect($dbhost,$dbuser,$dbpwd);
		if($conn){
			if(empty($dbname)){
				$info= "<font color='green'>信息正确</font>";
			}else{
				$info = mysql_select_db($dbname,$conn)?"<font color='red'>数据库已经存在，系统将覆盖数据库</font>":"<font color='green'>数据库不存在,系统将自动创建</font>";
			}
		}else{
		  $info= "<font color='red'>数据库连接失败！</font>";
		}
		@mysql_close($conn);
		$this->ajaxReturn(1,$info,1);
	}
	
	//安装
	public function Dbinstallation(){
		$dbhost=$this->_post('dbhost');
		$dbuser=$this->_post('dbuser');
		$dbpwd=$this->_post('dbpwd');
		$dbname=$this->_post('dbname');
		$conn = @mysql_connect($dbhost,$dbuser,$dbpwd);
		if(!$conn){
			@mysql_close($conn);
			$this->ajaxReturn(0,'<font color="red">数据库服务器或登录密码无效</font>',0);
		}
		mysql_query("CREATE DATABASE IF NOT EXISTS `".$dbname."` default   charset   utf8;",$conn);
		if(!mysql_select_db($dbname)){
			@mysql_close($conn);
			$this->ajaxReturn(0,'<font color="red">选择数据库失败，可能是你没权限，请预先创建一个数据库</font>',0);
		}
		mysql_query("set names utf8");
		//写入文件
		$conf="'DB_HOST'		=>'".$dbhost."',//数据库类型 
		 'DB_NAME'		=>'".$dbname."',//数据库名
		 'DB_USER'		=>'".$dbuser."',//用户名
		 'DB_PWD'		=>'".$dbpwd."',//密码
		 'DB_PREFIX'	=>'ds_',//后缀   
		";
		$arrInsert = $this->insertContent("./Conf/config.php", $conf,6,1);
		unlink("./Conf/config.php");
		foreach($arrInsert as $value){
			file_put_contents("./Conf/config.php", $value, FILE_APPEND);
		}   

		//读取SQL文件
		$sqls = file_get_contents('Install/install.sql');
		$sqls = str_replace("\r", "\n", $sqls);
		$sqls = explode(";\n", $sqls);
		$sqls=array_filter($sqls);
		foreach($sqls as $sql){
			$value = trim($sql);
			mysql_query($sql);
		}
		$result = @ mysql_query($sql, $conn);
		if(!$result){
			$this->ajaxReturn(0,'<font color="red">'.mysql_error().'</font>',0);
		}
		@mysql_close($conn);
		F('state',1);
		$this->ajaxReturn(1,'成功',1);
	}
	
	//检查目录是否可写
	private function TestWrite($d){
		if(is_writable($d)){
			return true;
		}else{
			return false;
		}
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
					$arr[] = substr($line, 0, strlen($line)-1) . $s;
				else
					$arr[] = substr($line, 0, $index) . $s . substr($line, $index);
			}else {
				$arr[] = $line;
			}
		}
		fclose($file_handle);
		return $arr; 
	}
}