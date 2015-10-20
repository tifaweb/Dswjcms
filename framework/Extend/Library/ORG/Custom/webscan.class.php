<?php
webscan_error();
//拦截开关(1为开启，0关闭)
$webscan_switch=1;
//提交方式拦截(1开启拦截,0关闭拦截,post,get,cookie,referre选择需要拦截的方式)
$webscan_post=1;
$webscan_get=1;
$webscan_cookie=1;
$webscan_referre=1;
//后台白名单,后台操作将不会拦截,添加"|"隔开白名单目录下面默认是网址带 admin  /dede/ 放行
$webscan_white_directory=WEBSCAN_DIRECTORY;
//url白名单,可以自定义添加url白名单,默认是对phpcms的后台url放行
//写法：比如phpcms 后台操作url index.php?m=admin php168的文章提交链接post.php?job=postnew&step=post ,dedecms 空间设置edit_space_info.php
$webscan_white_url = WEBSCAN_URL;
//引用处理类
require_once('webscan_http.class.php');
//防护脚本版本号
define("WEBSCAN_VERSION", '0.1.2.4');
//防护脚本MD5值
define("WEBSCAN_MD5", md5(@file_get_contents(__FILE__)));
/*//get拦截规则
$getfilter = "<.*=(&#\\d+?;?)+?>|<.*(data|src)=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\\(\d+?|sleep\s*?\\([\d\.]+?\\)|load_file\s*?\\()|<[a-z]+?\\b[^>]*?\\bon([a-z]{4,})\s*?=|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT(\\(.+\\)|\\s+?.+?)|UPDATE(\\(.+\\)|\\s+?.+?)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?)FROM(\\(.+\\)|\\s+?.+?)|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
//post拦截规则
$postfilter = "<.*=(&#\\d+?;?)+?>|<.*data=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\\(\d+?|sleep\s*?\\([\d\.]+?\\)|load_file\s*?\\()|<[^>]*?\\b(onerror|onmousemove|onload|onclick|onmouseover)\\b|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT(\\(.+\\)|\\s+?.+?)|UPDATE(\\(.+\\)|\\s+?.+?)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?)FROM(\\(.+\\)|\\s+?.+?)|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
//cookie拦截规则
$cookiefilter = "benchmark\s*?\\(\d+?|sleep\s*?\\([\d\.]+?\\)|load_file\s*?\\(|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT(\\(.+\\)|\\s+?.+?)|UPDATE(\\(.+\\)|\\s+?.+?)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?)FROM(\\(.+\\)|\\s+?.+?)|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";*/

// get拦截规则
$getfilter = "\\<.+javascript:window\\[.{1}\\\\x|<.*=(&#\\d+?;?)+?>|<.*(data|src)=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[a-z]+?\\b[^>]*?\\bon([a-z]{4,})\s*?=|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
// post拦截规则
$postfilter = "<.*=(&#\\d+?;?)+?>|<.*data=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[^>]*?\\b(onerror|onmousemove|onload|onclick|onmouseover)\\b|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
// cookie拦截规则
$cookiefilter = "benchmark\s*?\(.*\)|sleep\s*?\(.*\)|load_file\s*?\\(|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
//获取指令
$webscan_action  = isset($_POST['webscan_act'])&&webscan_cheack() ? trim($_POST['webscan_act']) : '';
//referer获取
$webscan_referer = empty($_SERVER['HTTP_REFERER']) ? array() : array('HTTP_REFERER'=>$_SERVER['HTTP_REFERER']);
/**
 *   关闭用户错误提示
 */
function webscan_error() {
  if (ini_get('display_errors')) {
    ini_set('display_errors', '0');
  }
}

/**
 *  验证是否是官方发出的请求
 */
function webscan_cheack() {
  if($_POST['webscan_rkey']==WEBSCAN_U_KEY){
    return true;
  }
  return false;
}
/**
 *  数据统计回传
 */
