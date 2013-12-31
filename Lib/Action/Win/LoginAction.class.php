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
class LoginAction extends WinAction {
	/**
	*
	* @登陆接口接收说明
	* @作者		pang
	* @版权		http://www.dswjcms.com
	* @传输数据
	* 	user		用户名
	* 	pass		密码
	* @接收用户资金和用户名
	*/
	//登陆
    public function index(){
		//跳转地址
		$this->assign('jump_url',2);
		$this->assign('title','登陆');
		$username=$this->_post('username');
		$password=$this->_post('password');
	    if(isset($username)&& isset($password)){
            if($this->_session('verify') != md5($this->_post('proving'))) {
               $this->error('验证码错误！');
            }
			$user = D("User");
			$condition['username'] = $username;
			$condition['password'] = $user->userMd5($password);
			$list = $user->where($condition)->select();
		   if($list){
				session('user_name',$username);  //设置session
				session('user_uid',$list[0]['id']);
				session('verify',null); //删除验证码
				//session(null); //清空
				$this->success("用户登录成功", '__ROOT__/Win/Loan/account');
				exit;
			}else{
				 $this->error('用户名或密码错误');
			exit;
			}
	   }
       $this->display();
    }
	
	//退出
    public function out(){
		//跳转地址
		$this->assign('jump_url',2);
		$this->assign('title','退出');
		session(null);
		$this->success('用户退出成功','__ROOT__/Win/Login');	
	}
}
?>