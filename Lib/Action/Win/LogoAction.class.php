<?php
// +----------------------------------------------------------------------
// | dswjcms
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.tifaweb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
// +----------------------------------------------------------------------
// | Author: 宁波市鄞州区天发网络科技有限公司 <dianshiweijin@126.com>
// +----------------------------------------------------------------------
// | Released under the GNU General Public License
// +----------------------------------------------------------------------
defined('THINK_PATH') or exit();
class LogoAction extends WinAction {
	
	//登陆
	public function sublogo(){
		 //微信登陆
		//$this->wxoauth();
		$user = D("User");
		$condition['username'] = $this->_post('username');
		$condition['password'] = $user->userMd5($this->_post('password'));
		$list = $user->where($condition)->find();
		if(!$list){	//手机号
			$uid=M('userinfo')->where(array('cellphone'=>$this->_post('username')))->getField('uid');
			
			if($uid>0){
				unset($condition);
				$condition['id'] =$uid;
				$condition['password'] = $user->userMd5($this->_post('password'));
				$list = $user->where($condition)->find();
			}else{
				$this->ajaxReturn(0,"用户名或密码错误",0);
			}
		}
	   if($list){
		   
			session('user_name',$list['username']);  //设置session
			session('user_uid',$list['id']);
			session('user_verify',MD5($list['username'].DS_ENTERPRISE.$list['password'].DS_EN_ENTERPRISE));
			cookie('user_uid',$this->_session('user_uid'),604800);
			cookie('user_name',$this->_session('user_name'),604800);
			cookie('user_verify',$this->_session('user_verify'),604800);
			$this->ajaxReturn(1,"用户登录成功",1);
		}else{
			 $this->ajaxReturn(0,"用户名或密码错误",0);
		}
	}
	
	//注册页
	public function register(){
		$this->display();  
    }
	
	//注册
	public function addreg(){
		if(!preg_match("/^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|17[0-9]{9}$|18[0-9]{9}$/",$this->_post('username'))){
			$this->ajaxReturn(0,"手机号有误",0);
		}
		$systems=$this->systems();
		$model=D('User');
		$money=M('money');
		$userinfo=M('userinfo');
		$models = new Model();
		if($create=$model->create()){
			 $create['time']=time();
			 $create['pay_password']=$model->userPayMd5($this->_post('password'));
		     $result = $model->add($create);
			if($result){
				//记录添加点
				$arr[0]=1;
				$arr[1]=$inf['mem_register'][1];
				$arr[2]=$inf['mem_register'][0];
				$arr[3]='平台';
				$arr[4]=1;
				$arr[5]=1;
				$arr[7]=$result;
				$this->moneyLog($arr);
				$money->add(array('uid'=>$result));	//资金表
				$userinfo->add(array('uid'=>$result,'cellphone'=>$this->_post('username')));	//用户资料表
				$this->userLog('会员注册成功',$result);	//会员记录
				$this->silSingle(array('title'=>'会员注册成功','sid'=>$result,'msg'=>$this->_post('username').'您的账号已注册成功！'));//站内信
				$user=$model->where('`id`='.$result)->find();
				session('user_name',$user['username']);  //设置session
				session('user_uid',$user['id']);
				session('user_verify',MD5($user['username'].DS_ENTERPRISE.$user['password'].DS_EN_ENTERPRISE));
				session('cellpcode',null); //删除验证码
				unset($user);
				$this->ajaxReturn(1,"注册成功",1);
			}else{
				 $this->ajaxReturn(0,"注册失败",0);
			}	
		}else{
			$this->ajaxReturn(0,$model->getError(),0);
			
		} 
    }
}