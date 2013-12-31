<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $s_lang?>" />
<title>安装程序 - 内容管理系统</title>
<script type="text/javascript" src="__PUBLIC__/Js/jquery.js"></script>
<script src="../include/dedeajax2.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
<!--


   function TestDb()
   {   
	   var dbhost = $('#dbhost').val();
       var dbuser = $('#dbuser').val();
       var dbpwd =  $('#dbpwd').val();
	   //ajax链接数据库
	   $.get('__URL__/dbconnect',{'dbhost':dbhost,'dbuser':dbuser,'dbpwd':dbpwd},function(data){

        $('#dbpwdsta').html(data);
       });
       HaveDB();
   }
   function HaveDB()
   {
       var dbhost = $('#dbhost').val();
       var dbuser = $('#dbuser').val();
       var dbpwd =  $('#dbpwd').val();
       var dbname = $('#dbname').val();
       //ajax链接数据库
       $.get('__URL__/dbconnect',{'dbhost':dbhost,'dbuser':dbuser,'dbpwd':dbpwd,'dbname':dbname},function(data){

        $('#havedbsta').html(data);
       });
   }


  
    function DoInstall()
    {
        //$o('postloader').style.display = 'block';
        document.form1.submit();
    }
-->
</script>
<link href="__ROOT__/Install/Tpl/Public/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id='postloader' class='waitpage'></div>
<form action="__URL__/install/step/3" method="post" name="form1">
<input type="hidden" name="step" value="4" />
<div class="top">

    <div class="top-version">
        <!-- 版本信息 -->
        <h2></h2>
    </div>
</div>

<div class="main">
    <div class="pleft">
        <dl class="setpbox t1">
            <dt>安装步骤</dt>
            <dd>
                <ul>
                    <li class="succeed">许可协议</li>
                    <li class="succeed">环境检测</li>
                    <li class="now">参数配置</li>
                    <li>正在安装</li>
                    <li>安装完成</li>
                </ul>
            </dd>
        </dl>
    </div>
    <div class="pright">
       

        <div class="pr-title"><h3>数据库设定</h3></div>
        <table width="726" border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
            <tr>
                <td class="onetd"><strong>数据库主机：</strong></td>
                <td><input name="dbhost" id="dbhost" type="text" value="localhost" class="input-txt" />
                <small>一般为localhost</small></td>
            </tr>
            <tr>
                <td class="onetd"><strong>数据库用户：</strong></td>
                <td><input name="dbuser" id="dbuser" type="text" value="root" class="input-txt" /></td>
            </tr>
            <tr>
                <td class="onetd"><strong>数据库密码：</strong></td>
                <td>
                  <div style='float:left;margin-right:3px;'><input name="dbpwd" id="dbpwd" type="text" class="input-txt" onChange="TestDb()" /></div>
                  <div style='float:left' id='dbpwdsta'></div>
                </td>
            </tr>
			<tr>
                <td class="onetd"><strong>数据表前缀：</strong></td>
                <td><input name="dbprefix" id="dbprefix" type="text" value="ds_" class="input-txt" />
                        <small>如无特殊需要,请不要修改</small></td>
            </tr>
			<tr>
                <td class="onetd"><strong>数据库名称：</strong></td>
                <td>
                    <div style='float:left;margin-right:3px;'><input name="dbname" id="dbname" type="text" value="" class="input-txt" onChange="HaveDB()" /></div>
					<div style='float:left' id='havedbsta'></div>
                </td>
            </tr>
            <tr>
                <td class="onetd"><strong>数据库编码：</strong></td>
                <td>
                       <input type="radio" name="dblang" id="dblang_utf8" value="utf8" checked="checked" /><label for="dblang_latin1">UTF8</label>
                        <small>仅对5.1+以上版本的MySql选择</small>
                </td>
            </tr>
        </table>

        

        <div class="btn-box">
    <input type="button" class="btn-back" value="后退" onclick="window.location.href='__URL__/install/step/1';" />
            <input type="button" class="btn-next" value="开始安装" onclick="DoInstall();" />
      </div>
    </div>
</div>
<div class="foot">
</div>
</form>
</body>
</html>