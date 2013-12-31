<?php if (!defined('THINK_PATH')) exit(); if($jump_url == 1 ): elseif($jump_url == 2): ?>

<?php else: ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>Dswjcms后台管理系统-{$Think.config.DS_TOP_POWERED}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="generator" content="Dswjcms! X1.0" />
    <meta name="author" content="Dswjcms! Team and Tf Team" />
    <meta name="copyright" content="2013-2014 Tf Inc." />
	<!-- The styles -->
	<link id="bs-css" href="__PUBLIC__/bootstrap/css/bootstrap-classic.css" rel="stylesheet">
	<style type="text/css">
	  body {
		padding-bottom: 40px;
	  }
	  .sidebar-nav {
		padding: 9px 0;
	  }
	</style>
	
	<link href="__PUBLIC__/bootstrap/css/charisma-app.css" rel="stylesheet">
	<link href="__PUBLIC__/bootstrap/css/jquery-ui-1.8.21.custom.css" rel="stylesheet">
	<link href='__PUBLIC__/bootstrap/css/fullcalendar.css' rel='stylesheet'>
	<link href='__PUBLIC__/bootstrap/css/fullcalendar.print.css' rel='stylesheet'  media='print'>
	<link href='__PUBLIC__/bootstrap/css/chosen.css' rel='stylesheet'>
	<link href='__PUBLIC__/bootstrap/css/uniform.default.css' rel='stylesheet'>
    <link href='__PUBLIC__/bootstrap/css/colorbox.css' rel='stylesheet'>
    <link href="__PUBLIC__/css/docs.css" rel="stylesheet">
	<link href='__PUBLIC__/bootstrap/css/jquery.cleditor.css' rel='stylesheet'>
	<link href='__PUBLIC__/bootstrap/css/jquery.noty.css' rel='stylesheet'>
	<link href='__PUBLIC__/bootstrap/css/noty_theme_default.css' rel='stylesheet'>
	<link href='__PUBLIC__/bootstrap/css/elfinder.min.css' rel='stylesheet'>
	<link href='__PUBLIC__/bootstrap/css/elfinder.theme.css' rel='stylesheet'>
	<link href='__PUBLIC__/bootstrap/css/jquery.iphone.toggle.css' rel='stylesheet'>
	<link href='__PUBLIC__/bootstrap/css/opa-icons.css' rel='stylesheet'>
	<link href='__PUBLIC__/bootstrap/css/uploadify.css' rel='stylesheet'>
    <link href='__PUBLIC__/css/fotorama.css' rel='stylesheet'>
	<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- The fav icon -->
	<link rel="shortcut icon" href="__PUBLIC__/bootstrap/img/favicon.ico">
</head>

