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
		$list=reset($this->user_details());	
		
		$this->assign('list',$list);
		$record=$this->moneyRecord($this->_session('user_uid'),3);	//最近资金记录
		$this->assign('record',$record);
		//数据统计
		//投资者
		$Collection=D('Collection');
		$collection=$Collection->where(array('uid'=>$this->_session('user_uid')))->relation('borrowing')->select();	//回款统计
		$collarr=array();
		foreach($collection as $coll){
			if($coll['type']==0){
				if(in_array($coll['bid'],$collarr)){
						$investment['returned_total']+=$coll['money'];	//回款中总额
				}else{
					$collarr[$coll['bid']]=$coll['bid'];
						$investment['returned_total']+=$coll['money'];	//回款中总额
						$investment['returned']+=1;	//回款中笔数
				}	
			}
		}
		unset($collection);
		$Coverdue=M('coverdue');
		$coverdue=$Coverdue->where(array('uid'=>$this->_session('user_uid'),'type'=>0))->select();//逾期统计
		foreach($coverdue as $cove){
			$investment['overdueReturned']+=1;	//逾期笔数
			$investment['overdueReturned_total']+=$cove['money'];	//逾期金额
		}
		
		//借款者
		$Refund=D('Refund');
		$refund=$Refund->where(array('uid'=>$this->_session('user_uid')))->relation('borrowing')->select();	//回款统计
		
		$collarr=array();
		foreach($refund as $ref){
			if($ref['type']==0){
				if(in_array($ref['bid'],$collarr)){
					$investment['refund_total']+=$ref['money'];	//还款中总额
				}else{
					$collarr[$ref['bid']]=$ref['bid'];
					$investment['refund_total']+=$ref['money'];	//还款中总额
					$investment['refund']+=1;	//还款中笔数
				}	
			}
		}
		unset($refund);
		$Overdue=M('overdue');
		$overdue=$Overdue->where(array('uid'=>$this->_session('user_uid'),'type'=>0))->select();//逾期统计
		foreach($overdue as $ove){
			$investment['overdueRefund']+=1;	//逾期笔数
			$investment['overdueRefund_total']+=$ove['money'];	//逾期金额
		}
		$this->assign('investment',$investment);
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
			$isclosed=$this->bidRecords(7,0,$this->_session('user_uid'));
			$this->assign('isclosed',$isclosed);
			break;
			case 'isbid'://逾期的借款
			
			import('ORG.Util.Page');// 导入分页类
			$count      = M('borrow_log')->where(array('type'=>3,'uid'=>$this->_session('user_uid')))->count();// 查询满足要求的总记录数
			
			$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
			$show       = $Page->show();// 分页显示输出
			$isbid=$this->bidRecords(3,0,$this->_session('user_uid'),$Page->firstRow.','.$Page->listRows);	
			$this->assign('isbid',$isbid);
			$this->assign('page',$show);// 赋值分页输出
			break;
			case 'overdue'://我投标的借款
			$overdue=$this->overdue($this->_session('user_uid'));//逾期信息
			$this->assign('overdue',$overdue);
			break;
			case 'plan'://还款计划
			if($this->_get('bid')){	//还款计划
				$refun=D('Collection')->where(array('bid'=>$this->_get('bid'),'uid'=>$this->_session('user_uid')))->order('time ASC')->select();
				$this->assign('refun',$refun);
			}else{
				$this->error("误操作");
			}
			break;
			default:
		}
		$this->display();
    }
	
