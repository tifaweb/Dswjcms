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
	public function _initialize() {
	   header("Content-Type:text/html; charset=utf-8");
       import('ORG.Util.Authority');//加载类库
       $auth=new Authority();	
	   //后台 admin_name
		  $uid=$this->_session('admin_uid');
		  $user =$this->_session('admin_name');
	   $prompt=$uid?"你没有权限":"请登陆";
	   $url=$uid?"":"__ROOT__/Admin/Logo.html";
	   if($user !="admin"){
		 if(!$auth->getAuth(GROUP_NAME.'/'.MODULE_NAME.'/'.ACTION_NAME,$uid)){
			//echo $user;
			 $this->error($prompt,$url);
		 }
	   }
		$system=$this->systems();
		$this->assign('s',$system);
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
	 * @后台更新
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */	
	public function iUpda(){
		$this->integral_upda();
	}
	
}

?>