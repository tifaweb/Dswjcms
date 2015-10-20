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
class AdminCommAction extends CommAction {
	protected function _initialize() {
		//后台自定义地址
		if(strpos(__SELF__,TIFAWEB_DSWJCMS)<1){
			$this->jumps(__ROOT__.'/error.html');
		}
		header("Content-Type:text/html; charset=utf-8");
		$this->adminVerify();
	}
	
	/**
	 * @后台验证
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	protected function adminVerify(){
		if($this->_session('admin_uid')){
			$users=M('admin')->field('username,password')->where(array('id'=>$this->_session('admin_uid')))->find();
			if($this->_session('admin_verify') !== MD5($users['username'].DS_ENTERPRISE.$users['password'].DS_EN_ENTERPRISE)){
				session('admin_uid',null);
				session('admin_name',null);
				session('admin_verify',null);
				$this->error("请先重新登陆",'__APP__/TIFAWEB_DSWJCMS/Logo.html');
			}
		}else{
			$this->error("请先登陆",'__APP__/TIFAWEB_DSWJCMS/Logo.html');
		}
	 }
	
	/**
	 *
	 * @后台添加
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */	
	public function tfAdd(){
		$this->add();
	}
	
	/**
	 *
	 * @后台更新
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */	
	public function tfUpda(){
		$this->upda();
	}
	
	/**
	 *
	 * @后台更新带验证码
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */	
	public function tfUpdas(){
		if($this->_session('verify') != md5($this->_post('proving'))) {
		   $this->error('验证码错误！');
			exit;
		}
		$this->upda();
	}
	
	/**
	 *
	 * @后台删除
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */	
	public function tfDel(){
		$this->del();
	}
	
	/**
	 *
	 * @带积分更新
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */	
	public function iUpda(){
		$this->integral_upda();
	}
	
	/**
	 *
	 * @后台更新带验证码
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */	
	public function iUpdas(){
		if($this->_session('verify') != md5($this->_post('proving'))) {
		   $this->error('验证码错误！');
			exit;
		}
		$this->integral_upda();
	}
}

?>