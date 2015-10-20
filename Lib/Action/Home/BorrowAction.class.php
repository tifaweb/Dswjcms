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
		$linkage=$this->borrowLinkage();
		$this->assign('linkage',$linkage);
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['borrow']='active';
		$this->assign('active',$active);
		$head='<link  href="__PUBLIC__/css/style.css" rel="stylesheet">';
		$this->assign('head',$head);
		$Borrow=D("Borrowing");
		$borrow=$Borrow->where(array('uid'=>$this->_session('user_uid'),'id'=>$this->_get('mid')))->find();
		$borrow['data']=array_filter(explode(",",$borrow['data']));
		$this->assign('b',$borrow);
		$this->display();
    }
	
	
	//普通标发布
	public function ordinaryAdd(){
		$this->homeVerify();
		//$this->bidValidation(1);
		$model=D('Borrowing');
		$money=M('money');
		$mone=$money->where('uid="'.$this->_session('user_uid').'"')->find();
		$systems=$this->systems();
		//各种标条件判断
		if($this->_post('type')==0){	//秒还标
			$counters=$this->counters($this->_post('money'),$this->_post('rates'),$this->_post('deadline'),$this->_post('candra'),$this->_post('way'));	//利息计算
			if($this->_post('reward_type')==1){	//金额
				$reward=$this->_post('reward');
			}else if($this->_post('reward_type')==2){	//百分比
				$reward=$this->_post('reward')*0.01*$this->_post('money');
			}
			if($mone['available_funds']<($counters['interest']+$reward)){	//余额小于还款金额
				$this->error("余额必须大于".($counters['interest']+$reward).'元');
			}
		}
		if($this->_post('type')==3){	//净值标
			if(($mone['due_in']*$systems['sys_net']*0.01)<$this->_post('money')){	//净值额度小于借款金额
				$this->error("净值额度小于借款金额！");
			}
		}
		
		if($create=$model->create()){
			if($this->_post('img')){
				$create['data']=",".implode(",",$this->_post('img'));
			}
			$create['time']=time();
			$create['surplus']=$create['money'];
			$create['uid']=$this->_session('user_uid');
			$create['title']=$this->_post('title');
			$result = $model->add($create);
			if($result){
				//记录添加点
				$this->userLog('发布【'.$create['title'].'】成功，等待审核',$result);	//会员记录
				$this->silSingle(array('title'=>'会员成功发布【'.$create['title'].'】','sid'=>$this->_session('user_uid'),'msg'=>'会员'.$this->_session('user_name').'成功发布【'.$create['title'].'】，等待管理员审核！'));//站内信
				$this->success("发标成功","__ROOT__/Center/loan/issue");			
			}else{
				 $this->error("发标失败");
			}
		}else{
			$this->error($model->getError());
			
		}
	}
	
	//普通标编辑
	public function ordinaryEdit(){
		$this->homeVerify();
		//$this->bidValidation();
		$model=D('Borrowing');
		$money=M('money');
		$mone=$money->where(array('uid'=>$this->_session('user_uid')))->find();
		$systems=$this->systems();
		//各种标条件判断
		if($this->_post('type')==0){	//秒还标
			$counters=$this->counters($this->_post('money'),$this->_post('rates'),$this->_post('deadline'),$this->_post('candra'),$this->_post('way'));	//利息计算
			if($this->_post('reward_type')==1){	//金额
				$reward=$this->_post('reward');
			}else if($this->_post('reward_type')==2){	//百分比
				$reward=$this->_post('reward')*0.01*$this->_post('money');
			}
			if($mone['available_funds']<($counters['interest']+$reward)){	//余额小于还款金额
				$this->error("余额必须大于".($counters['interest']+$reward).'元');
			}
		}
		if($this->_post('type')==3){	//净值标
			if(($mone['due_in']*$systems['sys_net'])<$this->_post('money')){	//净值额度小于借款金额
				$this->error("净值额度小于借款金额！");
			}
		}
		
		if($create=$model->create()){
			$create['surplus']=$create['money'];
			if($this->_post('img')){
				$create['data']=",".implode(",",$this->_post('img'));
			}
			$result = $model->where(array('id'=>$this->_post('sid')))->save($create);
			if($result){
				//记录添加点
				$this->userLog('【'.$create['title'].'】更新成功，等待审核',$result);	//会员记录
				$this->silSingle(array('title'=>'【'.$create['title'].'】更新成功','sid'=>$this->_session('user_uid'),'msg'=>'会员'.$this->_session('user_name').'成功更新【'.$create['title'].'】，等待管理员审核！'));//站内信
				$this->success("更新成功");			
			}else{
				 $this->error("更新失败");
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