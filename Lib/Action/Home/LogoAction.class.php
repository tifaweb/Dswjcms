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
class LogoAction extends HomeAction {
//----------登陆页------------
     public function login(){
		 //标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		 $this->display();
    }
	
	//登陆
	public function loging(){
		if($this->_session('verify') != md5($this->_post('proving'))) {
		   $this->error('验证码错误！');
			exit;
		}
		$user = D("User");
		$condition['username'] = $_POST[username];
		$condition['password'] = $user->userMd5($_POST[password]);
		$list = $user->where($condition)->select();
	   if($list){
			session('user_name',$_POST[username]);  //设置session
			session('user_uid',$list[0][id]);
			session('verify',null); //删除验证码
			//session(null); //清空
			$this->userLog('会员登陆',$this->_session('user_uid'));	//会员记录
			$this->success("用户登录成功", '__ROOT__/Center.html');
			exit;
		}else{
			 $this->error('用户名或密码错误');
		exit;
		}
	}
	
	//直接登陆
	public function logings(){
		$user = D("User");
		$condition['username'] = $_POST[username];
		$condition['password'] = $user->userMd5($_POST[password]);
		$list = $user->where($condition)->select();
	   if($list){
			session('user_name',$_POST[username]);  //设置session
			session('user_uid',$list[0][id]);
			session('verify',null); //删除验证码
			//session(null); //清空
			$this->userLog('会员登陆',$this->_session('user_uid'));	//会员记录
			$this->success("用户登录成功", '__ROOT__/Center.html');
			exit;
		}else{
			 $this->error('用户名或密码错误');
		exit;
		}
	}
	
//----------注册页------------
	public function register($uid = 0,$gid = 0){
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$head='<link  href="__PUBLIC__/css/style.css" rel="stylesheet">';
		$this->assign('head',$head);
		if($uid){
			$where= "group_id =".$id;
			$list = D("User")->where("id=".$uid)->find();
			$this->assign('list',$list);
		}
		if($gid){

			$this->assign('gid',intval($gid));
		}		
		//上线
		$QUERY_STRING=$_SERVER['QUERY_STRING'];
		if($QUERY_STRING){
			$lsuid=base64_decode($QUERY_STRING);
			$lsuid=explode('=',$lsuid);
			$lsuid=$lsuid[1];
			$this->assign('lsuid',$lsuid);
		}
		$this->display();  
    }
	
//----------找回密码------------
     public function forgotpass(){
		 //标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		 $head='<link  href="__PUBLIC__/css/style.css" rel="stylesheet">';
		 $this->assign('head',$head);
		 $this->display();  
	 }
	 
	 //找回密码修改页
	 public function rppage(){
		$cache = cache(array('expire'=>50));
		$value = $cache->get('rpawss'.$this->_get('uid'));  // 获取缓存
		$user=D('User');
		$users=$user->where('id="'.$this->_get('uid').'"')->find();
		if(!md5($users['email'])==$value){	//判断链接是否过期
			$this->error("链接已过期！","__ROOT__/Logo/login.html");
		}
		 $this->display();  
	 }

	//注册
	public function addreg(){
		if($this->_session('verify') != md5($this->_post('proving'))) {
		   $this->error('验证码错误！');
			exit;
		}
		$msgTools = A('msg','Event');
		$model=D('User');
		$ufees=M('ufees');
		$money=M('money');
		$vip_points=M('vip_points');
		$promote_integral=M('promote_integral');
		$userinfo=M('userinfo');
		$inf=$this->integralConf();
		if($model->create()){
		     $result = $model->add();
			if($result){
				//记录添加点
				$ufees->add(array('uid'=>$result,'total'=>$inf['mem_register'][0],'available'=>$inf['mem_register'][0]));	//会员积分
				$money->add(array('uid'=>$result));	//资金表
				$vip_points->add(array('uid'=>$result));	//VIP积分
				$promote_integral->add(array('uid'=>$result));	//推广积分
				$userinfo->add(array('uid'=>$result));	//用户资料表
				$this->userLog('会员注册成功',$result);	//会员记录
				$msgTools->sendMsg(3,'会员注册成功',$this->_post('username').'您的账号已注册成功！','admin',$this->_post('username'));//站内信
				if($this->_post('uid')){	//是否是推荐过来的
					$arr['promote']=array('uid'=>$this->_post('uid'),'name'=>'pro_registered');
					$this->integralAdd($arr);	//添加推广积分
					$msgTools->sendMsg(3,'下线注册返利通知','你推荐的下线'.$this->_post('username').'已成功通过注册，你获得推荐奖励，积分+'.$inf['mem_register'][0],'admin',$this->_post('uid'));//站内信
				}
				$this->success("注册成功",'__ROOT__/Logo/login.html');			
			}else{
				 $this->error("注册失败");
			}	
		}else{
			$this->error($model->getError());
			
		} 
    }
	
