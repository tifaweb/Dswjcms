$(document).ready(function(){
	docReady();
});
		
		
function docReady(){
	//prevent # links from moving to top
	
	
	//KindEditor
	var editor;
	KindEditor.ready(function(K) {
			editor = K.create('.editor');
	});
	
	//notifications
	$('.noty').click(function(e){
		e.preventDefault();
		var options = $.parseJSON($(this).attr('data-noty-options'));
		noty(options);
	});
	//失去焦点时
	$('.notys').blur(function(e){
		e.preventDefault();
		var options = $.parseJSON($(this).attr('data-noty-options'));
		noty(options);
	});

	//uniform - styler for checkbox, radio and file input
	$("input:checkbox, input:radio, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();

	//chosen - improves select
	$('[data-rel="chosen"],[rel="chosen"]').chosen();

	//tabs
	$('#myTab a:first').tab('show');
	$('#myTab a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	});

	//makes elements soratble, elements that sort need to have id attribute to save the result
	$('.sortable').sortable({
		revert:true,
		cancel:'.btn,.box-content,.nav-header',
		update:function(event,ui){
			//line below gives the ids of elements, you can make ajax call here to save it to the database
			//console.log($(this).sortable('toArray'));
		}
	});

	//slider
	$('.slider').slider({range:true,values:[10,65]});

	//tooltip
	$('[rel="tooltip"],[data-rel="tooltip"]').tooltip({"placement":"bottom",delay: { show: 400, hide: 200 }});

	//auto grow textarea
	$('textarea.autogrow').autogrow();

	//popover
	$('[rel="popover"],[data-rel="popover"]').popover();



	//uploadify - multiple uploads
	//$('#file_upload').uploadify({
//		'swf'      : '/Public/misc/uploadify.swf',
//		'uploader' : '/Public/misc/uploadify.php'
//		// Put your options here
//	});
	//单图
	var img_id_upload=new Array();//初始化数组，存储已经上传的图片名
	var img_var="";
	var i=$(".folder_id").val()?$(".folder_id").val():0;//初始化数组下标
	var fr=$(".folder").val()?$(".folder").val():'undefined';
	var fl=$(".file_url").val();
	$('#file_up').uploadify({
		'formData'     : {
			'folder' : fr,
		},
    	'auto'     : true,//关闭自动上传
    	'removeTimeout' : 1,//文件队列上传完成1秒后删除
        'swf'      : '/Public/uploadify/uploadify.swf',
        'uploader' : '/Public/uploadify/uploadify.php',
        'method'   : 'post',//方法，服务端可以用$_POST数组获取数据
		'buttonText' : '选择图片',//设置按钮文本
        'multi'    : false,//允许同时上传多张图片
        'uploadLimit' : 100,//一次最多只允许上传100张图片
        'fileTypeDesc' : 'Image Files',//只允许上传图像
        'fileTypeExts' : '*.gif; *.jpg;*.png;',//限制允许上传的图片后缀
        'fileSizeLimit' : '800KB',//限制上传的图片不得超过500KB
        'onUploadSuccess' : function(file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
               img_id_upload[i]=data;
               i++;
				$("#file_up_content").append("<li id='i"+i+"'><input name='idcard_img[]' type='hidden'  value='"+data+"'/><a id='feila"+i+"' ><img id='feil"+i+"' src='"+fl+data+"' style='width:150px;height:150px;'></li>");
        },
        'onQueueComplete' : function(queueData) {//上传队列全部完成后执行的回调函数
            //if(img_id_upload.length>0)
            //alert('成功上传的文件有：'+encodeURIComponent(img_id_upload));
        }
        // Put your options here
    });
	//单图
	$('#file_up2').uploadify({
		'formData'     : {
			'folder' : fr,
		},
    	'auto'     : true,//关闭自动上传
    	'removeTimeout' : 1,//文件队列上传完成1秒后删除
        'swf'      : '/Public/uploadify/uploadify.swf',
        'uploader' : '/Public/uploadify/uploadify.php',
        'method'   : 'post',//方法，服务端可以用$_POST数组获取数据
		'buttonText' : '选择图片',//设置按钮文本
        'multi'    : false,//允许同时上传多张图片
        'uploadLimit' : 100,//一次最多只允许上传100张图片
        'fileTypeDesc' : 'Image Files',//只允许上传图像
        'fileTypeExts' : '*.gif; *.jpg;*.png;',//限制允许上传的图片后缀
        'fileSizeLimit' : '800KB',//限制上传的图片不得超过500KB
        'onUploadSuccess' : function(file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
               img_id_upload[i]=data;
               i++;
				$("#file2_up_content").append("<li id='i"+i+"'><input name='idcard_img[]' type='hidden'  value='"+data+"'/><a id='feila"+i+"' ><img id='feil"+i+"' src='"+fl+data+"' style='width:150px;height:150px;'></li>");
        },
        'onQueueComplete' : function(queueData) {//上传队列全部完成后执行的回调函数
            //if(img_id_upload.length>0)
            //alert('成功上传的文件有：'+encodeURIComponent(img_id_upload));
        }
        // Put your options here
    });


	var img_id_upload=new Array();//初始化数组，存储已经上传的图片名
	var img_var="";
	var i=$("#folder_id").val()?$("#folder_id").val():0;//初始化数组下标
	var folder=$("#folder").val();
	var file_url=$("#file_url").val();
	var file_delete=$("#file_delete").val();	//如果存在图片就删除
	$('#file_upload').uploadify({
		'formData'     : {
			'folder' : folder,
		},
    	'auto'     : true,//关闭自动上传
    	'removeTimeout' : 1,//文件队列上传完成1秒后删除
        'swf'      : '/Public/uploadify/uploadify.swf',
        'uploader' : '/Public/uploadify/uploadify.php',
        'method'   : 'post',//方法，服务端可以用$_POST数组获取数据
		'buttonText' : '选择图片',//设置按钮文本
        'multi'    : false,//允许同时上传多张图片
        'uploadLimit' : 100,//一次最多只允许上传100张图片
        'fileTypeDesc' : 'Image Files',//只允许上传图像
        'fileTypeExts' : '*.gif; *.jpg;*.png;',//限制允许上传的图片后缀
        'fileSizeLimit' : '800KB',//限制上传的图片不得超过500KB
        'onUploadSuccess' : function(file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
               img_id_upload[i]=data;
               i++;
				$("#feil").attr({ src: "/Public/uploadify/uploads/"+folder+'/'+data});
				$("#feila").attr({ href: "/Public/uploadify/uploads/"+folder+'/'+data});
				$("#file_delete").attr({ value: "/uploads/"+folder+'/'+data});
				$("#feila").addClass("cboxElement");
				$('#file_upload').hide();
				$("#img").attr({ value: data});
        },
        'onQueueComplete' : function(queueData) {//上传队列全部完成后执行的回调函数
            //if(img_id_upload.length>0)
            //alert('成功上传的文件有：'+encodeURIComponent(img_id_upload));
        }
        // Put your options here
    });
	
	//uploadify - 多图
	$('#file_uploads').uploadify({
		'formData'     : {
			'folder' : folder,
		},
    	'auto'     : true,//关闭自动上传
    	'removeTimeout' : 1,//文件队列上传完成1秒后删除
        'swf'      : '/Public/uploadify/uploadify.swf',
        'uploader' : '/Public/uploadify/uploadify.php',
        'method'   : 'post',//方法，服务端可以用$_POST数组获取数据
		'buttonText' : '选择图片',//设置按钮文本
        'multi'    : true,//允许同时上传多张图片
        'uploadLimit' : 100,//一次最多只允许上传100张图片
        'fileTypeDesc' : 'Image Files',//只允许上传图像
        'fileTypeExts' : '*.gif; *.jpg;*.png;',//限制允许上传的图片后缀
        'fileSizeLimit' : '800KB',//限制上传的图片不得超过500KB
        'onUploadSuccess' : function(file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
               img_id_upload[i]=data;
               i++;
			   img_var=img_var+","+data;
				$("#img").attr({ value: img_var});
        },
        'onQueueComplete' : function(queueData) {//上传队列全部完成后执行的回调函数
            //if(img_id_upload.length>0)
            //alert('成功上传的文件有：'+encodeURIComponent(img_id_upload));
        }
        // Put your options here
    });
	
	//uploadify - 多图+显示
	$('#file_goods').uploadify({
		'formData'     : {
			'folder' : folder,
		},
    	'auto'     : true,//关闭自动上传
    	'removeTimeout' : 1,//文件队列上传完成1秒后删除
        'swf'      : '/Public/uploadify/uploadify.swf',
        'uploader' : '/Public/uploadify/uploadify.php',
        'method'   : 'post',//方法，服务端可以用$_POST数组获取数据
		'buttonText' : '选择图片',//设置按钮文本
        'multi'    : true,//允许同时上传多张图片
        'uploadLimit' : 10,//一次最多只允许上传100张图片
        'fileTypeDesc' : 'Image Files',//只允许上传图像
        'fileTypeExts' : '*.gif; *.jpg;*.png;',//限制允许上传的图片后缀
        'fileSizeLimit' : '800KB',//限制上传的图片不得超过500KB
        'onUploadSuccess' : function(file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
               img_id_upload[i]=data;
               i++;
				$("#file_content").append("<li id='i"+i+"'><input name='img[]' type='hidden'  value='"+data+"'/><div class='top'><a class='icon icon-color icon-close' onclick='intExit("+i+",\""+data+"\")'></a></div><a id='feila"+i+"' ><img id='feil"+i+"' src='"+file_url+data+"' style='width:150px;height:150px;'></li>");
        },
        'onQueueComplete' : function(queueData) {//上传队列全部完成后执行的回调函数
            //if(img_id_upload.length>0)
            //alert('成功上传的文件有：'+encodeURIComponent(img_id_upload));
        }
        // Put your options here
    });
	
	//uploadify - 多图+显示
	var i=$("#folder_ids").val()?$("#folder_ids").val():0;//初始化数组下标
	var file_urls=$("#file_urls").val();
	$('#file_good').uploadify({
		'formData'     : {
			'folder' : folder,
		},
    	'auto'     : true,//关闭自动上传
    	'removeTimeout' : 1,//文件队列上传完成1秒后删除
        'swf'      : '/Public/uploadify/uploadify.swf',
        'uploader' : '/Public/uploadify/uploadify.php',
        'method'   : 'post',//方法，服务端可以用$_POST数组获取数据
		'buttonText' : '选择图片',//设置按钮文本
        'multi'    : true,//允许同时上传多张图片
        'uploadLimit' : 10,//一次最多只允许上传100张图片
        'fileTypeDesc' : 'Image Files',//只允许上传图像
        'fileTypeExts' : '*.gif; *.jpg;*.png;',//限制允许上传的图片后缀
        'fileSizeLimit' : '800KB',//限制上传的图片不得超过500KB
        'onUploadSuccess' : function(file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
               img_id_upload[i]=data;
               i++;
				$("#file_contents").append("<li id='i"+i+"'><input name='agr[]' type='hidden'  value='"+data+"'/><div class='top'><a class='icon icon-color icon-close' onclick='intExit("+i+",\""+data+"\")'></a></div><a id='feilas"+i+"' ><img id='feils"+i+"' src='"+file_urls+data+"' style='width:150px;height:150px;'></li>");
        },
        'onQueueComplete' : function(queueData) {//上传队列全部完成后执行的回调函数
            //if(img_id_upload.length>0)
            //alert('成功上传的文件有：'+encodeURIComponent(img_id_upload));
        }
        // Put your options here
    });
	
	//gallery controlls container animation
	$('ul.gallery li').hover(function(){
		$('img',this).fadeToggle(1000);
		$(this).find('.gallery-controls').remove();
		$(this).append('<div class="well gallery-controls">'+
							'<p><a href="#" class="gallery-edit btn"><i class="icon-edit"></i></a> <a href="#" class="gallery-delete btn"><i class="icon-remove"></i></a></p>'+
						'</div>');
		$(this).find('.gallery-controls').stop().animate({'margin-top':'-1'},400,'easeInQuint');
	},function(){
		$('img',this).fadeToggle(1000);
		$(this).find('.gallery-controls').stop().animate({'margin-top':'-30'},200,'easeInQuint',function(){
				$(this).remove();
		});
	});


	

	//tour
	if($('.tour').length && typeof(tour)=='undefined')
	{
		var tour = new Tour();
		tour.addStep({
			element: ".span10:first", /* html element next to which the step popover should be shown */
			placement: "top",
			title: "Custom Tour", /* title of the popover */
			content: "You can create tour like this. Click Next." /* content of the popover */
		});
		tour.addStep({
			element: ".theme-container",
			placement: "left",
			title: "Themes",
			content: "You change your theme from here."
		});
		tour.addStep({
			element: "ul.main-menu a:first",
			title: "Dashboard",
			content: "This is your dashboard from here you will find highlights."
		});
		tour.addStep({
			element: "#for-is-ajax",
			title: "Ajax",
			content: "You can change if pages load with Ajax or not."
		});
		tour.addStep({
			element: ".top-nav a:first",
			placement: "bottom",
			title: "Visit Site",
			content: "Visit your front end from here."
		});
		
		tour.restart();
	}

	//datatable
	var datasort=$('#datasort').val()?$('#datasort').val():0;//排序根据哪列
	var dataasc=$('#dataasc').val()?"asc":"desc";//排序条件
	$('.datatable').dataTable({
			"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
			"sPaginationType": "bootstrap",
			"aaSorting": [[ datasort, dataasc ]],
			"oLanguage": {
			"sLengthMenu": "_MENU_ 每页显示数"
			}
		} );
	$('.btn-close').click(function(e){
		e.preventDefault();
		$(this).parent().parent().parent().fadeOut();
	});
	$('.btn-minimize').click(function(e){
		e.preventDefault();
		var $target = $(this).parent().parent().next('.box-content');
		if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
		else 					   $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
		$target.slideToggle();
	});
	$('.btn-setting').click(function(e){
		e.preventDefault();
		$('#myModal').modal('show');
	});



		
}


//additional functions for data table
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
	return {
		"iStart":         oSettings._iDisplayStart,
		"iEnd":           oSettings.fnDisplayEnd(),
		"iLength":        oSettings._iDisplayLength,
		"iTotal":         oSettings.fnRecordsTotal(),
		"iFilteredTotal": oSettings.fnRecordsDisplay(),
		"iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		"iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
	};
}
$.extend( $.fn.dataTableExt.oPagination, {
	"bootstrap": {
		"fnInit": function( oSettings, nPaging, fnDraw ) {
			var oLang = oSettings.oLanguage.oPaginate;
			var fnClickHandler = function ( e ) {
				e.preventDefault();
				if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
					fnDraw( oSettings );
				}
			};

			$(nPaging).addClass('pagination').append(
				'<ul>'+
					'<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
					'<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
				'</ul>'
			);
			var els = $('a', nPaging);
			$(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
			$(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
		},

		"fnUpdate": function ( oSettings, fnDraw ) {
			var iListLength = 5;
			var oPaging = oSettings.oInstance.fnPagingInfo();
			var an = oSettings.aanFeatures.p;
			var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

			if ( oPaging.iTotalPages < iListLength) {
				iStart = 1;
				iEnd = oPaging.iTotalPages;
			}
			else if ( oPaging.iPage <= iHalf ) {
				iStart = 1;
				iEnd = iListLength;
			} else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
				iStart = oPaging.iTotalPages - iListLength + 1;
				iEnd = oPaging.iTotalPages;
			} else {
				iStart = oPaging.iPage - iHalf + 1;
				iEnd = iStart + iListLength - 1;
			}

			for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
				// remove the middle elements
				$('li:gt(0)', an[i]).filter(':not(:last)').remove();

				// add the new list items and their event handlers
				for ( j=iStart ; j<=iEnd ; j++ ) {
					sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
					$('<li '+sClass+'><a href="#">'+j+'</a></li>')
						.insertBefore( $('li:last', an[i])[0] )
						.bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
							fnDraw( oSettings );
						} );
				}

				// add / remove disabled classes from the static elements
				if ( oPaging.iPage === 0 ) {
					$('li:first', an[i]).addClass('disabled');
				} else {
					$('li:first', an[i]).removeClass('disabled');
				}

				if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
					$('li:last', an[i]).addClass('disabled');
				} else {
					$('li:last', an[i]).removeClass('disabled');
				}
			}
		}
	}
});
