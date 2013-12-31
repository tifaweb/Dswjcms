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
		$list=R('dswjjd://Sharing/user_details');	
		//number_format($array['wmoweeks'],2,'.',',')
		$this->assign('list',$list);
		$msgTools = A('msg','Event');
		$msgCount = $msgTools->msgCount($this->_session('user_name'));
		$this->assign('msgCount',$msgCount);
		//最近待收
		$collection=D('Collection');
		$coll=$collection->relation(true)->where('uid='.$this->_session('user_uid').' and type=0')->order('time ASC')->find();	//获取最近的一条待收
		$this->assign('coll',$coll);
		$refund=D('Refund');
		$ref=$refund->relation(true)->where('uid='.$this->_session('user_uid').' and type=0')->order('time ASC')->find();	//获取最近的一条待收
		$this->assign('ref',$ref);
		//逾期的借款
		$overdue=M('overdue');
		$overd=$overdue->where('type!=1')->count();
		$this->assign('overd',$overd);
		//还款提前提醒
		$system=$this->systems();
		$time=strtotime("+$system[sys_refundDue] day");	//提前提醒设置的时间
		$refun=$refund->relation(true)->where('uid='.$this->_session('user_uid').' and type=0 and time<='.$time)->select();
		$count=$refund->relation(true)->where('uid='.$this->_session('user_uid').' and type=0 and time<='.$time)->count();//总数
		if($refun){	//判断是否有符全提前提醒还款的条件
			$this->assign('refun',$refun);
			$this->assign('count',$count);
		}
		$head=$this->headPortrait('./Public/FaustCplus/php/img/big_user_'.$this->_session('user_uid').'.jpg');
		$this->assign('heads',$head);
		$this->display();
    }
