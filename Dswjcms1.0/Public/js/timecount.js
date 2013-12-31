function timeCount(remain_id){
	 function _fresh(){ 
	 var nowDate = new Date();                             //当前时间
	 endDate = new Date($('#'+remain_id).attr('endtime')),    //截止时间
	 totalS  = parseInt((endDate.getTime() - nowDate.getTime()) / 1000);     //总秒数   
	 _day    = parseInt(totalS / 3600 / 24);      
	 _hour   = parseInt((totalS / 3600) % 24);   
	 _minute = parseInt((totalS / 60) % 60);   
	 _second = parseInt(totalS % 60);  
	 var _day = _day;
	 var _hour = _hour;
	 var _minute = _minute;
	 var _second = _second; 
	 d=_day.toString().length;
	 h=_hour.toString().length;
	 m=_minute.toString().length;
	 s=_second.toString().length;
	 if(d == 1) {
			_day="0"+_day;
				}  
	 if(h == 1) {
			_hour="0"+_hour;
				} 
	 if(m == 1) {
			_minute="0"+_minute;
				} 
	 if(s == 1) {
			_second="0"+_second;
				}  
	 $('#'+remain_id).html(_day +'天' + _hour + '时' + _minute + '分' + _second + '秒');        
	 if( totalS <= 0){       
	 $('#'+remain_id).html('<span class="red">已结束</span>');    
	 clearInterval(sh);   
			 }     
	 }    
	 _fresh(); 
	 var sh = setInterval(_fresh,1000);  
}