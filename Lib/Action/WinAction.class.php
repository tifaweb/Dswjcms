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
class WinAction extends CommAction{
	public function _initialize(){
		header("Content-Type:text/html; charset=utf-8");
		$dirname = F('wdirname')?F('wdirname'):"Default";
		C('DEFAULT_THEME','template/'.$dirname);	//自动切换模板
		C('TMPL_ACTION_ERROR','Index/jump');	//默认错误跳转对应的模板文件
		C('TMPL_ACTION_SUCCESS','Index/jump');	//默认成功跳转对应的模板文件
		//友情链接
		$links = M('links');
		$links=$links->field('title,url,img')->where('state=0')->order('`order` ASC')->select();
		$this->assign('links',$links);
		$system=$this->systems();
		$this->assign('s',$system);
		//站点关闭
		if($system['sys_site_switch']==1){	
			$this->display('Index/close');
			exit;
		}
	}
	
	/**
	*
	*前台退出
	*
	*/
	public function exits(){
		session('user_uid',null);
		session('user_name',null);
		$this->success("用户退出成功", '__ROOT__/Win/login.html');
	}
	
	/**
	 * @前台验证
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	 public function WxVerify(){
		if(!$this->_session('user_uid')){
			$this->error("请先登陆",'__ROOT__/Win/Login.html');
		}
	 }
}
?>