//我是投资者
	public function invest(){
		$this->homeVerify();
		$refund=M('collection');
		$automatic=D('Automatic');
		$this->assign('mid',$this->_get('mid'));
		$isflo=R('dswjjd://Sharing/bidRecords',array(15,0,$this->_session('user_uid')));
		if($isflo){	//筛选需要显示正在流转的标
			foreach($isflo as $id=>$is){
				$bid=$is['actionname']['bid'];
				if(!empty($arr[$bid])){
					$imd=$arr[$bid]+1;
					
				}else{
					$imd=1;
				}
				$arr[$bid]++;
					$count=$refund->where('nper='.$imd.' and bid='.$bid.' and uid='.$this->_session('user_uid').' and type=0')->count();
					if($count>0){
						$isflow[$id]['bid']=$bid;
						$isflow[$id]['id']=$imd;
						$isflow[$id]['title']=$is['details']['title'];
						$isflow[$id]['rates']=$is['details']['rates'];
						$isflow[$id]['code']=$is['details']['code'];
						$isflow[$id]['type_name']=$is['details']['type_name'];
						$isflow[$id]['operation']=$is['actionname']['operation'];
						$isflow[$id]['deadline']=$is['actionname']['deadline'];
					}
			}
		}
		unset($arr);
		unset($isflo);
		$isbid=R('dswjjd://Sharing/bidRecords',array(3,0,$this->_session('user_uid')));	
		$this->assign('isbid',$isbid);
		$isbids=R('dswjjd://Sharing/bidRecords',array(15,0,$this->_session('user_uid')));	
		if($isbids){	//筛选需要显示流转的标
			foreach($isbids as $id=>$is){
				$bid=$is['actionname']['bid'];
				if(!empty($arr[$bid])){
					$imd=$arr[$bid]+1;
					
				}else{
					$imd=1;
				}
				$arr[$bid]++;
				$count=$refund->where('nper='.$imd.' and bid='.$bid.' and uid='.$this->_session('user_uid').' and type=0')->count();
						$isflows[$id]['bid']=$bid;
						$isflows[$id]['id']=$imd;
						$isflows[$id]['count']=$count>0?"流转中":"已还款";
						$isflows[$id]['title']=$is['details']['title'];
						$isflows[$id]['rates']=$is['details']['rates'];
						$isflows[$id]['code']=$is['details']['code'];
						$isflows[$id]['type_name']=$is['details']['type_name'];
						$isflows[$id]['operation']=$is['actionname']['operation'];
						$isflows[$id]['deadline']=$is['actionname']['deadline'];
						
			}
		}
		unset($arr);
		unset($isbids);

		$this->assign('isbids',$isflows);
		$isclosed=R('dswjjd://Sharing/bidRecords',array(7,0,$this->_session('user_uid'),1));
		$win=R('dswjjd://Sharing/bidRecords',array(9,0,$this->_session('user_uid'),1));
		$this->assign('win',$win);
		$overdue=$this->overdue($this->_session('user_uid'));//逾期信息
		$this->assign('overdue',$overdue);
		$uncollected=R('dswjjd://Sharing/bidRecords',array(11,0,$this->_session('user_uid'),1));
		$assure=R('dswjjd://Sharing/bidRecords',array(13,0,$this->_session('user_uid'),1));
		//自动投标
		$linkage=$this->borrowLinkage();
		$auto=$automatic->where('uid='.$this->_session('user_uid'))->select();//自动投标
		$this->assign('linkage',$linkage);
		$this->assign('auto',$auto);
		if($this->_get('f')){	//编辑自动投标
			$aut=reset($automatic->where('id='.$this->_get('f'))->select());
			$approve=explode(",",$aut['approve']);
			$financial=explode(",",$aut['financial']);
			if($aut['candra']==1){
				$aut['deadline']=explode(",",$aut['deadline']);
			 }else if($aut['candra']==2){
				$aut['deadline_m']=explode(",",$aut['deadline']);
			 }
			unset($aut['approve']);
			unset($aut['financial']);
			foreach($approve as $ap){
				switch($ap){
					case 1:
					$aut['ap1']=$ap;
					break;
					case 2:
					$aut['ap2']=$ap;
					break;
					case 3:
					$aut['ap3']=$ap;
					break;
					case 4:
					$aut['ap4']=$ap;
					break;
					case 5:
					$aut['ap5']=$ap;
					break;
				}
				
			}
			foreach($financial as $ap){
				switch($ap){
					case 1:
					$aut['fi1']=$ap;
					break;
					case 2:
					$aut['fi2']=$ap;
					break;
					case 3:
					$aut['fi3']=$ap;
					break;
					case 4:
					$aut['fi4']=$ap;
					break;
					case 5:
					$aut['fi5']=$ap;
					break;
					case 6:
					$aut['fi6']=$ap;
					break;
					case 7:
					$aut['fi7']=$ap;
					break;
				}
				
			}
			$this->assign('aut',$aut);
		}
		
		$this->assign('isclosed',$isclosed);
		$this->assign('isflow',$isflow);
		$this->assign('assure',$assure);
		
		if($this->_get('bid') && $this->_get('mid')=='plan'){	//还款计划
			if($this->_get('nper')>0){//如果有nper说明是流转标，流转标只显示对应期数
				$refun=$refund->where('bid='.$this->_get('bid').' and nper='.$this->_get('nper').' and uid='.$this->_session('user_uid'))->order('time ASC')->select();
			}else{
				$refun=$refund->where('bid='.$this->_get('bid').' and uid='.$this->_session('user_uid'))->order('time ASC')->select();
			}
			$this->assign('refun',$refun);
		}
		$active['center']='active';
		$this->assign('active',$active);
		$this->display();
    }
	
	//添加自动投标
	public function investadd(){
		$this->homeVerify();
		$model=D('Automatic');
		$money=M('money');
		$mone=$money->where(array('uid'=>$this->_session('user_uid')))->find();	//资金表
		if($add=$model->create()){
			if($add['total']>$mone['available_funds']){	//如果自动投标金额比可用资金大
				$this->error("自动投标金额超出可用金额！");
			}
			 $add['uid']=$this->_session('user_uid');
			 if($this->_post('plan')==1){
				if(!$this->_post('money')){
					$this->error("每次投标金额不能为空！");
				}
			 }
			 if($this->_post('candra')==1){
				$add['deadline']=implode(",",$this->_post('deadline'));
			 }else if($this->_post('candra')==2){
				$add['deadline']=implode(",",$this->_post('deadline_m'));
			 }
			 $add['approve']=implode(",",$this->_post('approve'));
			 $add['financial']=implode(",",$this->_post('financial'));
			 $add['time']=time();
			 $result = $model->add($add);
			if($result){
				$msave['available_funds']=$mone['available_funds']-$add['total'];	//可用资金
				$msave['freeze_funds']=$mone['freeze_funds']+$add['total'];	//冻结资金
				$money->where(array('uid'=>$this->_session('user_uid')))->save($msave);	//更新资金表
				//记录添加点
				$userLog=$this->userLog('设置自动投标');//会员记录
				$moneyLog=$this->moneyLog(array(0,'设置自动投标',$add['total'],'平台',$mone['total_money'],$msave['available_funds'],$msave['freeze_funds']));	//资金记录
				
				$this->success("自动投标设置成功",'__ROOT__/Center/invest/automaticlist.html');			
			}else{
				 $this->error("自动投标设置失败");
			}	
		}else{
			$this->error($model->getError());
			
		}
		
    }
	
	//更新自动投标
	public function investupd(){
		$this->homeVerify();
		$model=D('Automatic');
		$money=M('money');
		$mone=$money->where(array('uid'=>$this->_session('user_uid')))->find();	//资金表
		$automatic=$model->where(array('id'=>$this->_post('id')))->find();	//自动投标表
		if($add=$model->create()){
			if($add['total']>$mone['available_funds']){	//如果自动投标金额比可用资金大
				$this->error("自动投标金额超出可用金额！");
			}
			 $add['uid']=$this->_session('user_uid');
			 if($this->_post('plan')==1){
				if(!$this->_post('money')){
					$this->error("每次投标金额不能为空！");
				}
			 }
			 if($this->_post('candra')==1){
				$add['deadline']=implode(",",$this->_post('deadline'));
			 }else if($this->_post('candra')==2){
				$add['deadline']=implode(",",$this->_post('deadline_m'));
			 }
			 $add['approve']=implode(",",$this->_post('approve'));
			 $add['financial']=implode(",",$this->_post('financial'));
			 if(!$this->_post('type')){
				$add['type']=0;	
			 }
			 $result = $model->where(array('id'=>$this->_post('id')))->save($add);
			if($result){
				if($add['total']==$automatic['total']){	//如果资金没有变就不执行
				}else{
					$poor=$add['total']-$automatic['total'];	//新设置的金额和原有的金额差
					$msave['available_funds']=$mone['available_funds']-$poor;	//可用资金
					$msave['freeze_funds']=$mone['freeze_funds']+$poor;	//冻结资金
					$money->where(array('uid'=>$this->_session('user_uid')))->save($msave);	//更新资金表
					//记录添加点
					$moneyLog=$this->moneyLog(array(0,'更新自动投标',(int)$poor,'平台',$mone['total_money'],$msave['available_funds'],$msave['freeze_funds']));	//资金记录
				}
				$this->success("自动投标更新成功",'__ROOT__/Center/invest/automaticlist.html');			
			}else{
				 $this->error("自动投标更新失败");
			}	
		}else{
			$this->error($model->getError());
			
		}
    }
	
	//删除自动投标
	public function investexit(){
		$this->homeVerify();
		$model=D('Automatic');
		$money=M('money');
		$mone=$money->where(array('uid'=>$this->_session('user_uid')))->find();	//资金表
		$automatic=$model->where(array('id'=>$this->_get('id')))->find();	//自动投标表
		$result = $model->where(array('id'=>$this->_get('id')))->delete();
		if($result){
			$msave['available_funds']=$mone['available_funds']+$automatic['total'];	//可用资金
			$msave['freeze_funds']=$mone['freeze_funds']-$automatic['total'];	//冻结资金
			$money->where(array('uid'=>$this->_session('user_uid')))->save($msave);	//更新资金表
			//记录添加点
			$moneyLog=$this->moneyLog(array(0,'删除自动投标返回资金',(int)$poor,'平台',$mone['total_money'],$msave['available_funds'],$msave['freeze_funds']));	//资金记录
			 $this->success("删除成功");
				
		}else{
			$this->error($n."删除失败");
		}			
	}