//我是借款者	
	public function loan(){
		$this->homeVerify();
		$active['center']='active';
		$this->assign('active',$active);
		$this->assign('mid',$this->_get('mid'));
		switch($this->_get('mid')){
			case 'issue':	//发布的借款
			$list=$this->borrowUidUnicom($this->_session('user_uid'),'`id` DESC');
			$this->assign('list',$list);
			break;
			case 'overdue':	//逾期的借款
			$overdue=$this->verdue($this->_session('user_uid'));//逾期信息
			$this->assign('overd',$overdue);
			break;
			case 'is':	//正在还款的借款
			$list=$this->borrowUidUnicom($this->_session('user_uid'),'`id` DESC');
			$this->assign('list',$list);
			break;
			case 'plan':	//还款计划
			if($this->_get('bid')){	//还款计划
				$refun=M('refund')->where(array('bid'=>$this->_get('bid')))->order('time ASC')->select();
				$borrow=M('borrowing')->field('money')->where(array('id'=>$this->_get('bid')))->find();
				$interest=$this->interest($this->_session('user_uid'),$borrow['money']);
				$this->assign('interest',$interest);
				$this->assign('refun',$refun);
			}else{
				$this->error("操作有误");
			}
			break;
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
	protected function borrows($id){
		$borrowing = M("borrowing");
		return $borrowing->where(array('id'=>$id))->field('id,title,rates,deadline,money,state')->find();
	}
	
//资金管理	
	public function fund(){
		$this->homeVerify();
		$this->assign('mid',$this->_get('mid'));
		switch($this->_get('mid')){
			case 'fundrecord':	//资金明细
			$moneys=M('money')->where(array('uid'=>$this->_session('user_uid')))->find();//资金
			//待还总金额（管理费+逾期管理费+逾期罚息+原本息）
			$systems=$this->systems();
			$borrs=D('Refund')->field('interest,bid')->relation('borrowing')->where(array('uid'=>$this->_session('user_uid'),'type'=>0))->select();
			foreach($borrs as $bo){
				$moneys['stay_still']+=$bo['interest']/($bo['rates']*0.01/12)*$systems['sys_InterestMF'];
			}
					//待还逾期
			$verdue=$this->verdue($this->_session('user_uid'),1);
			foreach($verdue as $ver){
				$moneys['stay_still']+=$ver['overdue']+$ver['penalty'];
			}
			
					//待收逾期
			$overdue=$this->overdue($this->_session('user_uid'),1);
			foreach($overdue as $ove){
				$moneys['due_in']+=$ove['penalty'];
				$moneys['stay_interest']+=$ove['penalty'];
			}
			import('ORG.Util.Page');// 导入分页类
			$count      = D('Money_log')->where(array('type'=>0,'uid'=>$this->_session('user_uid')))->count();// 查询满足要求的总记录数
			$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
			$show       = $Page->show();// 分页显示输出
			$record=D('Money_log')->relation(true)->where(array('type'=>0,'uid'=>$this->_session('user_uid')))->order('`time` DESC,`id` DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($record as $id => $r){
				$record[$id]['finetypename']=$this->finetypeName($r['finetype']);
			}
			$this->assign('page',$show);// 赋值分页输出
			$this->assign('record',$record);
			$this->assign('money',$moneys);
			$active['center']='active';
			$this->assign('active',$active);
			break;
			case 'bank':	//银行账户
			$available_funds=M('money')->where(array('uid'=>$this->_session('user_uid')))->getField('available_funds');
			$userinfos = D('Userinfo')->relation(true)->field('uid,name,bank,bank_name,bank_account,certification')->where(array('uid'=>$this->_session('user_uid')))->find();	
			if($userinfos['certification']!=='2'){
				$this->error("请先通过实名认证",'__ROOT__/Center/approve/autonym.html');
			}
			$userinfos['available_funds']=$available_funds;
			$this->assign('userinfos',$userinfos);
			$list=M('unite')->field('name,value')->where('`state`=0 and `pid`=14')->order('`order` asc,`id` asc')->select();
			$this->assign('list',$list);
			
			break;
			case 'draw'://账户提现
			$userinfos = D('Userinfo')->relation(true)->field('uid,name,bank,bank_name,bank_account,certification')->where(array('uid'=>$this->_session('user_uid')))->find();
			if($userinfos['certification']!=='2'){
				$this->error("请先通过实名认证",'__ROOT__/Center/approve/autonym.html');
			}
			$available_funds=M('money')->where(array('uid'=>$this->_session('user_uid')))->getField('available_funds');
			$userinfos['available_funds']=$available_funds;
			$list=M('unite')->field('name,value')->where('`state`=0 and `pid`=14')->order('`order` asc,`id` asc')->select();
			foreach($list as $lt){
				if($lt['value']==$userinfos['bank']){
					$userinfos['banks']=$lt['name'];
					break;
				}
			}
			$this->assign('list',$list);
			$this->assign('userinfos',$userinfos);
			break;
			case 'drawrecord'://提现记录
			import('ORG.Util.Page');// 导入分页类
			$count      = D('Withdrawal')->where(array('uid'=>$this->_session('user_uid')))->count();// 查询满足要求的总记录数
			$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
			$show       = $Page->show();// 分页显示输出
			$withuser=$this->showUser('',$this->_session('user_uid'),'',$Page->firstRow.','.$Page->listRows);
			$this->assign('withuser',$withuser);
			$this->assign('page',$show);// 赋值分页输出
			break;
			case 'inject':	//充值
			$audit=$this->offlineBank();
			$this->assign('audit',$audit);
			$online=M('online');
			$onlines=$online->field('id,name')->where('`state`=0')->order('`order` asc,`id` asc')->select();
			$this->assign('onlines',$onlines);
			break;
			case 'injectrecord':	//充值记录
			import('ORG.Util.Page');// 导入分页类
			$count      = D('Recharge')->where(array('uid'=>$this->_session('user_uid')))->count();// 查询满足要求的总记录数
			$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
			$show       = $Page->show();// 分页显示输出
			$showuser=$this->rechargeUser('',$this->_session('user_uid'),'',$Page->firstRow.','.$Page->listRows);
			$this->assign('showuser',$showuser);
			$this->assign('page',$show);// 赋值分页输出
			break;
			
		}
		$this->display();
    }
	
//提现申请	
	public function drawUpda(){
		$this->homeVerify();
		$withdrawal=D('Withdrawal');
		$user=D('User');
		$money=M('money');
		$userinfo=M('userinfo');
		$message=reset($userinfo->field('certification,bank,bank_name,bank_account')->where(array('uid'=>$this->_session('user_uid')))->select());//获取姓名、银行帐号信息用来判断
		if($message['certification']!=='2'){
			$this->error("请先通过实名认证",'__ROOT__/Center/approve/autonym.html');
		}
		if(!$message['bank'] || !$message['bank_name'] || !$message['bank_account'] ){
			$this->error("请先填写银行账户",'__ROOT__/Center/fund/mid/bank.html');
		}
		$moneys=reset($money->field('total_money,available_funds,freeze_funds')->where(array('uid'=>$this->_session('user_uid')))->select());
		$pay_password=$user->where(array('id'=>$this->_session('user_uid')))->getField('pay_password');
		if($user->userPayMd5($this->_post('password'))==$pay_password){	//验证支付密码
			if($this->_post('money')<=$moneys['available_funds']){	//提现金额必须小于可用余额
				if($create=$withdrawal->create()){
					$create['withdrawal_poundage']=$this->withdrawalPoundage($this->_post('money'));
					$create['account']=$this->_post('money')-$create['withdrawal_poundage'];
					$create['time']=time();
					$result = $withdrawal->add($create);
					if($result){
						$moneyarr['available_funds']=$moneys['available_funds']-$create['money'];
						$moneyarr['freeze_funds']=$moneys['freeze_funds']+$create['money'];
						$money->where(array('uid'=>$this->_session('user_uid')))->save($moneyarr);
						$this->moneyLog(array(0,'提现申请成功，冻结资金',$this->_post('money'),'平台',$moneys['total_money'],$moneyarr['available_funds'],$moneyarr['freeze_funds']),4);	//资金记录
						$this->success("提现申请成功",'__ROOT__/Center/fund/mid/drawrecord.html');
					}else{
						$this->error("提现申请失败");
					}
					
				}else{
					$this->error($withdrawal->getError());
				}
			}else{
				$this->error("提现金额需小于可提现金额");
			}
		}else{
			$this->error("支付密码错误");
		}
	}
//提现撤销	
	public function drawUndo(){
		$this->homeVerify();
		$id=$this->_post('id');
		$withdrawal=D('Withdrawal');
		$user=D('User');
		$money=M('money');
		$userinfo=M('userinfo');
		
		$moneys=reset($money->field('total_money,available_funds,freeze_funds')->where(array('uid'=>$this->_session('user_uid')))->select());
		$withdrawals=M('withdrawal')->field('money')->where(array('id'=>$id))->find();
		if($create=$withdrawal->create()){
			$result = $withdrawal->where(array('id'=>$id))->save();	//改变提现状态
			if($result){
				$moneyarr['available_funds']=$moneys['available_funds']+$withdrawals['money'];
				$moneyarr['freeze_funds']=$moneys['freeze_funds']-$withdrawals['money'];
				$money->where(array('uid'=>$this->_session('user_uid')))->save($moneyarr);
				$this->moneyLog(array(0,'提现撤销',$withdrawals['money'],'平台',$moneys['total_money'],$moneyarr['available_funds'],$moneyarr['freeze_funds']),12);	//资金记录
				$this->success("提现撤销成功",'__ROOT__/Center/fund/mid/drawrecord.html');
			}else{
				$this->error("提现撤销失败");
			}
			
		}else{
			$this->error($withdrawal->getError());
		}
	}
//账号充值	
	public function injectAdd(){
		$this->homeVerify();
		$recharge=D('Recharge');
		
		if($create=$recharge->create()){	
			 	$create['nid']				=$this->orderNumber();	//订单号
				$create['uid']				=$this->_session('user_uid');	//用户ID
				$create['poundage']			=$this->topUpFees($create['money']);//充值手续费
				$create['account_money']	=$create['money']-$create['poundage'];//到帐金额
				$create['time']				=time();
				$create['type']				=1;
				if($this->_post('way')==0){
					if(!$this->_post('oid')){
						$this->error("请选择充值类型");
					}
					if(!$this->_post('number')){
						$this->error("流水号必须");
					}
					$create['genre']				=0;		//线下充值
				}else{	//网上充值
			
			
			
			
			
			
			
				}
				$result = $recharge->add($create);
			if($result){
				$this->success("充值已提交","__ROOT__/Center/fund/mid/injectrecord.html");			
			}else{
				 $this->error("充值提交失败");
			}	
		}else{
			$this->error($recharge->getError());
			
		}
	}
	
//认证中心	
	public function approve(){
		$this->homeVerify();
		$head='<link  href="__PUBLIC__/css/style.css" rel="stylesheet">';
		$this->assign('head',$head);
		$unite=M('unite');
		$userinfo=M('userinfo');
		$list=$unite->field('name,value')->where('`state`=0 and `pid`=13')->order('`order` asc,`id` asc')->select();
		$this->assign('list',$list);
		$user_details=$this->user_details();
		$certification=$user_details[0][certification];
		$this->assign('user_details',$user_details);
		$userfo=$userinfo->field('qq,certification')->where(array('uid'=>$this->_session('user_uid')))->find();
		
		if($this->_get('mid')=='video'){	//视频认证
			if($userfo['certification']!=='2'){
				$this->error("请先通过实名认证！",'__ROOT__/Center/approve/autonym.html');
			}
			if(!$userfo['qq']){
				$this->error("请先完善个人资料！",'__ROOT__/Center/basic/personal_data.html');
			}
		}
		if($this->_get('mid')=='scene'){	//现场认证
			if($userfo['certification']!=='2'){
				$this->error("请先通过实名认证！",'__ROOT__/Center/approve/autonym.html');
			}
			if(!$userfo['qq']){
				$this->error("请先完善个人资料！",'__ROOT__/Center/basic/personal_data.html');
			}
		}
		$this->assign('certification',$certification);
		$this->assign('mid',$this->_get('mid'));
		$this->display();
    }
	
	//注册AJAX验证
	public function ajaxverify(){
		if($this->_post("name")=="cellphone"){	//验证手机
			$user=D('Userinfo');
			$row=$user->where('cellphone="'.$this->_post('param').'"')->count();
			if($row){
				echo '{
					"info":"手机号已存在！",
					"status":"n"
				 }';
			}else{
				echo '{
					"info":"可以注册！",
					"status":"y"
				 }';
			}
		}
	}
	
	//手机号码更换
	public function cellphoneedit(){
		M('userinfo')->where(array('uid'=>$this->_session('user_uid')))->save(array('cellphone'=>$this->_post('cellphone')));
		$this->success("手机更换成功");
	}
	
	//实名认证提交
	function autonymUpda(){
		$userinfo=D('Userinfo');
		
		if(count($this->_post('idcard_img'))<2) {
		   $this->error('身份证证未上传！','__ROOT__/Center/approve/autonym.html');
			exit;
		}
		
		if($create=$userinfo->create()){
			$userinfo->where(array('uid'=>$this->_session('user_uid')))->save($create);
			$this->success("申请成功");
		}else{
			$this->error($userinfo->getError(),'__ROOT__/Center/approve/autonym.html');
		}
	}
	
	//手机手动认证
	function appphone(){
		$userinfo=D('Userinfo');
		if($create=$userinfo->create()){
			$create['cellphone_audit']=1;
			$userinfo->where(array('uid'=>$this->_session('user_uid')))->save($create);
			$this->success("申请成功");
		}else{
			$this->error($userinfo->getError());
		}
	}
	
	//邮箱验证
	public function emailVerify(){
		$this->homeVerify();
		$userinfo=M('user');
		$smtp=M('smtp');
		$stmpArr=$smtp->find();
		$getfield = $userinfo->where(array('id'=>$this->_session('user_uid'),'email'=>$this->_post('email')))->find();
		if(!$getfield){		
			if($userinfo->create()){
				$result = $userinfo->where(array('id'=>$this->_session('user_uid')))->save();
				if(!$result){
					$this->error("邮箱未能发送，请联系管理员");
				}		
			}else{
				$this->error($userinfo->getError());
			}
		}
		$stmpArr['receipt_email']	=$this->_post('email');
		$stmpArr['title']			="用户激活邮件";
		$stmpArr['content']			='<div>
											<p>您好，<b>'.$this->_session('user_name').'</b> ：</p>
										</div>
										<div style="margin: 6px 0 60px 0;">
											<p>欢迎加入<strong>'.$stmpArr['addresser'].'</strong>！请点击下面的链接来认证您的邮箱。</p>
											<p><a href="http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Center/emailVerifyConfirm/'.base64_encode($this->_session('user_uid')).'">http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Center/emailVerifyConfirm/'.base64_encode($this->_session('user_uid')).'</a></p>
											<p>如果您的邮箱不支持链接点击，请将以上链接地址拷贝到你的浏览器地址栏中认证。</p>
										</div>
										<div style="color: #999;">
											<p>发件时间：'.date('Y/m/d H:i:s').'</p>
											<p>此邮件为系统自动发出的，请勿直接回复。</p>
										</div>';
		$emailsend=$this->email_send($stmpArr);	
		if($emailsend){
			$this->success('邮件发送成功', '__ROOT__/Center/approve/email.html');
		}else{
			$this->error("邮箱激活失败，请联系管理员");
		}	
	}
	//邮箱验证确认
	public function emailVerifyConfirm(){
		//$this->homeVerify();
		$userinfo=M('userinfo');
		$username=base64_decode($this->_get('email_audit'));
			$emailVerifyConfirm['email_audit']=$username?2:0;
			$result = $userinfo->where(array('uid'=>$username))->save($emailVerifyConfirm);
			if($result){
			//记录添加点
			$sendMsg=$this->silSingle(array('title'=>'用户通过邮箱验证','sid'=>$this->_session('user_uid'),'msg'=>'用户通过邮箱验证'));//站内信
			$this->userLog('通过邮箱验证');//前台操作
			$arr['member']=array('uid'=>$this->_session('user_uid'),'name'=>'mem_email_audit');
			$vip_points=M('vip_points');	
			$vips=$vip_points->where(array('uid'=>$this->_session('user_uid')))->find();
			if($vips['audit']==2){	//判断是不是开通了VIP
				$arr['vip']=array('uid'=>$this->_post('uid'),'name'=>'vip_email_audit');
			}
			$userss=M('user');
			$promotes=$userss->where(array('id'=>$this->_session('user_uid')))->find();
			if($promotes['uid']){	//判断是不是有上线
				$arr['promote']=array('uid'=>$promotes['uid'],'name'=>'pro_email_audit');
			}
			$integralAdd=$this->integralAdd($arr);	//积分操作
				$this->success('邮箱已激活','__ROOT__/Center.html');
			}else{
				$this->error("邮箱激活失败，请联系管理员");
			}		
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
		$users=$user->where(array('id'=>$this->_session('user_uid')))->find();
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
	
	//邮箱找回交易密码
	public function dealemailBack(){
		$this->homeVerify();
		$smtp=M('smtp');
		$stmpArr=$smtp->find();
		$email=$this->_post('email');
		$stmpArr['receipt_email']	=$email;
		$cache = cache(array('expire'=>3600));
		$cache->set('dealpawss'.$this->_session('user_uid'),md5($email));	//设置缓存
		$stmpArr['title']			="找回交易密码";
		$stmpArr['content']			='<div>
											<p>您好，<b>'.$this->_session('user_name').'</b> ：</p>
										</div>
										<div style="margin: 6px 0 60px 0;">
											<p>请点击这里，修改您的交易密码</p>
											<p><a href="http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Center/security/dealRpasswordpag?pass='.$cache->get('dealpawss'.$this->_session('user_uid')).'">http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Center/security/dealRpasswordpag?pass='.$cache->get('dealpawss'.$this->_session('user_uid')).'</a></p>
											<p>如果您的邮箱不支持链接点击，请将以上链接地址拷贝到你的浏览器地址栏中认证。</p>
										</div>
										<div style="color: #999;">
											<p>发件时间：'.date('Y/m/d H:i:s').'</p>
											<p>此邮件为系统自动发出的，请勿直接回复。</p>
										</div>';
		$emailsend=$this->email_send($stmpArr);	
		if($emailsend){
			$this->success('邮件发送成功', '__ROOT__/Center/security/dealRpassword.html');
		}else{
			$this->error("邮箱找回密码失败，请联系管理员");
		}	
	}
	
	//邮箱找回交易密码提交
	public function dealemailPasssubmit(){
		$user=D('User');
		$users=$user->where(array('id'=>$this->_session('user_uid')))->find();
		if($user->create()){
			$result = $user->where(array('id'=>$this->_session('user_uid')))->save();
			if($result){
				$cache = cache(array('expire'=>50));
				$cache->rm('dealpawss'.$this->_session('user_uid'));// 删除缓存
			 	$this->success("交易密码重置成功","__ROOT__/Center.html");
			}else{
			$this->error("新交易密码不要和原始交易密码相同！");
			}		
		}else{
			$this->error($user->getError());
		}

	}
//基本设置	
	public function basic(){
		$this->homeVerify();
		$unite=M('unite');
		$list=$unite->field('pid,name,value')->where('`state`=0 and (`pid`=8 or `pid`=9 or `pid`=10 or `pid`=11 or `pid`=12)')->order('`order` asc,`id` asc')->select();
		foreach($list as $lt){
			switch($lt[pid]){
				case 8:
				$education[]=$lt;
				break;
				case 9:
				$monthly_income[]=$lt;
				break;
				case 10:
				$housing[]=$lt;
				break;
				case 11:
				$buy_cars[]=$lt;
				break;
				case 12:
				$industry[]=$lt;
				break;
			}
		}
		$citys=$this->city();
		$userinfo=M('userinfo');
		$userinfo=$userinfo->field('location,marriage,education,monthly_income,housing,buy_cars,qq,fixed_line,industry,company')->where(array('uid'=>$this->_session('user_uid')))->order('`id` asc')->select();		
		$userinfo[0]['location']=explode(" ",$userinfo[0]['location']);
		foreach($userinfo[0]['location'] as $id=>$location){
			$lon.=$citys[$location]." ";
		}
		$userinfo[0]['location']=$lon;
		$this->assign('userinfo',$userinfo);
		$this->assign('education',$education);
		$this->assign('monthly_income',$monthly_income);
		$this->assign('housing',$housing);
		$this->assign('buy_cars',$buy_cars);
		$this->assign('industry',$industry);
		$this->assign('list',$list);
		$this->assign('mid',$this->_get('mid'));
		$active['center']='active';
		$this->assign('active',$active);
		$this->display();
    }
	
	
	//站内信
	public function mails(){
		$active['center']='active';
		$this->assign('active',$active);
		$this->assign('mid',$this->_get('mid'));
		$this->homeVerify();
		//标题、关键字、描述
		$active['review']='active';
		$this->assign('active',$active);
		//区分会员本人登陆还是其它人访问
		$this->homeVerify();
		$user_uid=$this->_session('user_uid');
		if($this->_get('pid')=='discuss'){
			$site['title']="发出的评论";
		}else{
			$site['title']="收到的通知";
		}
		$site['link']=1;
		$this->assign('si',$site);
		import('ORG.Util.Page');// 导入分页类
		if(isset($_GET['mid'])){
			$where=' and `state`="'.$this->_get('mid').'"';
		}else{
			$where=' and `state`<2';
		}
		$count      = M('instation')->where('`sid`="'.$this->_session('user_uid').'"'.$where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$all=$this->silReceipt($this->_session('user_uid'),$this->_get('mid'),$Page->firstRow.','.$Page->listRows);
		$this->assign('all',$all);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	
	//站内信显示
	public function standLetter(){
		$id=$this->_post('id');
		echo $this->singleReceipt($id);
	}
	
	//站内信删除
	public function stationexit(){
		$Instation=M('instation');
		$id=$this->_get('id');
		$Instation->where('`id`="'.$id.'"')->setField('state',2);
		$this->success("删除成功");
	}
	
	//站内信还原
	public function reduction(){
		$Instation=M('instation');
		$id=$this->_get('id');
		$Instation->where('`id`="'.$id.'"')->setField('state',1);
		$this->success("还原成功");
	}

//安全中心
	public function security(){
		$this->homeVerify();
		$active['center']='active';
		$this->assign('active',$active);
		$this->assign('mid',$this->_get('mid'));
		$userinfo=M('user');
		$email=$userinfo->field('email')->where('id="'.$this->_session('user_uid').'"')->find();
		$this->assign('email',$email);
		$cache = cache(array('expire'=>50));
		
		if($this->_get('mid')=='Rpasswordpag'){	//找回密码修改
		$value = $cache->get('pawss'.$this->_session('user_uid'));  // 获取缓存
			if(!md5($email['email'])==$value){	//判断链接是否过期
				$this->error("链接已过期！","__ROOT__/Logo/login.html");
			}
		}
		if($this->_get('mid')=='dealRpasswordpag'){	//找回密码修改
		$value = $cache->get('dealpawss'.$this->_session('user_uid'));  // 获取缓存
			if(!md5($email['email'])==$value){	//判断链接是否过期
				$this->error("链接已过期！","__ROOT__/Logo/login.html");
			}
		}
		//print_r($value);
		//$cache->rm('pawss'.$this->_session('user_uid'));
		//exit;
		$active['center']='active';
		$this->assign('active',$active);
		$this->display();
    }

	//修改密码
	public function updaPass(){
		$user=D('User');
		$users=$user->where('id="'.$this->_session('user_uid').'"')->find();
		if($user->create()){
			if($user->userMd5($this->_post('passwd'))==$users['password']){
				$result = $user->where(array('id'=>$this->_session('user_uid')))->save();
				if($result){
				 $this->success("密码重置成功","__ROOT__/Center/security/password.html");
				}else{
				$this->error("新密码不要和原始密码相同！");
				}		
			}else{
				$this->error("原始密码错误！");
			}
		}else{
			$this->error($user->getError());
		}

	}

	//修改交易密码
	public function updaPayPass(){
		$this->homeVerify();
		$user=D('User');
		$users=$user->where('id="'.$this->_session('user_uid').'"')->find();
		if($user->create()){
			if($user->userPayMd5($this->_post('pay_pasd'))==$users['pay_password']){
				$result = $user->where(array('id'=>$this->_session('user_uid')))->save();
				if($result){
				 $this->success("交易密码重置成功","__ROOT__/Center/security/tpassword.html");
				}else{
				$this->error("新交易密码不要和原始交易密码相同！");
				}		
			}else{
				$this->error("原始交易密码错误！");
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
		$head=$this->headPortrait('./Public/uploadify/uploads/portrait/big_user_'.$this->_session('user_uid').'.jpg');
		$this->assign('heads',$head);
		
		if($_POST['but']==1){
			$info=$this->upload('portrait');
			import('ORG.Util.Image.ThinkImage');
			$img =new ThinkImage();
			$img->open($info[0]['savepath'].$info[0]['savename'])->crop($img->width(), $img->height(), 0, 0,200,200)->save($info[0]['savepath'].'big_user_'.$this->_session('user_uid').'.'.$info[0]['extension']);
			if(file_exists($info[0]['savepath'].$info[0]['savename'])){
				unlink($info[0]['savepath'].$info[0]['savename']);
			}
			$this->success("上传成功","__ROOT__/Center/portrait.html");
		}else{
		$this->display();
		}
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
		if($this->_get('export')==1){//导出
			$this->exportWord('agreement');
		}
    }
	
	
}
