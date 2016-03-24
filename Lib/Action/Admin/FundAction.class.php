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
class FundAction extends AdminCommAction {
//--------用户资金汇总-----------
    public function summary(){
		if($this->_get('title') || $this->_get('state')){
			$uid=M('user')->field('id')->where('`username`="'.$this->_get('title').'"')->find();
			$uid=$uid['id'];
			$where=$uid?'`uid`="'.$uid.'"':'';
		}
		
		
		import('ORG.Util.Page');// 导入分页类
        $count      = M('money')->where($where)->count();// 查询满足要求的总记录数;
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$money=D('Money')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id')->relation(true)->select();
		$this->assign('money',$money);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	
	
	
	//导出EXCEL(用户资金汇总)
	public function summaryExport(){
		$Money=D('Money');
		$fid=$this->_post('fid');
		if($fid>0){
		$list=$Money->relation(true)->order('`id`  ASC ')->limit($fid.',5000')->select();
		}else{
		$list=$Money->relation(true)->order('`id`  ASC ')->limit(5000)->select();
		}
		$data['title']="用户资金汇总";
		$data['name']=array(
							array('n'=>'ID','u'=>'id'),
							array('n'=>'用户ID','u'=>'uid'),
							array('n'=>'用户名','u'=>'username'),
							array('n'=>'总资金','u'=>'total_money'),
							array('n'=>'可用资金','u'=>'available_funds'),
							array('n'=>'冻结资金','u'=>'freeze_funds'),
							array('n'=>'待收资金','u'=>'due_in'),
							array('n'=>'待还资金','u'=>'stay_still'),
							array('n'=>'待收利息','u'=>'stay_interest'),
							array('n'=>'已赚利息','u'=>'make_interest'),
							array('n'=>'已赚奖励','u'=>'make_reward'),
							array('n'=>'逾期金额','u'=>'overdue')
							);
		foreach($list as $l){
			$content[]=array(
							'id'				=>' '.$l['id'],
							'uid'				=>' '.$l['uid'],
							'username'			=>' '.$l['username'],
							'total_money'		=>' '.$l['total_money'],
							'available_funds'	=>' '.$l['available_funds'],
							'freeze_funds'		=>' '.$l['freeze_funds'],
							'due_in'			=>' '.$l['due_in'],
							'stay_still'		=>' '.$l['stay_still'],
							'stay_interest'		=>' '.$l['stay_interest'],
							'make_interest'		=>' '.$l['make_interest'],
							'make_reward'		=>' '.$l['make_reward'],
							'overdue'			=>' '.$l['overdue']
							);
		}
		$data['content']=$content;
		$excel=$this->excelExport($data);
		$this->Record('用户资金汇总导出成功');//后台操作
		$this->success("导出成功","__APP__/TIFAWEB_DSWJCMS/Fund/summary.html");
		
	}
//--------充值-----------
    public function recharge(){
		if($this->_get('title')){
			$uid=M('user')->field('id')->where('`username`="'.$this->_get('title').'"')->find();
			$uid=$uid['id'];
			$where=$uid?" or `uid`=".$uid:'';
			$where.="(`nid`=".$this->_get('title')." or `number`=".$this->_get('title').$uid.")";
		}
		if(is_numeric($this->_get('genre'))){
			if($this->_get('genre')>0){
				$where.=" and `genre`>0";
			}else{
				$where.=" and `genre`=".$this->_get('genre');
			}
		}
		
		if(is_numeric($this->_get('type'))){
			$where.=" and `type`=".$this->_get('type');
		}
		
		if($this->_get('starttime')>0){
			$starttime=strtotime($this->_get('starttime'));
			$starttime=" and `time`>='".$starttime."'";
		}
		if($this->_get('endtime')>0){
			$endtime=strtotime($this->_get('endtime'));
			$endtime=" and `time`<='".$endtime."'";
		}
		$where.=$starttime.$endtime;
		
		$where=trim($where,' and ');
		
		import('ORG.Util.Page');// 导入分页类
        $count      = M('recharge')->where($where)->count();// 查询满足要求的总记录数;
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$recharges=$this->rechargeUser('','',$where,$Page->firstRow.','.$Page->listRows);
		$this->assign('list',$recharges);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
    }
	//充值审核
	public function rechUpda(){
		$recharge=D('Recharge');
		if($create=$recharge->create()){
			$create['handlers']				=$this->_session('admin_name');
			$create['audittime']			=time();
			$result = $recharge->where(array('id'=>$this->_post('id')))->save($create);
			if($result){
				$money=M('money');
				$withd= $recharge->field('uid,account_money,poundage,money')->where(array('id'=>$this->_post('id')))->find();
				$mon=$money->field('total_money,available_funds,freeze_funds')->where(array('uid'=>$withd['uid']))->find();
				if($this->_post('type')==2){	//审核通过
					$array['total_money']				=$mon['total_money']+$withd['account_money'];
					$array['available_funds']			=$mon['available_funds']+$withd['account_money'];	
					//记录添加点
					$money->where(array('uid'=>$withd['uid']))->save($array);
					$sendMsg=$this->silSingle(array('title'=>'充值成功','sid'=>$withd['uid'],'msg'=>'充值成功，帐户增加'.$withd['account_money'].'元'));//站内信
					$this->moneyLog(array(0,'充值成功',$withd['money'],'平台',$array['total_money']+$withd['poundage'],$array['available_funds']+$withd['poundage'],$mon['freeze_funds'],$withd['uid']),3);	//资金记录
					$this->moneyLog(array(0,'充值手续费扣除',$withd['poundage'],'平台',$array['total_money'],$array['available_funds'],$mon['freeze_funds'],$withd['uid']),11);	//资金记录
				}else{	//失败
					//记录添加点
					$sendMsg=$this->silSingle(array('title'=>'充值失败','sid'=>$withd['uid'],'msg'=>'订单号为'.$withd['nid'].'的充值申请被撤回'));//站内信
				}
				$this->Record('充值审核成功');//后台操作
				$this->success("充值审核成功","__APP__/TIFAWEB_DSWJCMS/Fund/recharge");
			}else{
				$sendMsg=$this->silSingle(array('title'=>'充值失败','sid'=>$withd['uid'],'msg'=>'充值失败，流水号有误'));//站内信
				$this->Record('充值审核失败');//后台操作
			$this->error("充值审核失败");
			}		
		}else{
			$this->error($recharge->getError());
		}
    }
	//充值查看页
    public function recharge_page(){
		$recharges=$this->rechargeUser($this->_get('id'));
		$this->assign('list',$recharges);
		$this->display();
    }
//--------提现-----------
    public function withdrawal(){
		if($this->_get('title')){
			
			$uid=M('user')->field('id')->where('`username`="'.$this->_get('title').'"')->find();
			$uid=$uid['id'];
			$where=$uid?"`uid`=".$uid:'';
		}
		if(is_numeric($this->_get('type'))){
			$where.=" and `type`=".$this->_get('type');
		}
		
		if($this->_get('starttime')>0){
			$starttime=strtotime($this->_get('starttime'));
			$starttime=" and `time`>='".$starttime."'";
		}
		if($this->_get('endtime')>0){
			$endtime=strtotime($this->_get('endtime'));
			$endtime=" and `time`<='".$endtime."'";
		}
		$where.=$starttime.$endtime;
		
		$where=trim($where,' and ');
		
		import('ORG.Util.Page');// 导入分页类
        $count      = M('withdrawal')->where($where)->count();// 查询满足要求的总记录数;
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$unites=$this->showUser('','',$where,$Page->firstRow.','.$Page->listRows);
		$this->assign('list',$unites);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
    }
	
	//提现审核
	public function withUpda(){
		if($this->_session('verify') != md5(strtoupper($this->_post('proving')))) {
		   $this->error('验证码错误！');
		}
		$withdrawal=D('Withdrawal');
		if($create=$withdrawal->create()){
			$create['handlers']				=$this->_session('admin_name');
			$create['audittime']			=time();
			$result = $withdrawal->where(array('id'=>$this->_post('id')))->save($create);
			if($result){
				$money=M('money');
				$withd= reset($withdrawal->where(array('id'=>$this->_post('id')))->select());
				$mon=reset($money->field('total_money,available_funds,freeze_funds')->where(array('uid'=>$withd['uid']))->select());
				if($this->_post('type')==2){	//审核通过
					$arr['total_money']=$array['total_money']				=$mon['total_money']-$withd['money'];
					$arr['freeze_funds']=$array['freeze_funds']				=$mon['freeze_funds']-$withd['money'];
					$arr['available_funds']=$mon['available_funds'];
					//记录添加点
					$money->where(array('uid'=>$withd['uid']))->save($array);
					$sendMsg=$this->silSingle(array('title'=>'提现成功','sid'=>$withd['uid'],'msg'=>'提现成功，帐户减少'.$withd['money'].'元'));//站内信
					$this->moneyLog(array(0,'提现成功',$withd['money'],'平台',$arr['total_money'],$arr['available_funds'],$arr['freeze_funds'],$withd['uid']),4);	//资金记录
					$this->moneyLog(array(0,'提现手续费',$withd['withdrawal_poundage'],'平台',$arr['total_money'],$arr['available_funds'],$arr['freeze_funds'],$withd['uid']),13);	//资金记录
					
				}else if($this->_post('type')==3){	//审核不通过
					$arr['total_money']=$mon['total_money'];
					$arr['available_funds']=$array['available_funds']			=$mon['available_funds']+$withd['money'];
					$arr['freeze_funds']=$array['freeze_funds']				=$mon['freeze_funds']-$withd['money'];
					//记录添加点
					$money->where(array('uid'=>$withd['uid']))->save($array);
					$sendMsg=$this->silSingle(array('title'=>'提现失败','sid'=>$withd['uid'],'msg'=>'提现申请被撤回'));//站内信
					$this->moneyLog(array(0,'提现失败',$withd['money'],'平台',$arr['total_money'],$arr['available_funds'],$arr['freeze_funds'],$withd['uid']),12);	//资金记录
				}
				$this->Record('提现审核成功');//后台操作
				$this->success("提现审核成功","__APP__/TIFAWEB_DSWJCMS/Fund/withdrawal");
			}else{
				$sendMsg=$this->silSingle(array('title'=>'提现失败','sid'=>$withd['uid'],'msg'=>'提现失败，银行帐号和户主不统一'));//站内信
				$this->Record('提现审核失败');//后台操作
				$this->error("提现审核失败");
			}		
		}else{
			$this->error($withdrawal->getError());
		}
    }
	
	//提现查看页
    public function withdrawal_page(){
		$unites=$this->showUser($this->_get('id'));
		$this->assign('list',$unites);
		$this->display();
    }
	
//--------资金记录-----------
   public function money(){
	   if($this->_get('uid')>0){
			$where="`uid`=".$this->_get('uid');
		}
	   if($this->_get('title')){
			$uid=M('user')->field('id')->where('`username`="'.$this->_get('title').'"')->find();
			
			$uid=$uid['id'];
			$where=$uid?"`uid`=".$uid:'';
		}
		
		if($this->_get('starttime')>0){
			$starttime=strtotime($this->_get('starttime'));
			$starttime=" and `time`>='".$starttime."'";
		}
		if($this->_get('endtime')>0){
			$endtime=strtotime($this->_get('endtime'));
			$endtime=" and `time`<='".$endtime."'";
		}
		$where.=$starttime.$endtime;
		if($where){
		$where=' and '.trim($where,' and ');
		}
		import('ORG.Util.Page');// 导入分页类
        $count      = M('money_log')->where('type=0'.$where)->count();// 查询满足要求的总记录数;
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$record=D('Money_log')->relation(true)->where('type=0'.$where)->order('time DESC,id DESC ')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('record',$record);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
    }
		
	//导出EXCEL(充值列表)
	public function integExport(){
		$where=$this->_post('type')?"type=".$this->_post('type'):'';
		$fid=$this->_post('fid');
		if($fid>0){
		$list=$this->rechargeUser(0,0,$where,$fid.',5000','`id` ASC');
		}else{
		$list=$this->rechargeUser(0,0,$where,5000,'`id` ASC');
		}
		$data['title']="充值列表";
		$data['name']=array(
							array('n'=>'ID','u'=>'id'),
							array('n'=>'订单号','u'=>'nid'),
							array('n'=>'流水号','u'=>'number'),
							array('n'=>'用户名','u'=>'username'),
							array('n'=>'充值类型','u'=>'genre_name'),
							array('n'=>'充值金额','u'=>'money'),
							array('n'=>'手续费','u'=>'poundage'),
							array('n'=>'到账金额','u'=>'account_money'),
							array('n'=>'状态','u'=>'type')
							);
		foreach($list as $l){
			switch($l['type']){
				case '1':
				$type="充值申请";
				break;
				case '2':
				$type="充值成功";
				break;
				case '3':
				$type="充值失败";
				break;
				case '4':
				$type="撤销充值";
				break;
			}
			$content[]=array(
							'id'				=>' '.$l['id'],
							'nid'				=>' '.$l['nid'],
							'number'			=>' '.$l['number'],
							'username'			=>$l['username'],
							'genre_name'		=>$l['genre_name'],
							'money'				=>$l['money'],
							'poundage'			=>$l['poundage'],
							'account_money'		=>$l['account_money'],
							'type'				=>$type
							);
		}
		$data['content']=$content;
		$excel=$this->excelExport($data);
		$this->Record('充值列表导出成功');//后台操作
			$this->success("导出成功","__APP__/TIFAWEB_DSWJCMS/Fund/entry.html");
		
	}
	
	//导出EXCEL(提现列表)
	public function integExports(){
		$where=$this->_post('type')?"type=".$this->_post('type'):'';
		$fid=$this->_post('fid');
		if($fid>0){
		$list=$this->showUser(0,0,$where,$fid.',5000','`id` ASC');
		}else{
		$list=$this->showUser(0,0,$where,5000,'`id` ASC');
		}
		$data['title']="提现列表";
		$data['name']=array(
							array('n'=>'ID','u'=>'id'),
							array('n'=>'用户名','u'=>'username'),
							array('n'=>'真实姓名','u'=>'name'),
							array('n'=>'提现银行','u'=>'bank'),
							array('n'=>'提现支行','u'=>'bank_name'),
							array('n'=>'提现账户','u'=>'bank_account'),
							array('n'=>'提现金额','u'=>'money'),
							array('n'=>'手续费','u'=>'withdrawal_poundage'),
							array('n'=>'到账金额','u'=>'account'),
							array('n'=>'提现时间','u'=>'time'),
							array('n'=>'状态','u'=>'type')
							);
		foreach($list as $l){
			switch($l['type']){
				case '1':
				$type="提现申请";
				break;
				case '2':
				$type="提现成功";
				break;
				case '3':
				$type="提现失败";
				break;
				case '4':
				$type="撤销提现";
				break;
			}
			$content[]=array(
							'id'				=>' '.$l['id'],
							'username'			=>$l['username'],
							'name'				=>$l['name'],
							'bank'				=>$l['bank'],
							'bank_name'			=>$l['bank_name'],
							'bank_account'		=>" ".$l['bank_account'],
							'money'				=>$l['money'],
							'withdrawal_poundage'=>$l['withdrawal_poundage'],
							'account'			=>$l['account'],
							'time'				=>date('Y-m-d H:i:s',$l['time']),
							'type'				=>$type
							);
		}
		$data['content']=$content;
		$excel=$this->excelExport($data);
		$this->Record('提现列表成功');//后台操作
			$this->success("导出成功","__APP__/TIFAWEB_DSWJCMS/Fund/entry.html");
		
	}
	
	//导出EXCEL(资金记录)
	public function moneyExport(){
		$fid=$this->_post('fid');
		if($fid>0){
		$list=$this->moneyRecord('',$fid.',5000','`id` ASC');
		}else{
		$list=$this->moneyRecord('',5000,'`id` ASC');
		}
		
		$data['title']="充值列表";
		$data['name']=array(
							array('n'=>'ID','u'=>'id'),
							array('n'=>'用户名','u'=>'username'),
							array('n'=>'操作金额','u'=>'operation'),
							array('n'=>'总金额','u'=>'total_money'),
							array('n'=>'可用金额','u'=>'available_funds'),
							array('n'=>'冻结金额','u'=>'freeze_funds'),
							array('n'=>'交易对方','u'=>'counterparty'),
							array('n'=>'记录时间','u'=>'time'),
							array('n'=>'操作说明','u'=>'actionname')
							);
		foreach($list as $l){
			$content[]=array(
							'id'					=>' '.$l['id'],
							'username'				=>$l['username'],
							'operation'				=>$l['operation'],
							'total_money'			=>$l['total_money'],
							'available_funds'		=>$l['available_funds'],
							'freeze_funds'			=>$l['freeze_funds'],
							'counterparty'			=>$l['counterparty'],
							'time'					=>date('Y-m-d H:i:s',$l['time']),
							'actionname'			=>$l['actionname']
							);
		}
		$data['content']=$content;
		$excel=$this->excelExport($data);
		$this->Record('资金记录导出成功');//后台操作
			$this->success("导出成功","__APP__/TIFAWEB_DSWJCMS/Fund/money.html");
		
	}
	
	//--------其它费用操作-----------
    public function other(){
		if($this->_post('change')){
			$models = new Model();
			$y_price=$models->check($this->_post('price'),'number'); 
			$y_uid=$models->check($this->_post('uid'),'number'); 
			$y_explain=$models->check($this->_post('explain'),'require'); 
			if(!$y_price || !$y_uid || !$y_explain){
				$this->error("提交的参数有误！");
			}
			$user=M('user');
			$use=$user->where('id="'.$this->_post('uid').'"')->find();
			if($use){
				$Money=M('money');
				$money=$Money->where('uid="'.$this->_post('uid').'"')->find();
				if($this->_post('change')==1){	//奖励
					$models->query("UPDATE `ds_money` SET `total_money` = `total_money`+".$this->_post('price').", `available_funds` = `available_funds`+".$this->_post('price')." WHERE `uid` =".$this->_post('uid'));
					//记录添加点
					$money=$Money->where('uid="'.$this->_post('uid').'"')->find();
					$moneyLog=$this->moneyLog(array(0,$this->_post('explain'),$this->_post('price'),'平台',$money['total_money'],$money['available_funds'],$money['freeze_funds'],$this->_post('uid')),6);	//资金记录
				}else{
					if($this->_post('price')>$money['available_funds']){	//如果操作金额超过用户可用资金
						$this->error("操作金额超出用户可用资金！");
					}
					$models->query("UPDATE `ds_money` SET `total_money` = `total_money`-".$this->_post('price').", `available_funds` = `available_funds`-".$this->_post('price')." WHERE `uid` =".$this->_post('uid'));
					//记录添加点
					$money=$Money->where('uid="'.$this->_post('uid').'"')->find();
					$moneyLog=$this->moneyLog(array(0,$this->_post('explain'),$this->_post('price'),'平台',$money['total_money'],$money['available_funds'],$money['freeze_funds'],$this->_post('uid')),10);	//资金记录
				}
				//记录添加点
				$this->Record($this->_post('explain'));//后台操作
				$sendMsg=$this->silSingle(array('title'=>$this->_post('explain'),'sid'=>$this->_post('uid'),'msg'=>$this->_post('explain')));//站内信
				$arr['member']=array('uid'=>$this->_post('uid'),'name'=>'mem_other');
				$vip_points=M('vip_points');	
				$vips=$vip_points->where('uid="'.$this->_post('uid').'"')->find();
				if($vips['audit']==2){	//判断是不是开通了VIP
					$arr['vip']=array('uid'=>$this->_post('uid'),'name'=>'vip_other');
				}
				$userss=M('user');
				$promotes=$userss->where('id="'.$this->_post('uid').'"')->find();
				if($promotes['uid']){	//判断是不是有上线
					$arr['promote']=array('uid'=>$promotes['uid'],'name'=>'pro_other');
				}
				$integralAdd=$this->integralAdd($arr);	//积分操作
				$this->success("操作成功","__APP__/TIFAWEB_DSWJCMS/Fund/other");
			}else{
				$this->error("用户不存在！");
			}
		}else{
			$this->display();
		}
	}
	
}
?>