<?php
defined('THINK_PATH') or exit();
class LogoAction extends CommAction {
     public function index(){
		
        if(isset($_POST['name'])){
            if($this->_session('verify') != md5($_POST['proving'])) {
               $this->error('验证码错误！');
	       exit;
            }
			
            $User = D("Admin"); // 实例化User对象
            $condition['name'] = $_POST['name'];
			$condition['password'] = $User->adminMd5($_POST['passw']);
            $list = $User->where($condition)->find();
           if($list){
                session('admin_name',$list['username']);  //设置session
				session('admin_uid',$list['id']);
                session('verify',null); //删除验证码
                //session(null); //清空
				$this->Record('管理员登陆成功');//后台操作
                $this->success('用户登录成功',U('Index/index'));
		exit;
            }else{
                 $this->error('用户名或密码错误');
		exit;
            }
			
        }
		 $this->display();
        
    }
	
	public 	function loginout(){
		if(isset($_SESSION['admin_name'])) {
			$this->assign("jumpUrl",U("Admin/Logo/index"));
			unset($_SESSION['admin_name']);
			unset($_SESSION);
			session_destroy();   
			$this->Record('管理员登出成功');//后台操作
            $this->success('登出成功！');
        }else {
            $this->error('已经登出！');
        }
	}
	
	 
}