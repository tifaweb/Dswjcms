var APP='/install.php/';
$(document).ready(function(){
	//环境检测
	$('#sp-1 .btn').click(function(){
		$.ajax({
		   type: "POST",
		   url: APP+"Index/Detection.html",
		   success: function(msg){
			   if(msg.status==1){
				   $('#sp-1').hide();
				   $('#sp-3').show();
			   }else{
				   $('#sp-1').hide();
				   $('#sp-2').show();
				   $('#sp-2 .proajax').html(msg.info);
			   }
		   },
		   error: function() {
 				alert('环境不支持，请配置子域名或将项目放到根目录');
   			},
		});
	})
	
	$('#sp-2 .btn').click(function(){
		$.ajax({
		   type: "POST",
		   url: APP+"Index/Detection.html",
		   success: function(msg){
			   if(msg.status==1){
				   $('#sp-2').hide();
				   $('#sp-3').show();
			   }else{
				   $('#sp-2 .proajax').html(msg.info);
			   }
		   }
		});
	})
	
	//数据库信息
	$('#sp-3 input').blur(function(){
		var data=$('#sp-3 form').serialize();
		$.ajax({
		   type: "POST",
		   url: APP+"Index/Dbmysql.html",
		   data:data,
		   success: function(msg){
			    $('#sp-3 .dbprompt').html(msg.info);
		   }
		});
	})
	
	//数据库安装
	$('#sp-3 .btn').click(function(){
		var data=$('#sp-3 form').serialize();
		$('#sp-3').hide();
		$('#sp-4').show();
		$.ajax({
		   type: "POST",
		   url: APP+"Index/Dbinstallation.html",
		   data:data,
		   success: function(msg){
			   if(msg.status==1){
					$('#sp-4').hide();
					$('#sp-5').show();
			   }else{
				   $('#sp-4 .describe').html(msg.info);
				   $('#sp-4 .btncenter').html('<button type="submit" class="btn btn-inverse btn-large" onclick="lastStep()">上一步</button>');
			   }
		   }
		});
	})
	
})
//返回上一步
function lastStep(){
	$('#sp-3').show();
	$('#sp-4').hide();
	$('#sp-4 .describe').html('安装中，请勿关闭窗口');
}