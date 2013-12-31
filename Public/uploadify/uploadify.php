<?php
/*
uploadify 后台处理
*/

//设置上传目录
error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED );
$path = "uploads/".$_GET['folder']."/";	
if($_GET['file_delete']){	//如果原先有图片就删除	
	if(file_exists('.'.$_GET['file_delete'])){	//存在图片
		unlink('.'.$_GET['file_delete']);	//删除它
	}
}
if (!empty($_FILES)) {
	
	//得到上传的临时文件流
	$tempFile = $_FILES['Filedata']['tmp_name'];
	
	//允许的文件后缀
	$fileTypes = array('jpg','jpeg','gif','png'); 
	
	//得到文件原名
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	$ftype=$fileParts['extension'];
	$fileName=microtime(true).".".$fileParts['extension'];	//以微秒时间命名
	
	//最后保存服务器地址
	if(!is_dir($path))
	   mkdir($path);
	if (move_uploaded_file($tempFile, $path.$fileName)){
		echo $fileName;
	}else{
		echo $fileName."上传失败！";
	}
}
?>