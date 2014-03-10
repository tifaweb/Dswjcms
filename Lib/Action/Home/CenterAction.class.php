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
class CenterAction extends HomeAction {
//-------------个人中心--------------
//首页
	public function index(){
		$this->homeVerify();
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['center']='active';
		$this->assign('active',$active);
		$head=$this->headPortrait('./Public/FaustCplus/php/img/big_user_'.$this->_session('user_uid').'.jpg');
		$this->assign('heads',$head);
		$this->display();
    }

	
	//邮箱找回密码
	public function emailBack(){
		$this->homeVerify();
		$smtp=M('smtp');
		$stmpArr=$smtp->find();
		$email=$this->_post('email');
		$stmpArr['receipt_email']	=$email;
		$cache = cache(array('expire'=>3600));
		$cache->set('pawss'.$this->_session('user_uid'),md5($email));	//设置缓存
		$stmpArr['title']			="找回密码";
		$stmpArr['content']			='<div>
											<p>您好，<b>'.$this->_session('user_name').'</b> ：</p>
										</div>
										<div style="margin: 6px 0 60px 0;">
											<p>请点击这里，修改您的密码</p>
											<p><a href="http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Center/security/Rpasswordpag?pass='.$cache->get('pawss'.$this->_session('user_uid')).'">http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Center/security/Rpasswordpag?pass='.$cache->get('pawss'.$this->_session('user_uid')).'</a></p>
											<p>如果您的邮箱不支持链接点击，请将以上链接地址拷贝到你的浏览器地址栏中认证。</p>
										</div>
										<div style="color: #999;">
											<p>发件时间：'.date('Y/m/d H:i:s').'</p>
											<p>此邮件为系统自动发出的，请勿直接回复。</p>
										</div>';
		$emailsend=$this->email_send($stmpArr);	
		if($emailsend){
			$this->success('邮件发送成功', '__ROOT__/Center/security/Rpassword.html');
		}else{
			$this->error("邮箱找回密码失败，请联系管理员");
		}	
	}
	
	//邮箱找回密码提交
	public function emailPasssubmit(){
		$user=D('User');
		$users=$user->where('id='.$this->_session('user_uid'))->find();
		if($user->create()){
			$result = $user->where(array('id'=>$this->_session('user_uid')))->save();
			if($result){
				$cache = cache(array('expire'=>50));
				$cache->rm('pawss'.$this->_session('user_uid'));// 删除缓存
			 	$this->success("密码重置成功","__ROOT__/Center.html");
			}else{
			$this->error("新密码不要和原始密码相同！");
			}		
		}else{
			$this->error($user->getError());
		}

	}
//头像上传	
	public function portrait(){
		$this->homeVerify();
		$active['center']='active';
		$this->assign('active',$active);
		$head=$this->headPortrait('./Public/FaustCplus/php/img/big_user_'.$this->_session('user_uid').'.jpg');
		$this->assign('heads',$head);
		$this->display();
    }
	public  function test(){
		$this->homeVerify();
		 $tools = A('tools','Event');
		 $tools->aa();
	}
}