	//邮箱找回密码
	public function rPassword(){
		if($this->_session('verify') != md5($this->_post('proving'))) {
		   $this->error('验证码错误！');
			exit;
		}
		$userinfo=D('Userinfo');
		$user=D('User');
		$users=$user->where('username="'.$this->_post('user').'"')->find();
		if($users){
			$email=$users['email'];
		}else{
			$this->error("账号不存在");
		}
		$smtp=M('smtp');
		$stmpArr=$smtp->find();
		$stmpArr['receipt_email']	=$email;
		$cache = cache(array('expire'=>3600));
		$cache->set('rpawss'.$users['id'],md5($email));	//设置缓存
		$stmpArr['title']			="找回密码";
		$stmpArr['content']			='<div>
											<p>您好，<b>'.$this->_post('user').'</b> ：</p>
										</div>
										<div style="margin: 6px 0 60px 0;">
											<p>请点击这里，修改您的密码</p>
											<p><a href="http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Logo/rppage?pass='.$cache->get('rpawss'.$users['id']).'&uid='.$users['id'].'">http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Logo/rppage?pass='.$cache->get('rpawss'.$users['id']).'&uid='.$users['id'].'</a></p>
											<p>如果您的邮箱不支持链接点击，请将以上链接地址拷贝到你的浏览器地址栏中认证。</p>
										</div>
										<div style="color: #999;">
											<p>发件时间：'.date('Y/m/d H:i:s').'</p>
											<p>此邮件为系统自动发出的，请勿直接回复。</p>
										</div>';
		$emailsend=R('Sharing/email_send',array($stmpArr));	
		if($emailsend){
			$this->success('邮件发送成功', '__ROOT__/Logo/forgotpass.html');
		}else{
			$this->error("找回密码失败，请联系管理员");
		}	
	}
	
	//邮箱找回密码提交
	public function rsPassword(){
		$user=D('User');
		$users=$user->where('id='.$this->_post('uid'))->find();
		if($user->create()){
			$result = $user->where(array('id'=>$this->_post('uid')))->save();
			if($result){
				$cache = cache(array('expire'=>50));
				$cache->rm('rpawss'.$this->_post('uid'));// 删除缓存
			 	$this->success("密码重置成功","__ROOT__/Logo.html");
			}else{
			$this->error("新密码不要和原始密码相同！");
			}		
		}else{
			$this->error($user->getError());
		}

	}
	
	//注册AJAX验证
	public function ajaxverify(){
		if($this->_post("name")=="username"){	//验证会员名
			$user=D('User');
			$row=$user->where('username="'.$this->_post('param').'"')->count();
			if($row){
				 echo '{
					"info":"会员名已存在！",
					"status":"n"
				 }';
				}else{
			echo '{
					"info":"可以注册！",
					"status":"y"
				 }';
			}
		}
		else if($this->_post("name")=="email"){	//验证会员邮箱
			$user=D('User');
			$row=$user->where('email="'.$this->_post('param').'"')->count();
			if($row){
				echo '{
					"info":"邮箱已存在！",
					"status":"n"
				 }';
			}else{
				echo '{
					"info":"可以注册！",
					"status":"y"
				 }';
			}
		}
		else if($this->_post("name")=="user"){	//验证会员名必须存在
			$user=D('User');
			$row=$user->where('username="'.$this->_post('param').'"')->count();
			if(!$row){
				echo '{
					"info":"没有此用户名！",
					"status":"n"
				 }';
			}else{
				echo '{
					"info":"存在用户名！",
					"status":"y"
				 }';
			}
		}
	}

}