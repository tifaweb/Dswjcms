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
 					 $mdirs=array('Tpl','Conf','Public');
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

//读取SQL文件
	$sqls = file_get_contents('Install/install.sql');
	$sqls = str_replace("\r", "\n", $sqls);
	$sqls = explode(";\n", $sqls);
	$sqls=array_filter($sqls);
	foreach($sqls as $sql)
	{
	$value = trim($sql);
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