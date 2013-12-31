// 基于JQ的地区三级联动
//region.js为城市数据文件，调用时需要给cache赋值,如$("#area").area({cache:region});	 
// 创建一个闭包     
(function($) {     
  //插件主要内容     
  $.fn.area = function(options) {     
    	// 处理默认参数   
   		var opts = $.extend({}, $.fn.area.defaults, options);     
    	// 保存JQ的连贯操作    
    	return this.each(function()
		{ 
			getProvinces();
			$(opts.provinceId).change(function(){
				getCities();
			});
			$(opts.cityId).change(function(){
				getCounties();
			});
    	}); 
		
		//ajax获取地区数据
		function getData()
		{
			//可以通过AJAX把地区数据读取到opts.cache中,数据格式为					        /*{"provinces2":{"region_id":"2","region_name":"\u5317\u4eac","city":{"cities52":{"region_id":"52","region_name":"\u5317\u4eac","county":{"counties500":{"region_id":"500","region_name":"\u4e1c\u57ce\u533a"}}}  是一个三维数组的json数据,provinces2为省的id加provinces前缀作为key值;每个省数据中有city城市数据数组,每个城市数据中有county县数据数组*/
		}
	
		//获取省数据
　　		function getProvinces(){
　　　　　　var pro = "";
		  $.each(opts.cache, function(i,item){
			 pro += "<option value="+item.region_id+">" + item.region_name + "</option>";						  	
  		  });
		  $(opts.provinceId).empty().append(pro);　　　　　　
　　　　　　getCities();
　　		}
		
		//获取城市数据
		function getCities()
		{
			var proIndex = $(opts.provinceId).val();
　　　　　　  showCities(proIndex);
　　　　　　  getCounties();
		}
		
		//显示城市数据
　　　　function showCities(proIndex){
　　　　　　var cit = "";
　　　　　　if(opts.cache["provinces"+proIndex].city == null){
　　　　　　　　$(opts.cityId).empty();
　　　　　　　　return;
　　　　　　}
			
		  $.each(opts.cache["provinces"+proIndex].city, function(i,item){
			 cit += "<option value="+item.region_id+">" + item.region_name+ "</option>";
  		  });
　　　　　　$(opts.cityId).empty().append(cit);
　　　　}
	
	   //获取县数据
　	   function getCounties(){
　　　　　　var proIndex = $(opts.provinceId).val();
　　　　　　var citIndex = $(opts.cityId).val();
　　　　　　showCounties(proIndex,citIndex);
　　　　}
		
	   //显示县数据	
　　　　function showCounties(proIndex,citIndex){
　　　　　　var cou = "";
　　　　　　if(opts.cache["provinces"+proIndex].city["cities"+citIndex].county == null){
　　　　　　　　$(opts.countyId).empty();
　　　　　　　　return;
　　　　　　}
		  $.each(opts.cache["provinces"+proIndex].city["cities"+citIndex].county,function(i,item){
			 cou += "<option value="+item.region_id+">" + item.region_name+ "</option>";
  		  });
　　　　　　$(opts.countyId).empty().append(cou);
　　　　}
  };
	 //插件主要内容结束
    
  // 插件的defaults     
  $.fn.area.defaults = {     
        url:'',
		cache:'',//地区数据
		provinceId:'#province',
		cityId:'#city',
		countyId:'#county'
  };     
// 闭包结束     
})(jQuery); 