<body>
<?php if(!isset($no_visible_elements) || !$no_visible_elements) { ?>
	<!-- topbar starts -->
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>

				<!-- theme selector starts -->
				<div class="btn-group pull-right theme-container" >
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="icon-tint"></i><span class="hidden-phone"> 更换主题 / 皮肤</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu" id="themes">
						<li><a data-value="classic" href="#"><i class="icon-blank"></i> Classic</a></li>
						<li><a data-value="cerulean" href="#"><i class="icon-blank"></i> Cerulean</a></li>
						<li><a data-value="cyborg" href="#"><i class="icon-blank"></i> Cyborg</a></li>
						<li><a data-value="redy" href="#"><i class="icon-blank"></i> Redy</a></li>
						<li><a data-value="journal" href="#"><i class="icon-blank"></i> Journal</a></li>
						<li><a data-value="simplex" href="#"><i class="icon-blank"></i> Simplex</a></li>
						<li><a data-value="slate" href="#"><i class="icon-blank"></i> Slate</a></li>
						<li><a data-value="spacelab" href="#"><i class="icon-blank"></i> Spacelab</a></li>
						<li><a data-value="united" href="#"><i class="icon-blank"></i> United</a></li>
					</ul>
				</div>
				<!-- theme selector ends -->

				<!-- 栏目切换 starts -->
				<div class="btn-group pull-right" >
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="icon-user"></i><span class="hidden-phone"> 栏目切换</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
                                                <li><a href="__ROOT__/Admin/Index/system.html"><i class="icon-blank <?php if(($Url) == "Index"): ?>icon-ok<?php endif; ?>"></i>系统配置</a></li>
                                                <li><a href="__ROOT__/Admin/Basis/linebank.html" ><i class="icon-blank <?php if(($Url) == "linebank"): ?>icon-ok<?php endif; ?>"></i>基础配置</a></li>
                                                <li><a href="__ROOT__/Admin/Site.html" ><i class="icon-blank <?php if(($Url) == "Loan"): ?>icon-ok<?php endif; ?>"></i>文章管理</a></li>
                                                <li><a href="__ROOT__/Admin/Loan/review.html" ><i class="icon-blank <?php if(($Url) == "Loan"): ?>icon-ok<?php endif; ?>"></i>贷款管理</a></li>
                                                <li><a href="__ROOT__/Admin/Audit/entry.html" ><i class="icon-blank <?php if(($Url) == "Loan"): ?>icon-ok<?php endif; ?>"></i>认证管理</a></li>
                                                <li><a href="__ROOT__/Admin/Fund/recharge.html" ><i class="icon-blank <?php if(($Url) == "Loan"): ?>icon-ok<?php endif; ?>"></i>资金管理</a></li>
                                                <li><a href="__ROOT__/Admin/Integral.html" ><i class="icon-blank <?php if(($Url) == "Integral"): ?>icon-ok<?php endif; ?>"></i>积分商城</a></li>
                                                <li><a href="__ROOT__/Admin/Ganged.html" ><i class="icon-blank <?php if(($Url) == "Integral"): ?>icon-ok<?php endif; ?>"></i>联动管理</a></li>
                                                <li><a href="__ROOT__/Admin/Integralconf.html" ><i class="icon-blank <?php if(($Url) == "Integral"): ?>icon-ok<?php endif; ?>"></i>积分配置</a></li>
                                                <!--<li><a href="__ROOT__/Admin/Database.html" ><i class="icon-blank <?php if(($Url) == "Integral"): ?>icon-ok<?php endif; ?>"></i>数据库管理</a></li>-->
                                                <li><a href="__ROOT__/Admin/User.html" ><i class="icon-blank <?php if(($Url) == "Integral"): ?>icon-ok<?php endif; ?>"></i>用户管理</a></li>
												<!--<li><a href="__ROOT__/Admin/Commision/group.html" ><i class="icon-blank <?php if(($Url) == "Integral"): ?>icon-ok<?php endif; ?>"></i>提成管理</a></li>-->
					</ul>
				</div>
				<!-- 栏目切换 ends -->
                                <!-- user dropdown starts -->
				<div class="btn-group pull-right" >
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="icon-user"></i><span class="hidden-phone"> {$Think.session.admin_name}</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a  href="{:U('Admin/Logo/loginout')}" >退出</a></li>
					</ul>
				</div>
				<!-- user dropdown ends -->
                <!-- theme selector starts -->
				<div class="btn-group pull-right theme-container" >
					<a class="btn dropdown-toggle"  href="__ROOT__/Admin.html">
                    	<i class="icon-home"></i> 回到首页
					</a>
				</div>
				<!-- theme selector ends -->
			</div>
		</div>
	</div>
	<!-- topbar ends -->
	<?php } endif; ?>
<!--头部 end -->
<!-- container start --> 
<div class="row-fluid index">
    <div class="jump_top span12">
    	<div class="span12 loan_search center">
          	<?php if(isset($message)): ?><p class="green"><?php echo($message); ?></p>
            <?php else: ?>
            <p class="red"><?php echo($error); ?></p><?php endif; ?>
            <p class="jump">
            页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
            </p>
            </div>
        </div>
    </div>
