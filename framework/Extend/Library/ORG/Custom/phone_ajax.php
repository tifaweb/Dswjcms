<?php
error_reporting(0);
session_start();
include ("sms.class.php");
$mobile="";
//add by w 2013.04.11 增加修改银行账号获取验证码的流程
 if(!isset($_POST['nub'])){
     if(isset($_SESSION['yzphone'])&&!empty($_SESSION['yzphone'])){
         $mobile =$_SESSION['yzphone'];
     }else{
         $status = 0;
     }
 }else{
     $mobile = $_POST['nub']; 
 }
 $msg = rand(10000,99999);
 $smsg="你的手机验证码为：".$msg." [信诚通贷]";
$sms=new SMSxc();
$ret = $sms->send($mobile,$smsg);
 if($ret){
	 $status = 1;
	 session_register("phone_valicode");
	 $_SESSION["phone_valicode"] = $msg;
 }else{
	$status = 0;
 }
 
 $arr = array('mobile'=>$mobile,'status'=>$status);
// file_put_contents('c:/m.txt', $msg);//测试用
 echo json_encode($arr);
?>