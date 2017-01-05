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
class CenterAction extends WinAction {
//-------------个人中心--------------
//首页
	public function index(){
		$this->homeVerify();
		//标题、关键字、描述
		$Site = D("Site");
		$site['title']="个人中心";
		$this->assign('si',$site);
		$active['center']='active';
		$this->assign('active',$active);
		$list=reset($this->user_details());	
		$this->assign('list',$list);
		//待收
		$collection=M('collection')->where('`uid`="'.$this->_session('user_uid').'" and `type`=0')->order('`time` ASC')->find();
		$this->assign('collection',$collection);
		$this->display();
    }
//我是投资者
	public function invest(){
		$this->homeVerify();
		$active['center']='active';
		$this->assign('active',$active);
		$this->assign('mid',$this->_get('mid'));
		switch($this->_get('mid')){
			case 'isclosed':	//正在收款的借款
			import('@.Plugin.DswjcmsApp.Pages');
			$count      = M('borrow_log')->where(array('type'=>7,'uid'=>$this->_session('user_uid')))->count();
			$Page       = new Pages($count,10);
			$Page->setConfig('theme',"%first% <li class='now'>%nowPage%/%totalPage%</li> %linkPage%");
			$show       = $Page->show();
			$isclosed=$this->bidRecords(7,0,$this->_session('user_uid'),$Page->firstRow.','.$Page->listRows);
			
			$this->assign('isclosed',$isclosed);
			$this->assign('show',$show);
			break;
			case 'isbid'://我投标的借款
			
			import('@.Plugin.DswjcmsApp.Pages');
			$count      = M('borrow_log')->where(array('type'=>3,'uid'=>$this->_session('user_uid')))->count();
			
			$Page       = new Pages($count,5);
			$Page->setConfig('theme',"%first% <li class='now'>%nowPage%/%totalPage%</li> %linkPage%");
			$show       = $Page->show();
			$isbid=$this->bidRecords(3,0,$this->_session('user_uid'),$Page->firstRow.','.$Page->listRows);
			$this->assign('isbid',$isbid);
			$this->assign('show',$show);
			break;
			case 'plan'://还款计划
			if($this->_get('bid')){	//还款计划
				$refun=D('Collection')->where('bid='.$this->_get('bid').' and uid='.$this->_session('user_uid'))->order('time ASC')->select();
				$this->assign('refun',$refun);
			}else{
				$this->error("误操作");
			}
			break;
			default:
		}
		$this->display();
    }
		
	/**
	* @标信息
	* @id		单条借款传入ID
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function borrows($id){
		$borrowing = M("borrowing");
		return $borrowing->where('id='.$id)->field('id,title,rates,deadline,money,state')->find();
	}

//协议书	
	public function agreement(){
		$this->homeVerify();
		if(!$this->_get('bid')){
			$this->error("操作有误！");
		}
		$refund=M('refund');
		$collection=M('collection');
		$re=$refund->where('uid="'.$this->_session('user_uid').'" and bid="'.$this->_get('bid').'"')->find();
		$co=$collection->where('uid="'.$this->_session('user_uid').'" and bid="'.$this->_get('bid').'"')->find();
		if($re || $co){
			$boow=reset($this->borrow_unicom($this->_get('bid')));
			$userinfo=D('Userinfo');
			$userin=$userinfo->field('name,idcard,uid')->relation(true)->where('uid='.$boow['uid'])->find();
			if($boow['type']==8){	//机构担保标
			$bid_record=$this->lendUser('7',$this->_get('bid'));
			$Guarantee = D("Guarantee");
			$gcompany=$Guarantee->field('gid')->relation(true)->where('bid="'.$this->_get('bid').'"')->find();
			//担保公司
			$guara=$this->guaranteeComp();
			$gcompanys=$guara[$gcompany['gcompany']];
			$this->assign('gcompany',$gcompanys);
			}else{
			$bid_record=$this->lendUser('3',$this->_get('bid'));	
			}
			$bid_recor=$this->lendUser('19',$this->_get('bid'));	
			$this->assign('bid',$bid_record);
			$this->assign('bids',$bid_recor);
			$refun=$refund->where('uid='.$boow['uid'].' and bid="'.$this->_get('bid').'"')->select();
			
			$this->assign('refun',$refun);
			$this->assign('boow',$boow);
			$this->assign('userin',$userin);
		}
		$this->display();
    }
	
}