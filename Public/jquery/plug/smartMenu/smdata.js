// 自定义右键菜单上下文数据
var bodyMenuData = [
[{ text: "页面空白处点击是否冲突测试" }],
[{
	text:'测试',
	func:function(){
		alert("fsafsd");
		}
	}]
];
var public_data = 
[
[{
	text:'删除',
	func:function(){
		 var answer = confirm("你确定要删除该模块?");
		if (answer){
		   $(this).remove();
		}
		
		}
	}],
[{
	text:'添加模块',
	func:function(){
		$("div").removeClass("Active");
		$(this).addClass("Active");
		$("#publicModule").dialog({
			height:450,
			width:520,
			zIndex:1111,
		    open:function(event, ui) { po=false; dpo=false; },
		    close: function(event, ui) { po=true;dpo=true;}
			
		});	
		
		}
	}],	
	
[{
	text:'删除内容',
	func:function(){
		 var answer = confirm("你确定要删除该模块下面所有内容？");
		if (answer){
		   $(this).empty();
		}
		
		
		}
	}]	,	
[{
	text:'设置属性',
	func:function(){
		$("div").removeClass("Active");
		$(this).addClass("Active");
		   var ActiveDom = $(this);
		   var domwidth = ActiveDom.width();
		   var domheight = ActiveDom.height();
		   var domfloat = ActiveDom.css("float");
		   var dombackground_color = ActiveDom.css("background-color");
		   var dombackground_image = ActiveDom.css("background-image");
		   $("#domwidthvalue").attr("value",domwidth);
		   $("#domheightvalue").attr("value",domheight);
		   $("#domfloatvalue").attr("value",domfloat);
		   $("#dombgvalue").attr("value",dombackground_color);
		   $("#dombgimgvalue").attr("value",dombackground_image);
		  
	    $("#publicPro").dialog({
			height:450,
			width:520,
			zIndex:1111,
		   open:function(event, ui) { po=false; dpo=false; },
		    close: function(event, ui) { po=true;dpo=true; }
			
		});			
		
		}
	}]	
];

//-------------------------
