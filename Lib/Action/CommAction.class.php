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
		'unite'		=>'Unite',
		'memgrade'	=>'Membership_grade',
		'ag'		=>'Auth_group',
	  	'aga'		=>'Auth_group_access',
	  	'ar'		=>'Auth_rule',
	 	'am'		=>'Admin',
	  	'sta' 		=>'Site_add',
	  	'art' 		=>'Article',
	  	'atd' 		=>'Article_add',
		'Guar'		=>'Guaranteeapply',
		'Gcomp'		=>'Guaranteecomp',
		'on'		=>'Online',
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
	
   protected function upda(){
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
	
	protected function del(){
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
	
	protected function add(){
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
	protected function integral_upda(){
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
					$moneyLog=$this->moneyLog(array(0,$e,$i,'平台',$money['total_money'],$money['available_funds'],$money['freeze_funds'],$sid),10);	//资金记录
				}
			  $result = $model->where(array($pk=>$sid))->save();
			 if($result){
				if(GROUP_NAME=='Admin'){
					$this->Record($e);//后台操作
				}else{
					$this->userLog($e);//前台操作
				}
				//记录添加点
				$sendMsg=$this->silSingle(array('title'=>$e,'sid'=>$sid,'msg'=>$e));//站内信
				//如果是实名认证
				if($o=='certification'){
					$this->creditShared($sid);
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
			
	//过滤器
	    protected function dsFilter(){
		$name= ACTION_NAME;
        if(array_key_exists($name,$this->Filter)){
		}
	}
	
	/**
	 * @投标（普通标)
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	public function investUpdate(){
		$this->homeVerify();
		$this->copyright();
		if($this->_session('verify') != md5($this->_post('proving'))) {
		   $this->error('验证码错误！');
			exit;
		}
		session('verify',null);
		$models = new Model();
		
		$uid=$this->_post('uid')?$this->_post('uid'):$this->_session('user_uid');
		$uname=$this->_post('uname')?$this->_post('uname'):$this->_session('user_name');
		//解决多次提交导致的误操作
		$number=$this->orderNumber();
		$this->bidPretreatment($number);
		
		if($uid){
			$borr=D('Borrowing')->relation(true)->where(array('id'=>$this->_post('id')))->find();
			if($borr['surplus']>=$this->_post('price') || $borr['surplus']<$borr['min']){	//所需金额小于投标金额
			if($borr['surplus']<$this->_post('price')){	//如果投资的金额比所需的大，那么就将投资金额改为所需金额
				$_POST['price']=$borr['surplus'];
			}
				$users=reset($this->user_details());
				if($this->_post('update_uid')==$uid){
					$this->error("不能投自己的标！");
				}else{
					if($this->_post('password')==$borr['password']){
						if($this->_post('price')<$borr['min'] || $this->_post('price')>$borr['surplus']){
							if($borr['surplus']>$borr['min']){	//如果所需金额大于最小投资金额
								$this->error("操作有误，已记录，如误操作请联系管理员！");
							}
						}
						if($this->_post('price')>$borr['max']){
							if($borr['max']>0){
								$this->error("操作有误，已记录，如误操作请联系管理员！");
							}
						}
						if($this->_post('price')>$users['available_funds']){	//资金不足
							$this->error("账户余额不足，请充值！",'__ROOT__/Center/fund/mid/inject.html');
						}
						$user=D('User');
						$pay_password=$user->userPayMd5($this->_post('pay_password'));
						if($users['pay_password']==$pay_password){	//支付密码
							$borrowing=D('Borrowing');
							if($borrowing->create()){	
								$data['surplus']=$borr['surplus']-$this->_post('price');		
									$borrow=$models->table('ds_borrowing')->where(array('id'=>$this->_post('id')))->save($data);
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
										$logfreeze=$moneyarr['freeze_funds']=$array['freeze']				=$users['freeze_funds']+$this->_post('price');
									$array['operation_reward']				=$this->rewardCalculation($rewardCalculationArr,$borr['money']);
									$array['interest']						=$counters['interest'];
									$array['operation']		=$this->_post('price');
									$borrowlog=$this->borrowLog($array,'',$number);
									unset($array);
									unset($users);
									$array['operation_reward']				=$this->rewardCalculation($rewardCalculationArr,$borr['money']);
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
									$borrowlogs=$this->borrowLog($array,'',$number);
									$money=M('money');
									$money=$models->table('ds_money')->where(array('uid'=>$uid))->save($moneyarr);
									unset($array);
									unset($moneyarr);
									//记录添加点
									$userLog=$this->userLog('对【'.$borr['title'].'】投标');//会员记录
									$moneyLog=$this->moneyLog(array(0,'对【'.$borr['title'].'】投标冻结资金',$this->_post('price'),$borr['username'],$logtotal,$logavailable,$logfreeze),15);	//资金记录
									$sendMsg=$this->silSingle(array('title'=>'对【'.$borr['title'].'】的投标','sid'=>$uid,'msg'=>'对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的投标,冻结资金'));//站内信
									if($borr['surplus']==$this->_post('price')){	//满标
										
											$borrows=$models->table('ds_borrowing')->where(array('id'=>$this->_post('id')))->save(array('state'=>5));
											$this->success('投标成功','__ROOT__/Center/invest/mid/isbid.html');
											exit;
									}else{	//没满标
										$this->success('投标成功','__ROOT__/Center/invest/mid/isbid.html');
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
			$this->success('请先登陆！','__ROOT__/Logo/login.html');
		}	
	}
}
?>