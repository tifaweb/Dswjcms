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
class CommAction extends SharingAction{
	/*
	*参数说明
	*	q		//需要操作的表
	*	n		//跳转提示语
	*	u		//跳转地址
	*	m		//存放LOG的数据并区分前后台		m[0]:1前台2后台3同时 其他为各LOG所需的数据
	*	i		//积分值
	*   o		//积分参数
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	protected   $modtab = array(
		'us'		=>'User',
		'borrow'	=>'Borrowing',
		'ufo'		=>'Userinfo',
		'sys'		=>'System',
		'with'		=>'Withdrawal',
		'off'		=>'Offline',
		'rech'		=>'Recharge',
		'int'		=>'Integral',
		'intgr'		=>'Integralconf',
		'forr'		=>'Forrecord',
		'unite'		=>'Unite',
		'memgrade'	=>'Membership_grade',
		'vip'		=>'Vip_points',
		'ag'		=>'Auth_group',
	  	'aga'		=>'Auth_group_access',
	  	'ar'		=>'Auth_rule',
	 	'am'		=>'Admin',
	  	'sta' 		=>'Site_add',
	  	'art' 		=>'Article',
	  	'atd' 		=>'Article_add',
	  	'cm'		=>'Commision',
	);
	
	protected function _list($array=array()){
		$map = $array['map'];
		$field = $array['field'] ? $array['field'] :'';
		$order = $array['order'] ? $array['order'] : " id " ;
		$group = $array['group'] ? $array['group'] : '';
		$pagenub = $array['pagenub'] ?$array['pagenub'] :10;
		if($model){
			$mod= $this->modtab[$model];
			
		}else{
			$mod = $this->getActionName();
		}
		$mod= D($mod);
		import('ORG.Util.Page');
		$count  = $mod->where($map)->count();// 查询满足要求的总记录数
        $Page  = new Page($count,$pagenub);// 实例化分页类 传入总记录数和每页显示的记录数
        $show  = $Page->show();// 分页显示输出
		if($field && $group){
			$list = $mod->where($map)
			->field($field)
			->order($order)
			->group($group)
			->limit($Page->firstRow.','.$Page->listRows)
			->select();
		}elseif($field && !$group){
		  $list = $mod->where($map)
			->field($field)
			->order($order)
			->limit($Page->firstRow.','.$Page->listRows)
			->select();
		}elseif(!$field && $group){
		  $list = $mod->where($map)
			->order($order)
			->group($group)
			->limit($Page->firstRow.','.$Page->listRows)
			->select();
		}else{
		  $list = $mod->where($map)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		}
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		
	}
	
   public function upda(){
		$q=$_REQUEST['q'];	
		$sid=intval($_REQUEST['sid']);
		$u=$_REQUEST['u']?$_REQUEST['u']:'/';
		$n=$_REQUEST['n']?$_REQUEST['n']:'更新';
		
		if($q){
			$model= $this->modtab;
			$model = D($model[$q]);
			
		}else{
		   $name=$this->getActionName();
		   $model = D ($name);
		}
		$pk = $_REQUEST['g']?$_REQUEST['g']:$model->getPk();
		if($model->create()){
			  $result = $model->where(array($pk=>$sid))->save();
			 if($result){
				if(GROUP_NAME=='Admin'){
					$this->Record($n.'成功');//后台操作
				}else{
					$this->userLog($n.'成功');//前台操作
				}
				 $this->success($n."成功",$u);
				
			 }else{
				if(GROUP_NAME=='Admin'){
					$this->Record($n.'失败');//后台操作
				}else{
					$this->userLog($n.'失败');//前台操作
				}
				$this->error($n."失败");
			 }			 			
		}else{
			 $this->error($model->getError());
		}

	}
	
	public function del(){
		$q=$_REQUEST['q'];
		$id=intval($_REQUEST['id']);
		$u=$_REQUEST['u']?$_REQUEST['u']:'';
		$n=$_REQUEST['n']?$_REQUEST['n']:'删除';
		if(!$id){
			 dwzSt();
			exit();
		}
		if(isset($_REQUEST['q'])){
			$model= $this->modtab;
	     	$model = D($model[$q]);
		}else{
		   $name=$this->getActionName();
		   $model = D ($name);
		}		
		$pk = $model->getPk();
         $result = $model->where(array($pk=>$id))->delete();
		if($result){
			if(GROUP_NAME=='Admin'){
				$this->Record($n.'成功');//后台操作
			}else{
				$this->userLog($n.'成功');//前台操作
			}
			 $this->success($n."成功",$u);
				
		}else{
			if(GROUP_NAME=='Admin'){
				$this->Record($n.'失败');//后台操作
			}else{
				$this->userLog($n.'失败');//前台操作
			}
			$this->error($n."失败");
		}			 			
	

	}
	
	public function add(){
		$q=$_REQUEST['q'];	
		$n=$_REQUEST['n']?$_REQUEST['n']:'添加';
		$u=$_REQUEST['u']?$_REQUEST['u']:'/';
		if($q){
			$model= $this->modtab;	
	     	$model = D($model[$q]);
		}else{
		   $name=$this->getActionName();
		   $model = D ($name);
		}
		
        if($model->create()){
		     $result = $model->add();
			if($result){
				if(GROUP_NAME=='Admin'){
					$this->Record($n.'成功');//后台操作
				}else{
					$this->userLog($n.'成功');//前台操作
				}
				$this->success($n."成功",$u);			
			}else{
				if(GROUP_NAME=='Admin'){
					$this->Record($n.'失败');//后台操作
				}else{
					$this->userLog($n.'失败');//前台操作
				}
				 $this->error($n."失败");
			}	
		}else{
			$this->error($model->getError());
			
		}
		
	}
	
	//带积分操作的更新
	public function integral_upda(){
		$msgTools = A('msg','Event');
		$q=$_REQUEST['q'];	
		$sid=intval($_REQUEST['sid']);
		$u=$_REQUEST['u']?$_REQUEST['u']:'/';
		$n=$_REQUEST['n']?$_REQUEST['n']:'更新';
		$i=$_REQUEST['i']?$_REQUEST['i']:'';
		$o=$_REQUEST['o']?$_REQUEST['o']:'';
		$e=$_REQUEST['e']?$_REQUEST['e']:'添加成功';
		if($q){
			$model= $this->modtab;
			$model = D($model[$q]);
			
		}else{
		   $name=$this->getActionName();
		   $model = D ($name);
		}
		$pk = $_REQUEST['g']?$_REQUEST['g']:$model->getPk();
		if($model->create()){
			  //记录添加点
				$Money=M('money');
				$models = new Model();
				if($i){	//如果有资金操作
				$money=$Money->where('uid='.$sid)->find();
				if($i>$money['available_funds']){
					$this->error("用户可用资金不足");
				}
					$models->query("UPDATE `ds_money` SET `total_money` = `total_money`-".$i.", `available_funds` = `available_funds`-".$i." WHERE `uid` =".$sid);
					$money=$Money->where('uid='.$sid)->find();
					$moneyLog=$this->moneyLog(array(0,$e,$i,'平台',$money['total_money'],$money['available_funds'],$money['freeze_funds'],$sid));	//资金记录
				}
			  $result = $model->where(array($pk=>$sid))->save();
			 if($result){
				if(GROUP_NAME=='Admin'){
					$this->Record($e);//后台操作
				}else{
					$this->userLog($e);//前台操作
				}
				//记录添加点
				$sendMsg=$msgTools->sendMsg(3,$e,$e,'admin',$sid);//站内信
				$arr['member']=array('uid'=>$sid,'name'=>'mem_'.$o);
				$vip_points=M('vip_points');	
				$vips=$vip_points->where('uid='.$sid)->find();
				if($vips['audit']==2){	//判断是不是开通了VIP
					$arr['vip']=array('uid'=>$sid,'name'=>'vip_'.$o);
				}
				$userss=M('user');
				$promotes=$userss->where('id='.$sid)->find();
				if($promotes['uid']){	//判断是不是有上线
					$arr['vip']=array('uid'=>$promotes['uid'],'name'=>'pro_'.$o);
				}
				$integralAdd=$this->integralAdd($arr);	//积分操作
				 
				 $this->success($n."成功",$u);
				
			 }else{
				if(GROUP_NAME=='Admin'){
					$this->Record($n.'失败');//后台操作
				}else{
					$this->userLog($n.'失败');//前台操作
				}
				$this->error($n."失败");
			 }			 			
		}else{
			 $this->error($model->getError());
		}

	}

   /**
	*
	* @标操作记录
	* @id 		1多维数组0一维
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
    public function borrowLog($arr,$id=0){
			$models = new Model();
			if($id==1){
				foreach($arr as $k => $ar){
					$array[$k]['type']		= $ar['type'];
					unset($ar['type']);
					$array[$k]['actionname']= json_encode($ar);
					$array[$k]['ip']		= get_client_ip();
					$array[$k]['time']		= time();
				}
				return $models->table('ds_borrow_log')->addAll($array);
			}else{
				$array['type']		= $arr['type'];
				unset($arr['type']);
				$array['actionname']= json_encode($arr);
				$array['ip']		= get_client_ip();
				$array['time']		= time();
				return $models->table('ds_borrow_log')->add($array);
			}
			
    }
	
	/**
	*
	* @会员操作记录
	* @arr		记录说明
	* @uid		用户ID
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
    public function userLog($arr,$uid){
			$models = new Model();
            $array['uid']		= $uid?$uid:$this->_session('user_uid');
			$array['actionname']= $arr;
			$array['page']		= $_SERVER['PHP_SELF'];
            $array['ip']		= get_client_ip();
            $array['time']		= time();
			return $models->table('ds_user_log')->add($array);
    }

	/**
     * @资金/积分操作记录
     * @array   0操作类型1操作说明2操作金额3交易对方4总额5余额6冻结7用户
     * @id      是否开启
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
     */
    public function moneyLog($array,$id=0){
        if($id==0){
			$models = new Model();
			//$money=M('money');
			//$moneys=reset($money->field('total_money,available_funds,freeze_funds')->where('`uid`='.$this->_session('user_uid'))->select());
            $arrays['uid']				= $array[7]?$array[7]:$this->_session('user_uid');
            $arrays['type']				= $array[0];
			$arrays['actionname']		= $array[1];
			$arrays['total_money']		= $array[4];
			$arrays['available_funds']	= $array[5];
			$arrays['freeze_funds']		= $array[6];
			$arrays['counterparty']		= $array[3];
			$arrays['operation']		= $array[2];
            $arrays['time']				= time();
			$arrays['ip']				= get_client_ip();
			return $models->table('ds_money_log')->add($arrays);
        }
    }
			
