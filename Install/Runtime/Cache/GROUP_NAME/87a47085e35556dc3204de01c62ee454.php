<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $s_lang?>" />
<title>安装程序 - 内容管理系统</title>
<link href="__ROOT__/Install/Tpl/Public/style.css" rel="stylesheet" type="text/css" />
</head>

<body>

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
					<li class="now">环境检测</li>
					<li>参数配置</li>
					<li>正在安装</li>
					<li>安装完成</li>
				</ul>
			</dd>
		</dl>
	</div>
	<div class="pright">
		<div class="pr-title"><h3>服务器信息</h3></div>
		<table width="726" border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
			<tr>
				<th width="300" align="center"><strong>参数</strong></th>
				<th width="424"><strong>值</strong></th>
			</tr>
			<tr>
					<td><strong>服务器域名</strong></td>
					<td><?php echo ($sp_name); ?></td>
			</tr>
			<tr>
					<td><strong>服务器操作系统</strong></td>
					<td><?php echo ($sp_os); ?></td>
			</tr>
			<tr>
					<td><strong>服务器解译引擎</strong></td>
					<td><?php echo ($sp_server); ?></td>
			</tr>
			<tr>
					<td><strong>PHP版本</strong></td>
					<td><?php echo ($phpv); ?></td>
			</tr>
		
		</table>
		<div class="pr-title"><h3>系统环境检测</h3></div>
		<div style="padding:2px 8px 0px; line-height:33px; height:23px; overflow:hidden; color:#666;">
			系统环境要求必须满足下列所有条件，否则系统或系统部份功能将无法使用。
		</div>
		<table width="726" border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
			<tr>
					<td>GD 支持 </td>
					<td align="center">On</td>
					<td><?php echo ($sp_gd); ?> <small>(不支持将导致与图片相关的大多数功能无法使用或引发警告)</small></td>
			</tr>
			<tr>
					<td>MySQL 支持</td>
					<td align="center">On</td>
					<td><?php echo ($sp_mysql); ?> <small>(不支持无法使用本系统)</small></td>
			</tr>
		</table>
		
		
		<div class="pr-title"><h3>目录权限检测</h3></div>
		<div style="padding:2px 8px 0px; line-height:33px; height:23px; overflow:hidden; color:#666;">
			系统要求必须满足下列所有的目录权限全部可读写的需求才能使用，其它应用目录可安装后在管理后台检测。
		</div>
		<table width="726" border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
			<tr>
				<th width="300" align="center"><strong>目录名</strong></th>
				<th width="212"><strong>读取权限</strong></th>
				<th width="212"><strong>写入权限</strong></th>
			</tr>
            
            <?php if(is_array($results)): foreach($results as $key=>$result): ?><tr>
            <td><?php echo ($result['mdir']); ?></td>
            <td><?php if($result['read']): ?><font color=green>[√]读</font><?php else: ?><font color=red>[×]读</font><?php endif; ?></td>
            <td><?php if($result['write']): ?><font color=green>[√]写</font><?php else: ?><font color=red>[×]写</font><?php endif; ?></td>
            </tr><?php endforeach; endif; ?>
		</table>
		
		<div class="btn-box">
			<input type="button" class="btn-back" value="后退" onclick="window.location.href='__URL__';" />
			<input type="button" class="btn-next" value="继续" onclick="window.location.href='__URL__/install/step/2';" />
		</div>
	</div>
</div>

<div class="foot">

</div>

</body>
</html>