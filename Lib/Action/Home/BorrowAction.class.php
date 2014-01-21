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
class BorrowAction extends HomeAction {
//-------------借款标发布--------------
	//普通标显示
	public function index(){
		$linkage=R('Sharing/borrowLinkage');
		$this->assign('linkage',$linkage);
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['borrow']='active';
		$this->assign('active',$active);
		$head='<link  href="__PUBLIC__/css/style.css" rel="stylesheet">';
		$this->assign('head',$head);
		$this->display();
    }
	
	//流转标显示
	public function flow(){
		$linkage=R('Sharing/borrowLinkage');
		$this->assign('linkage',$linkage);
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['borrow']='active';
		$this->assign('active',$active);
		$head='<link  href="__PUBLIC__/css/style.css" rel="stylesheet">';
		$this->assign('head',$head);
		$this->display();
    }
	
	//流转标发标
	public function flowAdd(){
		$this->homeVerify();
		$this->bidValidation();
		$msgTools = A('msg','Event');
		$model=D('Borrowing');
		if($create=$model->create()){
			if($this->_post('flow_deadline')<=$this->_post('deadline')){
				if($this->_post('min_limit')<=$this->_post('flow_deadline')){
					$result = $model->add();
					if($result){
						//记录添加点
						$this->userLog('发布流转标成功，等待审核',$result);	//会员记录
						$msgTools->sendMsg(3,'会员成功发布流转标','会员'.$this->_session('user_name').'成功发布流转标，等待管理员审核！','admin',$this->_session('user_name'));//站内信
						$this->success("发标成功","__ROOT__/Center/loan/issue");			
					}else{
						 $this->error("发标失败");
					}
				}else{
					$this->error("最低认购期限必须小于等于流转期限");
				}	
			}else{
				$this->error("流转期限必须小于等于原借款期限");
			}
		}else{
			$this->error($model->getError());
			
		}
	}
	
	//普通标发布
	public function ordinaryAdd(){
		$this->homeVerify();
		$this->bidValidation();
		$msgTools = A('msg','Event');
		$model=D('Borrowing');
		$money=M('money');
		$mone=$money->where('uid='.$this->_session('user_uid'))->find();
		$ufees=M('ufees');
		$ufee=$ufees->field('total')->where('uid='.$this->_session('user_uid'))->find();
		$systems=$this->systems();
		//各种标条件判断
		if($this->_post('type')==3){	//净值标
			if(($mone['due_in']*$systems['sys_net'])<$this->_post('money')){	//净值额度小于借款金额
				$this->error("净值额度小于借款金额！");
			}
		}
		if($this->_post('type')==4){	//信用标
			if($ufee['total']<$systems['sys_credit']){	//积分不能低于设置积分
				$this->error("信用等级不足！");
			}
		}
		
		if($create=$model->create()){
			$result = $model->add();
			if($result){
				switch($this->_post('type')){
					case 0:
					$type="秒还标";
					break;
					case 1:
					$type="抵押标";
					break;
					case 2:
					$type="质押标";
					break;
					case 3:
					$type="净值标";
					break;
					case 4:
					$type="信用标";
					break;
					case 5:
					$type="担保标";
					break;
				}
				//记录添加点
				$this->userLog('发布'.$type.'成功，等待审核',$result);	//会员记录
				$msgTools->sendMsg(3,'会员成功发布'.$type,'会员'.$this->_session('user_name').'成功发布'.$type.'，等待管理员审核！','admin',$this->_session('user_name'));//站内信
				$this->success("发标成功","__ROOT__/Center/loan/issue");			
			}else{
				 $this->error("发标失败");
			}
		}else{
			$this->error($model->getError());
			
		}
	}
	
	//借款页
	public function welfare(){
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['borrow']='active';
		$this->assign('active',$active);
		$this->display();
    }
}