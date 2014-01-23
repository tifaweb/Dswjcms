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
class HomeAction extends CommAction{
	/*
	*参数说明
	*	q		//需要操作的表
	*	n		//跳转提示语
	*	u		//跳转地址
	*	m		//存放LOG的数据并区分前后台		m[0]:1前台2后台3同时 其他为各LOG所需的数据
	*
	*/
	public function _initialize(){	//检测标是否过期，过期就改变状态，后期可直接读缓存
		header("Content-Type:text/html; charset=utf-8");
		$dirname = F('dirname')?F('dirname'):"Default";
		C('DEFAULT_THEME','template/'.$dirname);	//自动切换模板
		C('TMPL_ACTION_ERROR','Index/jump');	//默认错误跳转对应的模板文件
		C('TMPL_ACTION_SUCCESS','Index/jump');	//默认成功跳转对应的模板文件
		//友情链接
		$links = M('links');
		$links=$links->field('title,url,img')->where('state=0')->order('`order` ASC')->select();
		$this->assign('links',$links);
//----------------------------------------------------
//计划任务
		$msgTools = A('msg','Event');
		$borrows=D('Borrowing');
		$system=R('Sharing/systems');
		$this->assign('s',$system);
		//站点关闭
		if($system['sys_site_switch']==1){	
			$this->display('Index/close');
			exit;
		}
		$borrow=$borrows->where('`state`=1 or `state`=10')->select();
		if($borrow){
			foreach($borrow as $bw){
				if($bw['type'] < 7){	//招标中 正在招标、正在担保不是流转的才执行
					if(($bw['endtime']-time())<0){
						$this->flowStandard($bw);
						$borrows->where(array('id'=>$bw['id']))->save(array('state'=>4));
					}
				}
			}
		}
		//VIP是否过期
		$vippoints=D('Vip_points');
		$id=$this->_post("id");
		$list=$vippoints->relation(true)->field('uid,expiration_time,audit')->where('audit=2')->select();
		if($list){
			$vip=F('vip');
			foreach($list as $bw){
				if(($bw['expiration_time']-time())<0){	//如果到期
					$vippoints->where(array('uid'=>$bw['uid']))->save(array('audit'=>4));
					$msgTools->sendMsg(3,'VIP到期通知','用户'.$bw['username'].'您的VIP已经到期！','admin',$bw['username']);//站内信
					$this->userLog('VIP到期',$bw['uid']);//会员记录
				}else if((($bw['expiration_time']-time())<432000) && (time()-$vip['vip_'.$bw['uid']])>=86400){	//如果离到小于5天提示并且一天只能会发一条
					
					if($vip){
						$array['vip_'.$bw['uid']]=time();
						$as=array_merge($array,$vip);
						F('vip',$as);
					}else{
						F('vip',array('vip_'.$bw['uid']=>time()));
					}
					$remaining=floor(($bw['expiration_time']-time())/86400);		//剩余时间/86400
					$msgTools->sendMsg(3,'VIP即将到期通知','用户'.$bw['username'].'您的VIP将于'.$remaining.'天后到期！','admin',$bw['username']);//站内信
				}
			}
		}
		
		//逾期计划任务
		$refund=F('refund');
		$overdue=M('overdue');
		$coverdue=D('Coverdue');
		
		$models = new Model();
		$collection=D('Collection');
		$overd=$overdue->where('type=0 or type=2')->select();	//查询处于逾期上的逾期记录
		if($overd){
			foreach($overd as $ov){	
				if(time()-$refund['refund_'.$ov['bid']]>=86400){	//一天只执行一次
					$arr['days']=$ov['days']+1;
					if($arr['days']>30 && $ov['type'] !=='2'){	//如果逾期大于30系统代还
						$arr['type']=2;
						//执行还款
						$cover=$coverdue->relation(true)->where('bid='.$ov['bid'])->select();
						//$borrows->where(array('id'=>$ov['bid']))->save(array('state'=>9));
						foreach($cover as $co){
							$ar['money']=$co['money']+$this->penaltyInterest($co['money'],$co['days']);//投资人应收本息+罚息
							$ar['interest']=$co['interest']+$this->penaltyInterest($co['money'],$co['days']);//投资人应收利息+罚息
							$models->query("UPDATE `ds_money` SET `total_money` = total_money+".$ar['money'].",`available_funds` = available_funds+".$ar['money'].",`stay_interest` = stay_interest-".$co['interest'].",`make_interest` = make_interest+".$ar['interest'].",`due_in` = due_in-".$co['money']." WHERE `uid` =".$co['uid']);	//平台代付
							//投资者
							$money=M('money');
							$total=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$co['uid'])->find();	//查询资金
							//记录添加点
							$moneyLog=$this->moneyLog(array(0,'平台对【'.$co['title'].'】的代还款',$ar['money'],'平台',$total['total_money'],$total['available_funds'],$total['freeze_funds'],$co['uid']));//资金记录		
							$msgTools->sendMsg(3,'平台对【'.$co['title'].'】的代还款','平台对<a href="'.__ROOT__.'/Loan/invest/'.$co['bid'].'.html">【'.$co['title'].'】</a>的代还款，账户增加：'.$ar['money'].' 元','admin',$co['uid']);//站内信
						}
						$overdue->where(array('id'=>$ov['id']))->save(array('type'=>2));			//更新借款者逾期表
						$coverdue->where(array('bid'=>$ov['bid']))->save(array('type'=>2));		//更新投资人逾期表
					}
					$overdue->where(array('id'=>$ov['id']))->setInc('days',1);			//更新借款者逾期表
					$coverdue->where(array('bid'=>$ov['bid']))->setInc('days',1);		//更新投资人逾期表
					if($refund){
						$refund['refund_'.$ov['bid']]=time();
						F('refund',$refund);
					}else{
						F('refund',array('refund_'.$ov['bid']=>time()));
					}
				}
			}
		}
		