//我是借款者	
	public function loan(){
		$this->homeVerify();
		$this->assign('mid',$this->_get('mid'));
		$list=R('dswjjd://Sharing/borrowUidUnicom',array('uid'=>$this->_session('user_uid')));
		$list=$this->borrowUidUnicom($this->_session('user_uid'));
		$refund=M('refund');
		if($this->_get('bid') && $this->_get('mid')=='plan'){	//还款计划
			$refun=$refund->where('bid='.$this->_get('bid'))->order('time ASC')->select();
			$this->assign('refun',$refun);
		}
		if($this->_get('bid') && $this->_get('mid')=='flowplan'){	//流转标的还款计划
			if($this->_get('nper')>0){//如果有nper说明是流转标，流转标只显示对应期数
				$refun=$refund->where('bid='.$this->_get('bid').' and nper='.$this->_get('nper').' and uid='.$this->_session('user_uid'))->order('time ASC')->select();
			}else{
				$refun=$refund->where('bid='.$this->_get('bid').' and uid='.$this->_session('user_uid'))->order('time ASC')->select();
			}
			
			$this->assign('refun',$refun);
		}
		$this->assign('list',$list);
		$overdue=$this->verdue($this->_session('user_uid'));//逾期信息
		$this->assign('overd',$overdue);
		$active['center']='active';
		$this->assign('active',$active);
		//$isflow=$this->borrowUidUnicom($this->_session('user_uid'),' and type=7');
		//foreach($isflow as $id=>$is){
			//$isflow[$id]['count']=$refund->where('uid='.$this->_session('user_uid').' and type=0 and bid='.$is['id'])->count();
		//}
		
		
		
		$isflo=R('dswjjd://Sharing/bidRecords',array(16,0,$this->_session('user_uid')));
		
		if($isflo){	//筛选需要显示并不重复的正在流转的标
			foreach($isflo as $id=>$is){
				$bid=$is['actionname']['bid'];
				if(!empty($arr[$bid])){
					$imd=$arr[$bid]+1;
					
				}else{
					$imd=1;
				}
				$arr[$bid]++;
					$count=$refund->where('nper='.$imd.' and bid='.$bid.' and uid='.$this->_session('user_uid').' and type=0')->count();
					if($count>0){
						$isflow[$id]['bid']=$bid;
						$isflow[$id]['id']=$imd;
						$isflow[$id]['title']=$is['details']['title'];
						$isflow[$id]['rates']=$is['details']['rates'];
						$isflow[$id]['code']=$is['details']['code'];
						$isflow[$id]['type_name']=$is['details']['type_name'];
						$isflow[$id]['operation']=$is['actionname']['operation'];
						$isflow[$id]['deadline']=$is['actionname']['deadline'];
					}
			}
		}
		unset($arr);
		unset($isflo);	
		$this->assign('isflow',$isflow);
		$this->display();
    }