	//过滤器
	    protected function dsFilter(){
		$name= ACTION_NAME;
        if(array_key_exists($name,$this->Filter)){
		}
	}
   /**	
	* @自动投标
	* @id		标ID
	* @price	投标金额
	* @surplus	可投金额
	* @uid		用户ID
	* @uname	用户名
	* @total	自动投标可投金额
	*/
	/*public function autoTender($auto){
		$models = new Model();
		$msgTools = A('msg','Event');
		$uid=$auto['uid'];
		$uname=$auto['uname'];
		$data['surplus']=$auto['surplus']-$auto['price'];
		$total=$auto['total']-$auto['price'];
		$borrow=$models->table('ds_borrowing')->where('id='.$auto['id'])->save($data);
		$models->table('ds_automatic')->where('uid='.$auto['id'])->save(array('total'=>$total));	//更新自动投标金额
		$rewardCalculationArr['reward_type']	=$borr['reward_type'];
		$rewardCalculationArr['reward']			=$borr['reward'];
		$rewardCalculationArr['money']			=$borr['money'];
		$rewardCalculationArr['price']			=$auto['price'];
		$array['type']			=3;
		$array['uid']			= $uid;
		$array['bid']			=$auto['id'];
		$array['instructions']	='对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的自动投标';
		$logtotal=$array['total']			=$users['total_money'];
		$logavailable=$moneyarr['available_funds']=$array['available']		=$users['available_funds'];
		$logfreeze=$moneyarr['freeze_funds']=$array['freeze']				=$users['freeze_funds'];
		$array['operation_reward']				=R('Sharing/rewardCalculation',array($rewardCalculationArr,$borr['money']));
		$array['interest']						=R('Sharing/interest',array($borr,$auto['price']));
		$array['operation']		=$auto['price'];
		$borrowlog=$this->borrowLog($array);
		unset($array);
		unset($users);
		$array['operation_reward']				=R('Sharing/rewardCalculation',array($rewardCalculationArr,$borr['money']));
		$array['interest']						=R('Sharing/interest',array($borr,$auto['price']));
		$array['type']				=4;
		$array['uid']				=$borr['uid'];
		$array['uname']				= $uname;
		$array['bid']				=$auto['id'];
		$array['instructions']		='用户：'.$uname.'对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的自动投标';
		$array['total']				=$borr['total_money'];
		$array['available']			=$borr['available_funds'];
		$array['freeze']			=$borr['freeze_funds'];
		$array['operation']			=$auto['price'];
		$array['collected']			=$borr['collected']+$auto['price'];
		$borrowlogs=$this->borrowLog($array);
		unset($array);
		unset($moneyarr);
		//记录添加点
		$userLog=$this->userLog('对【'.$borr['title'].'】的自动投标');//会员记录
		$moneyLog=$this->moneyLog(array(0,'对【'.$borr['title'].'】的自动投标冻结资金',$auto['price'],$borr['username'],$logtotal,$logavailable,$logfreeze));	//资金记录
		$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】的自动投标','对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的自动投标,冻结资金','admin',$uname);//站内信
		$arr['member']=array('uid'=>$uid,'name'=>'mem_tender');
		$vip_points=M('vip_points');
		$vips=$vip_points->where('uid='.$uid)->find();
		if($vips['audit']==2){	//判断是不是开通了VIP
			$arr['vip']=array('uid'=>$uid,'name'=>'vip_tender');
		}
		$userss=M('user');
		$promotes=$userss->where('id='.$uid)->find();
		if($promotes['uid']){	//判断是不是有上线
			$arr['vip']=array('uid'=>$promotes['uid'],'name'=>'pro_tender');
		}
		$integralAdd=$this->integralAdd($arr);	//积分操作
		
		if($borr['surplus']==$auto['price']){	//满标
			if($borr['type']==0){	//秒标
				$reviewtime=time();
				if($borr['candra']==0){	//获取用户选择的是月标还是天标
					$month=$borr['deadline'];
					$limittime=strtotime("+$month month");
				}else{
					$day=$borr['deadline'];
					$limittime=strtotime("+$day day");
				}
				$bid_records=R('Sharing/secondPayment',array($borr));
				$borrows=$models->table('ds_borrowing')->where('id='.$auto['id'])->save(array('state'=>9,'reviewtime'=>$reviewtime,'limittime'=>$limittime));
				
			}else{
				$borrows=$models->table('ds_borrowing')->where('id='.$auto['id'])->save(array('state'=>5));
				$bid_records=R('Sharing/withAudit',array($borr));
			}
		}
	}*/
	
