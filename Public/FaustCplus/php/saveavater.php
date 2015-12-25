<?php
/*
*头像FLASH处理
*
*/
if($_GET['action']){
	$action=explode('user_',$_GET['action']);
	if( count($action) !=2 || !is_numeric($action[1])){
		echo 'Illegal operation!';
		exit;
	}
	$input = file_get_contents('php://input');
	$data = explode('--------------------', $input);

	@file_put_contents('./img/big_'.$_GET['action'].'.jpg', $data[0]);
	@file_put_contents('./img/mid_'.$_GET['action'].'.jpg', $data[1]);
	@file_put_contents('./img/small_'.$_GET['action'].'.jpg', $data[2]);
	@file_put_contents('./img/raw_'.$_GET['action'].'.jpg', $data[3]);
}

?>