//资金管理	
	public function fund(){
		$this->homeVerify();
		$this->assign('mid',$this->_get('mid'));
		$unite=M('unite');
		$money=M('money');
		$withdrawal=D('Withdrawal');
		$userinfo=D('Userinfo');
		//银行账号
		$available_funds=$money->where('`uid`='.$this->_session('user_uid'))->getField('available_funds');
		$userinfos = reset($userinfo->relation(true)->field('uid,name,bank,bank_name,bank_account')->where("`uid`=".$this->_session('user_uid'))->select());	
		$userinfos['available_funds']=$available_funds;
		$list=$unite->field('name,value')->where('`state`=0 and `pid`=14')->order('`order` asc,`id` asc')->select();
		foreach($list as $lt){
			if($lt['value']==$userinfos['bank']){
				$userinfos['banks']=$lt['name'];
				break;
			}
		}
		$this->assign('list',$list);
		$this->assign('userinfos',$userinfos);
		//提现记录
		$withuser=R('dswjjd://Sharing/showUser',array('',$this->_session('user_uid')));
		foreach($withuser as $show){
			$withusers['money']+=$show['money'];	//提现总金额
			if($show['type']==2){
				$withusers['poundage']+=$show['withdrawal_poundage'];	//总手续费
				$withusers['account']+=$show['account'];	//到账总金额
			}
		}
		$this->assign('withusers',$withusers);
		$this->assign('withuser',$withuser);
		//账户充值
		$audit=R('dswjjd://Sharing/offlineBank');
		$this->assign('audit',$audit);
		//充值记录
		$showuser=R('dswjjd://Sharing/rechargeUser',array('',$this->_session('user_uid')));
		foreach($showuser as $show){
			$showusers['money']+=$show['money'];	//提现总金额
			if($show['type']==2){
				$showusers['poundage']+=$show['poundage'];	//总手续费
				$showusers['account']+=$show['account_money'];	//到账总金额
			}
		}
		$this->assign('showusers',$showusers);
		$this->assign('showuser',$showuser);
		//资金记录
		$money=M('money');
		$moneys=reset($money->where('`uid`='.$this->_session('user_uid'))->select());//资金
		$record=$this->moneyRecord($this->_session('user_uid'));
		$this->assign('record',$record);
		$this->assign('money',$moneys);
		$active['center']='active';
		$this->assign('active',$active);
		$this->display();
    }
	
