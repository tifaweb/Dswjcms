<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>安装程序 - 内容管理系统</title>
<link href="__ROOT__/Install/Tpl/Public/style.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="top">
	<div class="top-logo">
	</div>
	<!--<div class="top-link">
		<ul>
			<li><a href="http://www.DedeCMS.com" target="_blank">官方网站</a></li>
			<li><a href="http://bbs.DedeCMS.com" target="_blank">技术论坛</a></li>
			<li><a href="http://help.DedeCMS.com" target="_blank">帮助</a></li>
		</ul>
	</div>-->
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
					<li class="now">许可协议</li>
					<li>环境检测</li>
					<li>参数配置</li>
					<li>正在安装</li>
					<li>安装完成</li>
				</ul>
			</dd>
		</dl>
	</div>
	<div class="pright">
		<div class="pr-title"><h3>阅读许可协议</h3></div>
		<div class="pr-agreement">
				
		</div>
		<div class="btn-box">
			<input name="readpact" type="checkbox" id="readpact" value="" /><label for="readpact"><strong class="fc-690 fs-14">我已经阅读并同意此协议</strong></label>
			<input type="button" class="btn-next" value="继续" onclick="document.getElementById('readpact').checked ?window.location.href='__URL__/install/step/1' : alert('您必须同意软件许可协议才能安装！');" />
		</div>
	</div>
</div>

<div class="foot">

</div>

</body>
</html>