function webscan_slog($logs) {
  if(! function_exists('curl_init')) {
    $http=new webscan_http();
    $http->post(WEBSCAN_API_LOG,$logs);
  }
  else{
    webscan_curl(WEBSCAN_API_LOG,$logs);
  }
}
/**
 *  参数拆分
 */
function webscan_arr_foreach($arr) {
  static $str;
  if (!is_array($arr)) {
    return $arr;
  }
  foreach ($arr as $key => $val ) {

    if (is_array($val)) {

      webscan_arr_foreach($val);
    } else {

      $str[] = $val;
    }
  }
  return implode($str);
}
/**
 *  新版文件md5值效验
 */
function webscan_updateck($ve) {
  if($ve!=WEBSCAN_MD5)
  {
    return true;
  }
  return false;
}

/**
 *  防护提示页
 */
function webscan_pape(){
  $pape=<<<HTML
  <html>
  <body style="margin:0; padding:0">
  <center><iframe width="100%" align="center" height="870" frameborder="0" scrolling="no" src="http://www.dswjcms.com/stopattack.html"></iframe></center>
  </body>
  </html>
HTML;
  echo $pape;
}

/**
 *  攻击检查拦截
 */
function webscan_StopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq,$method) {
  $StrFiltValue=webscan_arr_foreach($StrFiltValue);
  if (preg_match("/".$ArrFiltReq."/is",$StrFiltValue)==1){
    webscan_slog(array('ip' => $_SERVER["REMOTE_ADDR"],'time'=>strftime("%Y-%m-%d %H:%M:%S"),'page'=>$_SERVER["PHP_SELF"],'method'=>$method,'rkey'=>$StrFiltKey,'rdata'=>$StrFiltValue,'user_agent'=>$_SERVER['HTTP_USER_AGENT'],'request_url'=>$_SERVER["REQUEST_URI"]));
	$slog='ip='.$_SERVER["REMOTE_ADDR"].',time='.strftime("%Y-%m-%d %H:%M:%S").',page='.$_SERVER["PHP_SELF"].',method='.$method.',rkey='.$StrFiltKey.',rdata='.$StrFiltValue.',user_agent='.$_SERVER['HTTP_USER_AGENT'].',request_url='.$_SERVER["REQUEST_URI"];
	tflog('dangerous.txt',$slog);
	exit(webscan_pape());
  }
  if (preg_match("/".$ArrFiltReq."/is",$StrFiltKey)==1){
    webscan_slog(array('ip' => $_SERVER["REMOTE_ADDR"],'time'=>strftime("%Y-%m-%d %H:%M:%S"),'page'=>$_SERVER["PHP_SELF"],'method'=>$method,'rkey'=>$StrFiltKey,'rdata'=>$StrFiltKey,'user_agent'=>$_SERVER['HTTP_USER_AGENT'],'request_url'=>$_SERVER["REQUEST_URI"]));
    $slog='ip='.$_SERVER["REMOTE_ADDR"].',time='.strftime("%Y-%m-%d %H:%M:%S").',page='.$_SERVER["PHP_SELF"].',method='.$method.',rkey='.$StrFiltKey.',rdata='.$StrFiltKey.',user_agent='.$_SERVER['HTTP_USER_AGENT'].',request_url='.$_SERVER["REQUEST_URI"];
	tflog('dangerous.txt',$slog);
	exit(webscan_pape());
  }
  //$slog='ip='.$_SERVER["REMOTE_ADDR"].',time='.strftime("%Y-%m-%d %H:%M:%S").',page='.$_SERVER["PHP_SELF"].',method='.$method.',rkey='.$StrFiltKey.',rdata='.$StrFiltKey.',user_agent='.$_SERVER['HTTP_USER_AGENT'].',request_url='.$_SERVER["REQUEST_URI"];
  //tflog('ordinary.txt',$slog);
}
/**
 *  拦截目录白名单
 */