	//投标
	public function investUpdate(){
		$this->copyright();
		if($this->_session('verify') != md5($this->_post('proving'))) {
		   $this->error('验证码错误！');
			exit;
		}
		$msgTools = A('msg','Event');
		$models = new Model();
		$uid=$this->_post('uid')?$this->_post('uid'):$this->_session('user_uid');
		$uname=$this->_post('uname')?$this->_post('uname'):$this->_session('user_name');
		if($uid){
			$borr=R('Sharing/borr',array($this->_post('id')));
			if($borr['surplus']>=$this->_post('price')){	//所需金额小于投标金额
				$users=reset(R('Sharing/user_details'));
				if($this->_post('update_uid')==$uid){
					$this->error("不能投自己的标！");
				}else{
					if($this->_post('password')==$borr['password']){
						if($this->_post('price')<$borr['min'] || $this->_post('price')>$borr['surplus']){
								$this->error("操作有误，已记录，如误操作请联系管理员！");
						}
						if($this->_post('price')>$borr['max']){
							if($borr['max']>0){
								$this->error("操作有误，已记录，如误操作请联系管理员！");
							}
						}
						if($this->_post('price')>$users['available_funds']){	//资金不足
							$this->error("账户余额不足，请充值！",'__ROOT__/Center/fund/inject.html');
						}
						$user=D('User');
						$pay_password=$user->userPayMd5($this->_post('pay_password'));
						if($users['pay_password']==$pay_password){	//支付密码
							$borrowing=D('Borrowing');
							if($borrowing->create()){	
								$data['surplus']=$borr['surplus']-$this->_post('price');		
									$borrow=$models->table('ds_borrowing')->where('id='.$this->_post('id'))->save($data);
									$rewardCalculationArr['reward_type']	=$borr['reward_type'];
									$rewardCalculationArr['reward']		=$borr['reward'];
									$rewardCalculationArr['money']			=$borr['money'];
									$rewardCalculationArr['price']			=$this->_post('price');
									$counters=$this->counters($this->_post('price'),$borr['rates'],$borr['deadline'],$borr['candra'],$borr['way']);	//利息计算
									$array['type']			=3;
									$array['uid']			= $uid;
									$array['bid']			=$this->_post('id');
									$array['instructions']	='对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的投标';
									$logtotal=$array['total']			=$users['total_money'];
									$logavailable=$moneyarr['available_funds']=$array['available']		=$users['available_funds']-$this->_post('price');
									//($borr['type']==0){	//秒标
									//	$logfreeze=$moneyarr['freeze_funds']=$array['freeze']				=$users['freeze_funds'];
									//}else{
										$logfreeze=$moneyarr['freeze_funds']=$array['freeze']				=$users['freeze_funds']+$this->_post('price');
									//}
									$array['operation_reward']				=R('Sharing/rewardCalculation',array($rewardCalculationArr,$borr['money']));
									//$array['interest']						=R('Sharing/interest',array($borr,$this->_post('price')));
									$array['interest']						=$counters['interest'];
									$array['operation']		=$this->_post('price');
									$borrowlog=$this->borrowLog($array);
									unset($array);
									unset($users);
									$array['operation_reward']				=R('Sharing/rewardCalculation',array($rewardCalculationArr,$borr['money']));
									//$array['interest']						=R('Sharing/interest',array($borr,$this->_post('price')));
									$array['interest']						=$counters['interest'];
									$array['type']				=4;
									$array['uid']				=$borr['uid'];
									$array['uname']				= $uname;
									$array['bid']				=$this->_post('id');
									$array['instructions']		='用户：'.$uname.'对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的投标';
									$array['total']				=$borr['total_money'];
									$array['available']			=$borr['available_funds'];
									$array['freeze']			=$borr['freeze_funds'];
									$array['operation']			=$this->_post('price');
									$array['collected']			=$borr['collected']+$this->_post('price');
									$borrowlogs=$this->borrowLog($array);
									$money=M('money');
									$money=$models->table('ds_money')->where('uid='.$uid)->save($moneyarr);
									unset($array);
									unset($moneyarr);
									//记录添加点
									$userLog=$this->userLog('对【'.$borr['title'].'】投标');//会员记录
									$moneyLog=$this->moneyLog(array(0,'对【'.$borr['title'].'】投标冻结资金',$this->_post('price'),$borr['username'],$logtotal,$logavailable,$logfreeze));	//资金记录
									$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】的投标','对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的投标,冻结资金','admin',$uname);//站内信
									$arr['member']=array('uid'=>$uid,'name'=>'mem_tender');
									$vip_points=M('vip_points');
									$vips=$vip_points->where('uid='.$uid)->find();
									if($vips['audit']==2){	//判断是不是开通了VIP
										$arr['vip']=array('uid'=>$uid,'name'=>'vip_tender');
									}
									$userss=M('user');
									$promotes=$userss->where('id='.$uid)->find();
									if($promotes['uid']){	//判断是不是有上线
										$arr['vip']=array('uid'=>$promotes['uid'],'name'=>'pro_tender');
									}
									$integralAdd=$this->integralAdd($arr);	//积分操作
									
									if($borr['surplus']==$this->_post('price')){	//满标
										if($borr['type']==0){	//秒标
											$reviewtime=time();
											if($borr['candra']==0){	//获取用户选择的是月标还是天标
												$month=$borr['deadline'];
												$limittime=strtotime("+$month month");
											}else{
												$day=$borr['deadline'];
												$limittime=strtotime("+$day day");
											}
											$bid_records=R('Sharing/secondPayment',array($borr));
											$borrows=$models->table('ds_borrowing')->where('id='.$this->_post('id'))->save(array('state'=>9,'reviewtime'=>$reviewtime,'limittime'=>$limittime));
											$this->success('投标成功','__ROOT__/Center/invest/win.html');
											exit;
										}else{
											$borrows=$models->table('ds_borrowing')->where('id='.$this->_post('id'))->save(array('state'=>5));
											$this->success('投标成功','__ROOT__/Center/invest/isbid.html');
											exit;
										}
									}else{	//没满标
										$this->success('投标成功','__ROOT__/Center/invest/isbid.html');
									}
							}else{
								 $this->error($borrowing->getError());
							}
						}else{
							$this->error("支付密码错误！");
						}
					}else{
						$this->error("密码标密码错误！");
					}
				}
			}else{
				$this->error("此标状态已发生改变，请从新提交！");
			}
		}else{
			$this->error("请先登陆！",'__ROOT__/Logo/login.html');
		}	
	}
	
