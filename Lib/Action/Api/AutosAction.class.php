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
set_time_limit(0);//防止PHP执行时间超时
class AutosAction extends CommAction {
	
	public function index(){	
		$this->automaticBackup();	//数据库备份邮箱改送
	}
	public function timing(){
			$borrows=D('Borrowing');
			$borrow=$borrows->where('`state`=1 or `state`=2')->limit(10)->select();
			
			if($borrow){
				foreach($borrow as $bw){
					if($bw['type'] < 5){	//招标中 正在招标
						if(($bw['endtime']-time())<0){
							$borrows->where(array('id'=>$bw['id']))->save(array('state'=>14));
							$this->flowStandard($bw);
							
						}
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
			$refun=$refund->where('type=0 and time <='.time())->limit(10)->select();	//将所有逾期的借款信息查出来
			if($refun){
				foreach( $refun as $re){
					//停掉正常还款的借款，把状态改为逾期
					$assignment=M('assignment');
					$refund->where('type=0 and bid='.$re['bid'])->save(array('type'=>2));	
					$collection->where('type=0 and bid='.$re['bid'])->save(array('type'=>2));
					$assignment->where('`bid`='.$re['bid'])->save(array('type'=>2));	
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
					unset($arrs);
					//添加到借款者逾期表
					$add['bid']=$re['bid'];
					$add['uid']=$re['uid'];
					$add['money']=$refund->where('type=2 and bid='.$re['bid'])->sum('money');	//统计逾期的本息
					$borrows->where(array('id'=>$re['bid']))->save(array('state'=>8));	//改变标为逾期
					$add['time']=time();
					$overdue->add($add);
					$refunds['refund_'.$re['bid']]=time();
					F('refund',$refunds);
				}
				
			}
			//还款提前提醒
			$refund=D('Refund');
			$system=$this->systems();
			$time=strtotime("+$system[sys_refundDue] day");	//提前提醒设置的时间
			$refun=$refund->relation(true)->where('uid='.$this->_session('user_uid').' and type=0 and time<='.$time)->select();
			$emailrefund=F('emailrefund');
			
			foreach($refun as $r){
			if(time()-$emailrefund['refund_'.$r['bid']]>=86400){	//一天只执行一次
				$msg='
				<table class="table table-bordered table-hover">
							<thead>
							  <tr>
								<th>标题</th>
								<th>还款时间</th>
								<th>还款金额</th>
							  </tr>
							</thead>
							<tbody>
							  <tr>
								<td>'.$r['title'].'</td>
								<td>'.date('Y-m-d H:i:s',$r['time']).'</td>
								<td>'.number_format($r['money'],2,'.',',').' 元</td>
							  </tr>
							</tbody>
						</table>
				';
				$this->silSingle(array('title'=>'还款提前提醒','sid'=>$r['uid'],'msg'=>$msg));//站内信
				//邮件通知
				$mailNotice['uid']=$r['uid'];
				$mailNotice['title']='还款提前提醒';
				$mailNotice['content']='
				<div style="margin: 6px 0 60px 0;">
					<table class="table table-bordered table-hover">
						<thead>
						  <tr>
							<th>标题</th
							<th>还款时间</th>
							<th>还款金额</th>
						  </tr>
						</thead>
						<tbody>
						  <tr>
							<td>'.$r['title'].'</td>
							<td>'.date('Y-m-d H:i:s',$r['time']).'</td>
							<td>'.number_format($r['money'],2,'.',',').' 元</td>
						  </tr>
						</tbody>
					</table>p>
				</div>
				<div style="color: #999;">
					<p>发件时间：'.date('Y/m/d H:i:s').'</p>
					<p>此邮件为系统自动发出的，请勿直接回复。</p>
				</div>';
			$this->mailNotice($mailNotice);
			}
				if($emailrefund){
					$emailrefund['refund_'.$r['bid']]=time();
					F('emailrefund',$emailrefund);
				}else{
					F('emailrefund',array('refund_'.$r['bid']=>time()));
				}
			}
	}
}