		//逾期
		$refund=M('refund');
		$refun=$refund->where('type=0 and time <='.time())->select();	//将所有逾期的借款信息查出来
		if($refun){
			foreach( $refun as $re){
				//停掉正常还款的借款，把状态改为逾期
				$refund->where('type=0 and bid='.$re['bid'])->save(array('type'=>2));	
				$collection->where('type=0 and bid='.$re['bid'])->save(array('type'=>2));	
				//把逾期的投资人查找出来
				$colle=$collection->relation(true)->where('type=2 and bid='.$re['bid'])->select();
				
				foreach($colle as $co){
					if(array_key_exists($co['uid'],$arrs)){
						$arrs[$co['uid']]['money']+=$co['money'];
						$arrs[$co['uid']]['interest']+=$co['interest'];
					}else{
						$arrs[$co['uid']]=array('uid'=>$co['uid'],'money'=>$co['money'],'interest'=>$co['interest'],'title'=>$co['title']);
					}
				}
				//添加到投资人逾期表
				foreach($arrs as $id=> $ar){
					$cadd['uid']=$id;
					$cadd['bid']=$re['bid'];
					$cadd['money']=$ar['money'];//逾期本息
					$cadd['interest']=$ar['interest'];//逾期利息
					$cadd['time']=time();
					$coverdue->add($cadd);
				}
				//添加到借款者逾期表
				$add['bid']=$re['bid'];
				$add['uid']=$re['uid'];
				$add['money']=$refund->where('type=2 and bid='.$re['bid'])->sum('money');	//统计逾期的本息
				$borrows->where(array('id'=>$re['bid']))->save(array('state'=>8));	//改变标为逾期
				$add['time']=time();
				$overdue->add($add);
			}
			F('refund',array('refund_'.$re['bid']=>time()));
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
		$this->success("用户退出成功", '__ROOT__/Logo/login.html');
	}
	
}
?>