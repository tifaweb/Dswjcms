<?php
// +----------------------------------------------------------------------
// | dswjcms
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.tifaweb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
// +----------------------------------------------------------------------
// | Author: tfweb <dianshiweijin@126.com>
// +----------------------------------------------------------------------
class UserModel extends RelationModel{
	protected $_validate = array(
		array('proving','require','验证码必须！'), 
		array('proving','checkCode','验证码错误!',0,'callback',1),
		array('email','email','email有误！'),
		array('email','','邮箱已被注册！',0,'unique',1),
		array('username','require','用户必须填写！'),
		array('username','','用户已经存在！',0,'unique',1),
		array('username','/^\w{2,}$/','用户名必须2个字母以上！',0,'regex',1),
		array('passwd','require','原始密码必须！'), 
		array('passw','require','密码必须！'), 
		array('password','require','重复密码必须！'), 
		array('passw','password','确认密码不正确！',0,'confirm'), 
		array('pay_pasd','require','原始交易密码必须！'),
		array('npay_password','require','交易密码必须！'), 
		array('npay_password','pay_password','重复交易密码必须！',0,'confirm'), 
	);
	
	protected function checkCode($code){
		if(md5($code)!=session('verify')){
			return false;
		}else{
			return true;
		}
	}
		
	public function userMd5($password){
		return MD5(substr(MD5(substr(MD5($password),2,30).DS_ENTERPRISE),10,20).DS_EN_ENTERPRISE);
	}
}
?>