function webscan_white($webscan_white_name,$webscan_white_url=array()) {
  $url_path=$_SERVER['PHP_SELF'];
  $url_var=$_SERVER['QUERY_STRING'];
  if (preg_match("/".$webscan_white_name."/is",$url_path)==1) {
    return false;
  }
  foreach ($webscan_white_url as $key => $value) {
    if(!empty($url_var)&&!empty($value)){
      if (stristr($url_path,$key)&&stristr($url_var,$value)) {
        return false;
      }
    }
    elseif (empty($url_var)&&empty($value)) {
      if (stristr($url_path,$key)) {
        return false;
      }
    }

  }

  return true;
}

/**
 *  curl方式提交
 */
function webscan_curl($url , $postdata = array()){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
  $response = curl_exec($ch);
  $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
  curl_close($ch);
  return array('httpcode'=>$httpcode,'response'=>$response);
}

if($webscan_action=='update') {
  //文件更新操作
  $webscan_update_md5=md5(@file_get_contents(WEBSCAN_UPDATE_FILE));
  if (webscan_updateck($webscan_update_md5))
  {
    if (!file_exists(dirname(__FILE__).'/caches_webscan'))
    {
      if (@mkdir(dirname(__FILE__).'/caches_webscan',755)) {
      }
      else{
        exit("file_failed");
      }
    }
    @file_put_contents(dirname(__FILE__).'/caches_webscan/'."update_360.dat", @file_get_contents(WEBSCAN_UPDATE_FILE));

    if(copy(__FILE__,dirname(__FILE__).'/caches_webscan/'."bak_360.dat")&&filesize(dirname(__FILE__).'/caches_webscan/'."update_360.dat")>500&&md5(@file_get_contents(dirname(__FILE__).'/caches_webscan/'."update_360.dat"))==$webscan_update_md5)
    {
      if (!copy(dirname(__FILE__).'/caches_webscan/'."update_360.dat",__FILE__))
      {
        copy(dirname(__FILE__).'/caches_webscan/'."bak_360.dat",__FILE__);
        exit("copy_failed");
      }
      unlink(dirname(__FILE__).'/caches_webscan/'."update_360.dat");
      exit("update_success");
    }
    unlink(dirname(__FILE__).'/caches_webscan/'."update_360.dat");
    exit("failed");
  }
  else{
    exit("news");
  }

}

elseif($webscan_action=="ckinstall") {
  //验证安装与版本信息
  if(! function_exists('curl_init')){
    $web_code=new webscan_http();
    $httpcode=$web_code->request("http://safe.webscan.360.cn");
  }
  else{
    $web_code=webscan_curl("http://safe.webscan.360.cn");
    $httpcode=$web_code['httpcode'];
  }

  exit("1".":".WEBSCAN_VERSION.":".WEBSCAN_MD5.":".WEBSCAN_U_KEY.":".$httpcode);
}

if ($webscan_switch&&webscan_white($webscan_white_directory,$webscan_white_url)) {
  if ($webscan_get) {
    foreach($_GET as $key=>$value) {
      webscan_StopAttack($key,$value,$getfilter,"GET");
    }
  }
  if ($webscan_post) {
    foreach($_POST as $key=>$value) {
      webscan_StopAttack($key,$value,$postfilter,"POST");
    }
  }
  if ($webscan_cookie) {
    foreach($_COOKIE as $key=>$value) {
      webscan_StopAttack($key,$value,$cookiefilter,"COOKIE");
    }
  }
  if ($webscan_referre) {
    foreach($webscan_referer as $key=>$value) {
      webscan_StopAttack($key,$value,$postfilter,"REFERRER");
    }
  }
}

/**
 *  log记录
 *  name  文件名
 *  log   记录信息
 */
function tflog($name,$log){
	if (!file_exists("./temp/Logs/".date("Y-m").$name)) { //注意你的站点的实际路径 
		$fp = fopen("./temp/Logs/".date("Y-m").$name,'w+'); 
		$col =$log."\r\n"; //记录赋值 
		fwrite($fp, $col, strlen($col)); //插入第一条记录 
		fclose($fp); //关闭文件 
	}else{
		$file= fopen("./temp/Logs/".date("Y-m").$name, "a+");
		$col=$log."\r\n";
		fwrite($file,$col);
		fclose($file);
		unset($file);
	}
}

?>
