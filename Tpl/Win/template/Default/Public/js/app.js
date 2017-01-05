var APPURL="/Win/";
//表单提交
function submission(name,url,urls){
	var data=$('#'+name+'').serialize();
	url=APPURL+url+'.html';
	if(urls){
	urls=APPURL+urls;
	}else{
		
	}
	$.ajax({
	   type: "POST",
	   url: url,
	   data: data,
	   success: function(msg){
		   if(msg.status==1){	//成功
			  $('#'+name+'_prompt').html('');
			  
			  if(urls){
				window.location.href=urls;	 
			  }else{
				 history.go(-1);
			  }
		   }else{
			   $('#'+name+'_prompt').html('<div class="redPrompt">'+msg.info+'</div>');
			   setTimeout(function () {
					$('#'+name+'_prompt').html('');
				}, 3000);
		   }
	   }
	});
}