</div>
<!-- container end -->

<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
<!--底部  start-->
<?php if($jump_url == 1 ): elseif($jump_url == 2): ?>

<?php else: ?>

			<!-- content ends -->
			</div><!--/#content.span10-->
		</div><!--/fluid-row-->

		<hr>
		<footer>
			{$Think.config.DS_POWERED}
			<p class="pull-right">开发：<a href="http://www.tifaweb.com" target="_blank">宁波天发网络技术组</a></p>
            
		</footer>
	{$ajax_list}
	</div><!--/.fluid-container-->
	<!-- external javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	<!-- jQuery -->
	<script src="__PUBLIC__/bootstrap/js/jquery-1.7.2.min.js"></script>
	<!-- jQuery UI -->
	<script src="__PUBLIC__/bootstrap/js/jquery-ui-1.8.21.custom.min.js"></script>
    <!-- 木马轮播插件-->
	<script src="__PUBLIC__/js/fotorama.js"></script>
    <!-- bootstrap-->
    <script src="__PUBLIC__/bootstrap/js/bootstrap.min.js"></script>
	
	<!-- library for cookie management -->
	<script src="__PUBLIC__/bootstrap/js/jquery.cookie.js"></script>
	<!-- calander plugin -->
	<script src='__PUBLIC__/bootstrap/js/fullcalendar.min.js'></script>
	<!-- data table plugin -->
	<script src='__PUBLIC__/bootstrap/js/jquery.dataTables.min.js'></script>

	<!-- chart libraries start -->
	<script src="__PUBLIC__/bootstrap/js/excanvas.js"></script>
	<script src="__PUBLIC__/bootstrap/js/jquery.flot.min.js"></script>
	<script src="__PUBLIC__/bootstrap/js/jquery.flot.pie.min.js"></script>
	<script src="__PUBLIC__/bootstrap/js/jquery.flot.stack.js"></script>
	<script src="__PUBLIC__/bootstrap/js/jquery.flot.resize.min.js"></script>
	<!-- chart libraries end -->

	<!-- select or dropdown enhancer -->
	<script src="__PUBLIC__/bootstrap/js/jquery.chosen.min.js"></script>
	<!-- checkbox, radio, and file input styler -->
	<script src="__PUBLIC__/bootstrap/js/jquery.uniform.min.js"></script>
	<!-- plugin for gallery image view -->
	<script src="__PUBLIC__/bootstrap/js/jquery.colorbox.min.js"></script>
    <!-- editor -->
	<script src="__PUBLIC__/editor/kindeditor-min.js"></script>
	<!-- rich text editor library -->
	<script src="__PUBLIC__/bootstrap/js/jquery.cleditor.min.js"></script>
	<!-- notification plugin -->
	<script src="__PUBLIC__/bootstrap/js/jquery.noty.js"></script>
	<!-- file manager library -->
	<script src="__PUBLIC__/bootstrap/js/jquery.elfinder.min.js"></script>
	<!-- star rating plugin -->
	<script src="__PUBLIC__/bootstrap/js/jquery.raty.min.js"></script>
	<!-- for iOS style toggle switch -->
	<script src="__PUBLIC__/bootstrap/js/jquery.iphone.toggle.js"></script>
	<!-- autogrowing textarea plugin -->
	<script src="__PUBLIC__/bootstrap/js/jquery.autogrow-textarea.js"></script>
	<!-- multiple file upload plugin -->
	<script src="__PUBLIC__/bootstrap/js/jquery.uploadify-3.1.min.js"></script>
	<!-- history.js for cross-browser state change on ajax -->
	<script src="__PUBLIC__/bootstrap/js/jquery.history.js"></script>
	<!-- application script for Charisma demo -->
	<script src="__PUBLIC__/bootstrap/js/charisma.js"></script>
<script>
{$endjs}
</script>
</body>
</html><?php endif; ?>