//提现申请	
	public function drawUpda(){
		$this->homeVerify();
		$withdrawal=D('Withdrawal');
		$user=D('User');
		$money=M('money');
		$userinfo=M('userinfo');
		$message=reset($userinfo->field('certification,bank,bank_name,bank_account')->where('`uid`='.$this->_session('user_uid'))->select());//获取姓名、银行帐号信息用来判断
		if($message['certification']!=='2'){
			$this->error("请先通过实名认证",'__ROOT__/Center/approve/autonym.html');
		}
		if(!$message['bank'] || !$message['bank_name'] || !$message['bank_account'] ){
			$this->error("请先填写银行账户",'__ROOT__/Center/fund/bank.html');
		}
		$moneys=reset($money->field('total_money,available_funds,freeze_funds')->where('`uid`='.$this->_session('user_uid'))->select());
		$pay_password=$user->where('`id`='.$this->_session('user_uid'))->getField('pay_password');
		if($user->userPayMd5($this->_post('password'))==$pay_password){	//验证支付密码
			if($this->_post('money')<=$moneys['available_funds']){	//提现金额必须小于可用余额
				if($create=$withdrawal->create()){
					$create['time']=time();
					$result = $withdrawal->add($create);
					if($result){
						$moneyarr['available_funds']=$moneys['available_funds']-$create['money'];
						$moneyarr['freeze_funds']=$moneys['freeze_funds']+$create['money'];
						$money->where(array('uid'=>$this->_session('user_uid')))->save($moneyarr);
						$this->moneyLog(array(0,'提现申请',$this->_post('money'),'平台',$moneys['total_money'],$moneyarr['available_funds'],$moneyarr['freeze_funds']));	//资金记录
						$this->success("提现申请成功",'__ROOT__/Center/fund/drawrecord.html');
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
		
		$moneys=reset($money->field('total_money,available_funds,freeze_funds')->where('`uid`='.$this->_session('user_uid'))->select());
		if($create=$withdrawal->create()){
			$result = $withdrawal->where('id='.$id)->save();	//改变提现状态
			if($result){
				$moneyarr['available_funds']=$moneys['available_funds']+$create['money'];
				$moneyarr['freeze_funds']=$moneys['freeze_funds']-$create['money'];
				$money->where(array('uid'=>$this->_session('user_uid')))->save($moneyarr);
				$this->moneyLog(array(0,'提现销销',$this->_post('money'),'平台',$moneys['total_money'],$moneyarr['available_funds'],$moneyarr['freeze_funds']));	//资金记录
				$this->success("提现撤销成功",'__ROOT__/Center/fund/drawrecord.html');
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
			 	$create['nid']				=R('dswjjd://Sharing/orderNumber');	//订单号
				$create['uid']				=$this->_session('user_uid');	//用户ID
				$create['poundage']			=R('dswjjd://Sharing/topUpFees',array($create['money']));//充值手续费
				$create['account_money']	=$create['money']-$create['poundage'];//到帐金额
				$create['time']				=time();
				$create['type']				=1;
				if($this->_post('way')==0){
					$create['genre']				=0;		//线下充值
				}else{	//网上充值
					if($this->_post('oid')==1){	//支付宝
						$create['genre']				=1;	
					}
				}
				$result = $recharge->add($create);
			if($result){
				$this->success("充值已提交","__ROOT__/Center/fund/injectrecord.html");			
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
		$unite=M('unite');
		$userinfo=M('userinfo');
		$list=$unite->field('name,value')->where('`state`=0 and `pid`=13')->order('`order` asc,`id` asc')->select();
		$this->assign('list',$list);
		$user_details=R('dswjjd://Sharing/user_details');
		$certification=$user_details[0][certification];
		$this->assign('user_details',$user_details);
		$userfo=$userinfo->field('qq,certification')->where('uid='.$this->_session('user_uid'))->find();
		
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
		//VIP
		$vip_points=M('vip_points');
		$vip_points=$vip_points->field('audit')->where(array('uid'=>$this->_session('user_uid')))->find();
		$this->assign('vip_points',$vip_points['audit']);
		$systems=$this->systems();
		$this->assign('systems',$systems);
		$endjs='
			/*
			*年月切换 
			*宁波开发网络科技有限公司
			*id		站内信ID
			*/
			 function vipmod(id){	
			 var length=$("#length").val();	//值
			 if(id){
				 $(".approve_vip a").removeClass("hove");
				 $("#vip_"+id).addClass("hove");
				 $("#mod").val(id);
			 }
			 	if(id==1){
					$("#year").html("月");
				}else if(id==2){
					$("#year").html("年");
				}
				var id=$("#mod").val();	//付费模式
				$(".approve_with").load("__URL__/vipAjax", {length:length,mod:id});
			 }
		';
		$this->assign('endjs',$endjs);
		$active['center']='active';
		$this->assign('active',$active);
		$this->display();
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
	
	//VIP显示费用
	public function vipAjax(){
		$this->homeVerify();
		$systems=$this->systems();
		if($this->_post('mod')==1){	//月
			$cost=$this->_post('length')*$systems['sys_vipm'];
		}else{
			$cost=$this->_post('length')*$systems['sys_vipy'];
		}
		echo $cost.'<input name="price" type="hidden" value="'.$cost.'" />';
	}
	
	//申请VIP
	public function updaVip(){
		$this->homeVerify();
		$vip_points=M('vip_points');
		
		$money=M('money');
		$available_funds=$money->field('available_funds')->where('uid='.$this->_session('user_uid'))->find();	//可用余额
		if($available_funds['available_funds']<$this->_post('price')){
			$this->error("账户可用余额不足以开通VIP！",'__ROOT__/Center/fund/inject.html');
		}
		$save['audit']=1;
		$save['checktime']=time();
		$save['deadline']=$this->_post('length');
		$save['unit']=$this->_post('mod')==2?0:$this->_post('mod');
		$result = $vip_points->where(array('uid'=>$this->_session('user_uid')))->save($save);
		if($result){
			$this->userLog('申请VIP');//会员记录
			$this->success("申请成功");
		}else{
			$this->error("申请失败");
		}			 			
	}
	
	//邮箱验证
	public function emailVerify(){
		$this->homeVerify();
		$userinfo=M('user');
		$smtp=M('smtp');
		$stmpArr=$smtp->find();
		$getfield = $userinfo->where("`id`=".$this->_session('user_uid')." and `email`='".$this->_post('email')."'")->find();
		if(!$getfield){		
			if($userinfo->create()){
				$result = $userinfo->where('`id`='.$this->_session('user_uid'))->save();
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
											<p>欢迎加入<strong>点石为金</strong>！请点击下面的链接来认证您的邮箱。</p>
											<p><a href="http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Center/emailVerifyConfirm/'.base64_encode($this->_session('user_uid')).'">http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Center/emailVerifyConfirm/'.base64_encode($this->_session('user_uid')).'</a></p>
											<p>如果您的邮箱不支持链接点击，请将以上链接地址拷贝到你的浏览器地址栏中认证。</p>
										</div>
										<div style="color: #999;">
											<p>发件时间：'.date('Y/m/d H:i:s').'</p>
											<p>此邮件为系统自动发出的，请勿直接回复。</p>
										</div>';
		$emailsend=R('dswjjd://Sharing/email_send',array($stmpArr));	
		if($emailsend){
			$this->success('邮件发送成功', '__ROOT__/Center/approve/email.html');
		}else{
			$this->error("邮箱激活失败，请联系管理员");
		}	
	}
	//邮箱验证确认
	public function emailVerifyConfirm(){
		//$this->homeVerify();
		$msgTools = A('msg','Event');
		$userinfo=M('userinfo');
		$username=base64_decode($this->_get('email_audit'));
			$emailVerifyConfirm['email_audit']=$username?2:0;
			$result = $userinfo->where("`uid`= ".$username)->save($emailVerifyConfirm);
			if($result){
			//记录添加点
			$sendMsg=$msgTools->sendMsg(3,'用户通过邮箱验证','用户通过邮箱验证','admin',$this->_session('user_uid'));//站内信
			$this->userLog('通过邮箱验证');//前台操作
			$arr['member']=array('uid'=>$this->_session('user_uid'),'name'=>'mem_email_audit');
			$vip_points=M('vip_points');	
			$vips=$vip_points->where('uid='.$this->_session('user_uid'))->find();
			if($vips['audit']==2){	//判断是不是开通了VIP
				$arr['vip']=array('uid'=>$this->_post('uid'),'name'=>'vip_email_audit');
			}
			$userss=M('user');
			$promotes=$userss->where('id='.$this->_session('user_uid'))->find();
			if($promotes['uid']){	//判断是不是有上线
				$arr['vip']=array('uid'=>$promotes['uid'],'name'=>'pro_email_audit');
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
		$emailsend=R('dswjjd://Sharing/email_send',array($stmpArr));	
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
		$emailsend=R('dswjjd://Sharing/email_send',array($stmpArr));	
		if($emailsend){
			$this->success('邮件发送成功', '__ROOT__/Center/security/dealRpassword.html');
		}else{
			$this->error("邮箱找回密码失败，请联系管理员");
		}	
	}
	
	//邮箱找回交易密码提交
	public function dealemailPasssubmit(){
		$user=D('User');
		$users=$user->where('id='.$this->_session('user_uid'))->find();
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
		$list=$unite->field('pid,name,value')->where('`state`=0 and `pid`=8 or `pid`=9 or `pid`=10 or `pid`=11 or `pid`=12')->order('`order` asc,`id` asc')->select();
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
		$city	=	M('city');
		$city=$city->select();
		foreach($city as $cy){
			$citys[$cy['var']]=$cy[city];
		}
		$userinfo=M('userinfo');
		$userinfo=$userinfo->field('location,marriage,education,monthly_income,housing,buy_cars,qq,fixed_line,industry,company')->where('`uid`='.$this->_session('user_uid'))->order('`id` asc')->select();		
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
		//站内信
		$endjs='
			/*
			*单条站内信显示AJAX 
			*shop猫
			*id		站内信ID
			*/
			 function msgcont(id){	
			 	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
				$("#basic_content").html(loading);	
				$("#basic_content").load("__URL__/stationview", {id:id});
			 }
		';
		$this->assign('endjs',$endjs);
		$msgTools = A('msg','Event');
		$msgCount = $msgTools->msgCount($this->_session('user_name'));
		if($this->_get('pid')=='unread'){	//未读
			$msgInfo = $msgTools->msgInfo($this->_session('user_name'),'unread');
		}else if($this->_get('pid')=='read'){	//已读
			$msgInfo = $msgTools->msgInfo($this->_session('user_name'),'read');
		}else if($this->_get('pid')=='inbox'){	//收件箱
			$msgInfo = $msgTools->msgInfo($this->_session('user_name'),'inbox');
		}else{	//全部
			$msgInfo = $msgTools->msgInfo($this->_session('user_name'));
			$msgInfo = $msgInfo['outbox'];
		}
		$this->assign('msgInfo',$msgInfo);	
        $msgTools->msgPage($this->_session('user_name'));
		$this->assign('msgCount',$msgCount);		
		$this->assign('mid',$this->_get('mid'));
		$active['center']='active';
		$this->assign('active',$active);
		//邀请好友
		$lsuid=$_SERVER['HTTP_HOST']."/Logo/register.html?".base64_encode("lsuid=".$this->_session('user_uid'));
		$this->assign('lsuid',$lsuid);
		$this->display();
    }
	
	//查看站内信
	public function stationview(){
		$this->homeVerify();
		$msgTools = A('msg','Event');
		$instation=M('instation');
		$msgCount = $msgTools->msgSingle($this->_post('id'));
		if($msgCount){
			$instation->where('id='.$this->_post('id'))->save(array("rd"=>1));
		}
		$count.='<div class="basic_single">
				<h5>'.$msgCount[0]['title'].'</h5>
				<div>发件人：'.$msgCount[0]['hostname'].'</div>
				<div>发件时间：'.date('Y-m-d H:i:s',$msgCount[0]['addline']).'</div>
				<div>'.$msgCount[0]['msg'].'</div>
				<form class="form-horizontal" method="post" action="'.__ROOT__.'/Center/stationreply.html">
				<input name="id" type="hidden" value="'.$this->_post('id').'" />
				<input name="title" type="hidden" value="对'.$msgCount[0]['title'].'的回复" />
				';
				//if($msgCount[0]['hostname']=='admin'){	//系统信息不能回复
					//$count.='<div><a href="#" class="btn disabled reply">回复</a>';
				//}else{
					//$count.='<div><button class="btn btn-primary reply" type="submit">回复</button>';
				//}
				$count.='<a class="btn btn-info" href="'.$_SERVER["HTTP_REFERER"].'">返回</a></button>
				</div>
				</form>
			</div>
		';
		echo $count;
    }
	
	//删除站内信
	public function stationexit(){
		$this->homeVerify();
		$msgTools = A('msg','Event');
		$instation=M('instation');
		$result=$instation->where('id='.$this->_get('id'))->delete();
		if($result){
			 $this->success("删除成功");
				
		}else{
			$this->error("删除失败");
		}			 
    }
	
	//回复
	/*
	public function stationreply(){
		$msgTools = A('msg','Event');
		$msgCount = $msgTools->reply($this->_post('id'),$this->_post('title'),$this->_post('msg'));
		if($msgCount){
			$this->success("回复成功",'__URL__/basic/mail?pid=outbox');		
		}else{
			$this->error("回复失败");
		}
    }*/

//安全中心
	public function security(){
		$this->homeVerify();
		$this->assign('mid',$this->_get('mid'));
		$userinfo=M('user');
		$email=$userinfo->field('email')->where('id='.$this->_session('user_uid'))->find();
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
		$users=$user->where('id='.$this->_session('user_uid'))->find();
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
		$users=$user->where('id='.$this->_session('user_uid'))->find();
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
		$head=$this->headPortrait('./Public/FaustCplus/php/img/big_user_'.$this->_session('user_uid').'.jpg');
		$this->assign('heads',$head);
		$this->display();
    }
	public  function test(){
		$this->homeVerify();
		 $tools = A('tools','Event');
		 $tools->aa();
	}
//协议书	
	public function agreement(){
		$this->homeVerify();
		if(!$this->_get('bid')){
			$this->error("操作有误！");
		}
		$refund=M('refund');
		$collection=M('collection');
		$re=$refund->where('uid='.$this->_session('user_uid').' and bid='.$this->_get('bid'))->find();
		$co=$collection->where('uid='.$this->_session('user_uid').' and bid='.$this->_get('bid'))->find();
		if($re || $co){
			$boow=reset($this->borrow_unicom($this->_get('bid')));
			$userinfo=D('Userinfo');
			$userin=$userinfo->field('name,idcard,uid')->relation(true)->where('uid='.$boow['uid'])->find();
			$bid_record=$this->lendUser('3',$this->_get('bid'));
			$this->assign('bid',$bid_record);
			$refun=$refund->where('uid='.$boow['uid'].' and bid='.$this->_get('bid'))->select();
			$this->assign('refun',$refun);
			$this->assign('boow',$boow);
			$this->assign('userin',$userin);
		}else{
			$this->error("操作有误！");
		}
		$this->display();
    }
	
//协议书(流转)	
	public function flowagreement(){
		$this->homeVerify();
		if(!$this->_get('bid')&& !$this->_get('nper')){
			$this->error("操作有误！");
		}
		$refund=D('Refund');
		$collection=D('Collection');
		$re=$refund->where('uid='.$this->_session('user_uid').' and bid='.$this->_get('bid').' and nper='.$this->_get('nper'))->relation(true)->find();
		$co=$collection->where('uid='.$this->_session('user_uid').' and bid='.$this->_get('bid').' and nper='.$this->_get('nper'))->relation(true)->find();
		if($re || $co){
			$boow=reset($this->borrow_unicom($this->_get('bid')));
			$userinfo=D('Userinfo');
			$userin=$userinfo->field('name,idcard,uid')->relation(true)->where('uid='.$boow['uid'])->find();
			$bid_record=$this->lendUser('15',$this->_get('bid'),1);
			//$bid_record['total']=$bid_record['money']-$bid_record['interest'];
			$this->assign('bid',$bid_record);
			$refun=$refund->where('uid='.$boow['uid'].' and bid='.$this->_get('bid'))->order('time ASC ')->select();
			$this->assign('refun',$refun);
			$this->assign('boow',$boow);
			$this->assign('userin',$userin);
		}else{
			$this->error("操作有误！");
		}
		$this->display();
    }
	
	//额度申请
	public function assureUpda(){
		$model=D('Lines');
        if($create=$model->create()){
			$create['uid']=$this->_session('user_uid');
			$create['state']=1;
			$create['time']=time();
			$result=$model->add($create);
			if($result){
				$this->success("额度申请成功");			
			}else{
				 $this->error("额度申请失败");
			}	
		}else{
			$this->error($model->getError());
			
		}
	}
	
//提成下线管理
   public function commision($id){
	   $this->homeVerify();
	  if(!$id){
		  $this->error("请选择用户");
	  }	 
	  $list = M("user_commision")->where("uid=".$id)->find();
	  if(!$list){
		  $this->success('您是普通用户');
	  }
	  $mod = D("Commision");
	  $field = "id,pid,name,concat(catpid,'-',id) as absPath,level,ratio,status,bonus,if_downNode";
	  $order = " absPath,id ";
	  $where['catpid']  = array('like', '%'.$list['group_id'].'%');
      $mylist = $mod->where("id=".$list['group_id'])->find();
	  $group = $mod->field($field)->where($where)->order($order)->select();	
      $mylist['abscatpid'] = $mylist['catpid']."-".$mylist['id'];
	  $mylist['uplevel'] = intval($mylist['level']) +1;

	 $this->assign('list',$list);  
	 $this->assign('mylist',$mylist);  
	 $this->assign('group',$group);
	 $active['center']='active';
	 $this->assign('active',$active);
	 $this->display();		   
	   
	   
   }
	
	//编辑下级分组
	public function editGroup($id=0){
		$this->homeVerify();
		if(!$id){
			$this->error("请选择分组");
		}	
	  $user_id = $_SESSION['admin_uid'] ?$_SESSION['admin_uid'] : 0;
	  $mod = D("Commision");
	  $list = $mod->where("id=".$id)->find();	
	  $field = "id,pid,name,concat(catpid,'-',id) as absPath,level,ratio,bonus,if_downNode";
	  $order = " absPath,id ";
	  $group = $mod->field($field)->order($order)->select();	
	  
	  $this->assign('list',$list);
	  $this->assign('group',$group);
	  $this->assign('user_id',$user_id);
	  $active['center']='active';
	  $this->assign('active',$active);
	  $this->display();		
	}	
	
	//查看用户组下所有用户
	public function viewUser($id=0){
		$this->homeVerify();
		if($id){
			$where= "group_id =".$id;
		}else{
			$this->error("请选择用户组");
		}
		$group = D("Commision")->where("id=".$id)->find();
		$mod = D("user_commision");
		$list = $mod->where($where)->relation("user")->select();
		$this->assign('list',$list);
		$this->assign('id',$id);
		$this->assign('group',$group);
		$active['center']='active';
		$this->assign('active',$active);
        $this->display();
	}	
    //删除分组
	public function delGroup($id=0){
		$this->homeVerify();
		if($id){
			$where= "group_id =".$id;
		}else{
			$this->error("请选择用户组");
		}	
		$where['catpid']  = array('like', '%'.$id.'%');
		$where['id']  = array('eq',$id);
		$where['_logic'] = 'or';
		$mod = D("Commision");
		$moduc = D("user_commision");
		$list = $mod->where($where)->select();
		$delList = array();  //删除id列表
		$upUser = array();//site_add删除id列表
		foreach($list as $k=>$v){
			array_push($delList ,$v['id']);
		}	
		$list2 = $moduc	->where("id in (".$delList.")")->select();
		foreach($list2 as $k=>$v){
			array_push($upUser ,$v['uid']);
		}	

		$ret1 = $mod->where(array('id'=>array('in',$delList)))->delete();		
		$ret2 = $moduc->where(array('group_id'=>array('in',$delList)))->delete();	
		if($ret1 && $ret2){
			$Model = new Model();
			$sql = "update ds_user set uid = 0 where id in(".$upUser.")";
			$Model->execute($sql,false);	
			$this->success('删除成功');
		}elseif($ret1 && !$ret2){
			$this->success('分组删除成功，分组与用户对应关系删除失败，请手动删除');
		}elseif(!$ret1 && $ret2){
			$this->success('，分组与用户对应关系删除成功，分组删除失败，请手动删除');
		}
	}	
	
	
    //设置提成比例
	
	public function setRatio($id = 0){
		$this->homeVerify();
		if(!$id){

			$this->error("请选择用户组");
		}		
		$mod = D("Commision");
		$field = "id,pid,name,concat(catpid,'-',id) as absPath,level,ratio,bonus,if_downNode";
		$order = " absPath,id ";
		$where['catpid']  = array('like', '%'.$id.'%');
		$where['id']  = array('eq',$id);
		$where['_logic'] = 'or';
		$list = $mod->field($field)->where($where)->order($order)->select();
		//pre($list);
        $this->assign('list',$list);
		$active['center']='active';
		$this->assign('active',$active);
		$this->display();
	}	
	
	
}