	//投标（流转标）
	public function flowUpdate(){
		$this->copyright();
		if($this->_session('verify') != md5($this->_post('proving'))) {
		   $this->error('验证码错误！');
			exit;
		}
		$refund=M('collection');
		$msgTools = A('msg','Event');
		$models = new Model();
		$uid=$this->_post('uid')?$this->_post('uid'):$this->_session('user_uid');
		$refu=$refund->field('nper')->where('uid='.$uid.' and bid='.$this->_post('id'))->order('`nper` DESC ')->find();
		$uname=$this->_post('uname')?$this->_post('uname'):$this->_session('user_name');
		$collection=M('collection');
		$collec=$collection->where('uid='.$uid.' and bid='.$this->_post('id').' and type=0')->find();
		
		if($uid){
			if($collec){	//流转标一轮用户只能认购一次
				$this->error("每个用户只限认购一次！");
			}
			$borr=reset(R('Sharing/borrow_information',array($this->_post('id'))));
			if($borr['candra']==0){	//获取用户选择的是月标还是天标
				$month=$this->_post('deadline');
				$limittime=strtotime("+$month month");
				$limtime=floor(($borr['limittime']-time())/30/86400);	//还可认购时间
				if($limtime>=$borr['flow_deadline']){	//当还可认购时间大于等于流转期限时（因每个月按30天算，会有时间差）
					$limtime=$limtime-1;
				}
				$moday='个月';
			}else{
				$day=$this->_post('deadline');
				$limittime=strtotime("+$day day");
				$limtime=floor(($borr['limittime']-time())/86400);	//还可认购时间
				$moday='天';
			}
			if($limittime>$borr['limittime']){	//如果用户认购期限已超出原借款期限就跳出
				if($limtime<$borr['min_limit']){	//还可认购时间比最低认购期限短
					$this->error("此借款标可认购期限不满足最低认购期限！");
				}else{
					$this->error("认购期限大于原借款期限，此标最多还可认购".($limtime-1).$moday."！");
				}
			}
			if($borr['flow_deadline']<$this->_post('deadline') || $this->_post('deadline')<$borr['min_limit']){
				$this->error("认购期限不能大于流转期限小于最低流转期限！");
			}
			if($borr['subscribe']>=$this->_post('copies')){	//认购份数比剩余数小
				$users=reset(R('Sharing/user_details'));
				if($this->_post('update_uid')==$uid){
					$this->error("不能投自己的标！");
				}else{
					if($this->_post('password')==$borr['password']){
						if($this->_post('copies')<1 || $this->_post('copies')>$borr['subscribe']){
								$this->error("操作有误，已记录，如误操作请联系管理员！");
						}
						$funds=floor($users['available_funds']/$borr['min']);	//用户可认购数
						if($this->_post('copies')>$funds){
							$this->error("账户余额不足，请充值！",'__ROOT__/Center/fund/inject.html');
						}
						$user=D('User');
						$pay_password=$user->userPayMd5($this->_post('pay_password'));
						if($users['pay_password']==$pay_password){	//支付密码
							$borrowing=D('Borrowing');
							if($borrowing->create()){	
								$data['flows']=$borr['flows']+$this->_post('copies');		
									$borrow=$models->table('ds_borrowing')->where('id='.$this->_post('id'))->save($data);
									$rewardCalculationArr['reward_type']	=$borr['reward_type'];
									$rewardCalculationArr['reward']			=$borr['reward'];
									$rewardCalculationArr['money']			=$borr['money'];
									$rewardCalculationArr['price']			=$this->_post('copies')*$borr['min'];
									$counters=$this->counters($this->_post('copies')*$borr['min'],$borr['rates'],$this->_post('deadline'),$borr['candra'],$borr['way']);	//利息计算
									$money=M('money');
									$array['type']			=15;
									$array['uid']			=$uid;
									$array['bid']			=$this->_post('id');
									$array['instructions']	='对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的认购';
									$logtotal=$moneyarr['total_money']=$array['total']			=$users['total_money']-$this->_post('copies')*$borr['min'];
									$logavailable=$moneyarr['available_funds']=$array['available']		=$users['available_funds']-$this->_post('copies')*$borr['min'];
									$array['operation_reward']				=R('Sharing/rewardCalculation',array($rewardCalculationArr,$borr['money']));
									//$array['interest']						=R('Sharing/interest',array($borr,$this->_post('copies')*$borr['min'],$this->_post('deadline')));
									$array['interest']						=$counters['interest'];
									$moneyarr['stay_interest']=$array['stay_interest']=$users['stay_interest']+$array['interest'];
									$moneyarr['due_in']=$array['collected']	=$users['due_in']+$this->_post('copies')*$borr['min']+$array['interest']+$array['operation_reward'];
									$array['copies']		=$this->_post('copies');
									$array['deadline']		=$this->_post('deadline');
									$array['continue']		=$this->_post('continue');
									$array['candra']		=$borr['candra'];
									$array['operation']		=$this->_post('copies')*$borr['min'];
									$borrowlog=$this->borrowLog($array);
									$money=$models->table('ds_money')->where('uid='.$uid)->save($moneyarr);
									//记录添加点
									$userLog=$this->userLog('对【'.$borr['title'].'】的认购');//会员记录
									$moneyLog=$this->moneyLog(array(0,'对【'.$borr['title'].'】的认购,扣除资金',$array['operation'],$borr['username'],$logtotal,$logavailable,$users['freeze_funds']));	//资金记录
									$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】的认购','对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的认购,扣除资金','admin',$uid);//站内信
									$arr['member']=array('uid'=>$uid,'name'=>'mem_flow');
									$vip_points=M('vip_points');
									$vips=$vip_points->where('uid='.$uid)->find();
									if($vips['audit']==2){	//判断是不是开通了VIP
										$arr['vip']=array('uid'=>$uid,'name'=>'vip_flow');
									}
									$userss=M('user');
									$promotes=$userss->where('id='.$uid)->find();
									if($promotes['uid']){	//判断是不是有上线
										$arr['vip']=array('uid'=>$promotes['uid'],'name'=>'pro_flow');
									}
									$integralAdd=$this->integralAdd($arr);	//积分操作
									unset($array);
									unset($moneyarr);
									unset($users);
									$users=reset(R('Sharing/user_details',array($borr['uid'])));
									$array['type']				=16;
									$array['uid']				=$borr['uid'];
									$array['uname']				=$uname;
									$array['bid']				=$this->_post('id');
									$array['instructions']		='用户：'.$uname.'对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的认购';
									$moneyarr['total_money']=$array['total']			=$users['total_money']+$this->_post('copies')*$borr['min'];
									$moneyarr['available_funds']=$array['available']		=$users['available_funds']+$this->_post('copies')*$borr['min'];
									$array['operation_reward']				=R('Sharing/rewardCalculation',array($rewardCalculationArr,$borr['money']));
									//$array['interest']						=R('Sharing/interest',array($borr,$this->_post('copies')*$borr['min'],$this->_post('deadline')));
									$array['deadline']		=$this->_post('deadline');
									$array['candra']		=$borr['candra'];
									$array['interest']						=$counters['interest'];
									$moneyarr['stay_still']=$array['also']	=$users['stay_still']+$this->_post('copies')*$borr['min']+$array['interest']+$array['operation_reward'];
									$array['operation']		=$this->_post('copies')*$borr['min'];
									$borrowlogs=$this->borrowLog($array);
									$moneys=$models->table('ds_money')->where('uid='.$borr['uid'])->save($moneyarr);
									
									//记录添加点
									$moneyLogs=$this->moneyLog(array(0,$uname.'对【'.$borr['title'].'】的认购,获得资金',$array['operation'],$uname,$moneyarr['total_money'],$moneyarr['available_funds'],$users['freeze_funds'],$borr['uid']));	//资金记录
									$sendMsgs=$msgTools->sendMsg(3,$uname.'对【'.$borr['title'].'】的认购,获得资金',$uname.'对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的认购,获得资金','admin',$borr['uid']);//站内信
									
									$fcollection=$this->fcollection($this->_post('id'),$uid);//收款记录(流转)
									$frefunds=$this->frefunds($this->_post('id'),($refu['nper']+1));	//还款记录(流转)
									unset($array);
									unset($moneyarr);
									unset($users);
									if($borr['subscribe']==$this->_post('copies')){	//满标							
										$borrows=$models->table('ds_borrowing')->where('id='.$this->_post('id'))->save(array('state'=>11));
											$this->success('认购成功','__ROOT__/Center/invest/isflow.html');
									}else{
											$this->success('认购成功','__ROOT__/Center/invest/isflow.html');
									}
							}else{
								 $this->error($borrowing->getError());
							}
						}else{
							$this->error("支付密码错误！");
						}
					}else{
						$this->error("密码标密码错误！");
					}
				}
			}else{
				$this->error("此标状态已发生改变，请从新提交！");
			}
		}else{
			$this->error("请先登陆！",'__ROOT__/Logo/login.html');
		}	
	}
}
?>