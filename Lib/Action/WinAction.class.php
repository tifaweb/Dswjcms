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
class WinAction extends CommAction{
	protected function _initialize(){
		//禁止非微信访问
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		/*if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){
			echo "Not allowed to access!";
			echo '<br/>
			<script language="JavaScript">
			function myrefresh()
			{
				   window.location.href="http://www.dswjcms.com";
			}
			setTimeout("myrefresh()",2000); //指定1秒刷新一次
			</script>
			';
			exit;
		}*/
		$this->webScan();//安全检测记录
		header("Content-Type:text/html; charset=utf-8");
		$dirname = F('wdirname')?F('wdirname'):"Default";
		C('DEFAULT_THEME','template/'.$dirname);	//自动切换模板
		C('TMPL_ACTION_ERROR','Index/jump');	//默认错误跳转对应的模板文件
		C('TMPL_ACTION_SUCCESS','Index/jump');	//默认成功跳转对应的模板文件
		$system=$this->systems();
		$this->assign('s',$system);
		//缓存
		if($this->_cookie('user_uid')>0){
			session('user_uid',$this->_cookie('user_uid'),604800);
			session('user_name',$this->_cookie('user_name'),604800);
			session('user_verify',$this->_cookie('user_verify'),604800);
		}
		if($this->_get('app')==1){	//切换为APP模式
			cookie('app',1,604800);
			session('app',1,604800);
		}
		if($this->_get('app')==2){
			cookie('app',2,604800);
			session('app',2,604800);
		}
	}
	
	//投标
	public function subcasts(){
		$models = new Model();
		$uid=$this->_session('user_uid');
		$uname=$this->_session('user_name');
		//解决多次提交导致的误操作
		$number=$this->orderNumber();
		$this->bidPretreatment($number);
		if($this->_post('price')<=0){
			$this->ajaxReturn(0,"投标金额有误",0);
		}
			$borr=D('Borrowing')->relation(true)->where('`id`='.$this->_post('id'))->find();
			if($borr['surplus']>=$this->_post('price') || $borr['surplus']<$borr['min']){	//所需金额小于投标金额
			if($borr['surplus']<$this->_post('price')){	//如果投资的金额比所需的大，那么就将投资金额改为所需金额
				$_POST['price']=$borr['surplus'];
			}
				$users=reset($this->user_details());
				if($this->_post('update_uid')==$uid){
					$this->ajaxReturn(0,"不能投自己的标",0);
				}else{
					if($this->_post('password')==$borr['password']){
						if($this->_post('price')<1 ||($this->_post('price')>$users['available_funds'])){	//资金不足
							$this->ajaxReturn(0,"账户余额不足，请充值！",0);
						}
						
						if($this->_post('price')<$borr['min'] || $this->_post('price')>$borr['surplus']){
							if($borr['surplus']>$borr['min']){	//如果所需金额大于最小投资金额
								$this->ajaxReturn(0,"操作有误，已记录，如误操作请联系管理员！",0);
							}
						}
						if($this->_post('price')>$borr['max']){
							if($borr['max']>0){
								$this->ajaxReturn(0,"操作有误，已记录，如误操作请联系管理员！",0);
							}
						}
						
						$user=D('User');
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
									$sendMsg=$this->silSingle(array('title'=>'对【'.$borr['title'].'】的投标','sid'=>$uid,'msg'=>'对【'.$borr['title'].'】的投标,冻结资金'));//站内信
									if($borr['surplus']==$this->_post('price')){	//满标
											$borrows=$models->table('ds_borrowing')->where(array('id'=>$this->_post('id')))->save(array('state'=>5));
											$this->ajaxReturn(1,'投标成功',1);
									}else{	//没满标
										$this->ajaxReturn(1,'投标成功',1);
									}
							}else{
								 $this->ajaxReturn(0,$borrowing->getError(),0);
							}
						
					}else{
						$this->ajaxReturn(0,"密码标密码错误！",0);
					}
				}
			}else{
				$this->ajaxReturn(0,"此标状态已发生改变，请从新提交！",0);
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
		session('user_verify',null);
		cookie('user_uid',null);
		cookie('user_name',null);
		cookie('user_verify',null);
		cookie('promote',null);
		cookie('user_promote',null);
		echo "<script>window.location.href='".__ROOT__."/Win/Logo/login.html';</script>";
	}
		 
	 /**
	 *
	 * @前台更新
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */	
	public function tfUpda(){
		$user=M('user');
		$users=$user->field('username,password')->where('id='.$this->_session('user_uid'))->find();
		if($this->_session('user_verify')==MD5($users['username'].DS_ENTERPRISE.$users['password'].DS_EN_ENTERPRISE)){
			$this->upda();
		}else{
			echo '非法操作，网警已介入！';
		}
	}
	
	/**
	 * @前台验证
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	protected function homeVerify(){
		
		if($this->_session('user_uid')){
			$user=M('user');
			$users=D('User')->relation('userinfo')->where('id='.$this->_session('user_uid'))->find();
			
			if($this->_session('user_verify') !== MD5($users['username'].DS_ENTERPRISE.$users['password'].DS_EN_ENTERPRISE)){
				session('user_uid',null);
				session('user_name',null);
				session('user_verify',null);
				echo "<script>window.location.href='".__ROOT__."/Win/Logo/login';</script>";
				
				
			}
			
		}else{
			echo "<script>window.location.href='".__ROOT__."/Win/Logo/login';</script>";
		}
	 }
}
?>