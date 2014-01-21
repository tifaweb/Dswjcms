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
class LoanAction extends WinAction {
	//投资单页
    public function loan(){
		$this->WxVerify();	//验证权限
		$this->assign('title','我要投资');
		$id=(int)$this->_get('id');	
		$borrow=R('Sharing/borrow_information',array($id));
		$borr=M('borrowing');
		$borrow[0]['amount_total']=$borr->where('uid='.$borrow[0]['uid'].' and state=9')->Sum();//借入总金额
		$borrow[0]['amount_total']=$borrow[0]['amount_total']?$borrow[0]['amount_total']:0;
		$borrow[0]['amount_number']=$borr->where('uid='.$borrow[0]['uid'].' and state=9')->Count();//成功借入数
		$borrow[0]['standard']=$borr->where('uid='.$borrow[0]['uid'].' and state=4')->Count();//流标数
		$borrow[0]['stay_number']=$borr->where('uid='.$borrow[0]['uid'].' and state=7')->Count();//待还
		$borrow[0]['overdue_number']=$borr->where('uid='.$borrow[0]['uid'].' and state=8')->Count();//逾期
		$this->assign('money',$money);
		$this->assign('borrow',$borrow);
		$img=array_filter(explode(",",$borrow[0]['data']));
		$this->assign('img',$img);
		$money=M('money');
		$money=$money->field('total_money,available_funds,freeze_funds')->where('`uid`='.$this->_session('user_uid'))->select();
		$money=reset($money);
		$this->assign('money',$money);
		$head=$this->headPortrait('./Public/FaustCplus/php/img/big_user_'.$borrow[0]['uid'].'.jpg');
		$this->assign('heads',$heads);
		$endjs.='
			function changeVerify(){
				var timenow = new Date().getTime();
				document.getElementById("verifyImg").src="'.__APP__.'/Public/verify/"+timenow;
			}';
		if($borrow[0]['type']==7){
			$endjs.='
			/*
			 * @流转份数事件
			 * @uid			1减2加3最大金额4键入时
			 * @gpfd		还可认购份数
			 * @yu			余额
			 * @mins		每份认购金额
			 */
			 function Totalprice(uid,gpfd,yu,mins){	
				var ordvalue=$("#price").val();						//获取输入框的值
				var strP=/^\d+$/;										//数字正则
				var surplus="";
				if(yu>(gpfd*mins)){
					surplus=gpfd;
				}else{
					surplus=Math.floor(yu/mins);	//四舍五入，舍掉
				}
				if(uid==1){		//减
					var cut=parseInt(ordvalue)-1;							//减1
						if(parseInt(ordvalue) <= 1){
								$("#price").val(1);
						}else{
								$("#price").val(cut);
						}
				}else if(uid==2){	//加
					var add=parseInt(ordvalue)+1;								//加1
						if(parseInt(ordvalue) >= surplus){
							$("#price").val(surplus);
						}else{
							$("#price").val(add);
						}
				}else if(uid==3){	//最大金额
					$("#price").val(surplus);
				}else if(uid==4){	//键入时
					if(strP.test(ordvalue)){		//如果是数字
						if(parseInt(ordvalue) <= 1){
							$("#price").val(1);
							var ordvalue=1;
						}else if(parseInt(ordvalue) >= surplus){
							$("#price").val(surplus);
						}
					}else{		//如果不是数字
						$("#price").val(1);
					}
				}
			 }
			 
			 /*
			 * @流转期限事件
			 * @uid			1减2加3最大金额4键入时
			 * @min			最低认购期限
			 * @flow		流转期限
			 */
			 function Totalmonth(uid,min,flow){	
				var ordvalue=$("#month").val();						//获取输入框的值
				var strP=/^\d+$/;										//数字正则
				if(uid==1){		//减
					var cut=parseInt(ordvalue)-1;							//减1
						if(parseInt(ordvalue) <= 1){
								$("#month").val(1);
						}else{
								$("#month").val(cut);
						}
				}else if(uid==2){	//加
					var add=parseInt(ordvalue)+1;								//加1
						if(parseInt(ordvalue) >= flow){
							$("#month").val(flow);
						}else{
							$("#month").val(add);
						}
				}else if(uid==3){	//最大金额
					$("#month").val(flow);
				}else if(uid==4){	//键入时
					if(strP.test(ordvalue)){		//如果是数字
						if(parseInt(ordvalue) <= 1){
							$("#month").val(1);
							var ordvalue=1;
						}else if(parseInt(ordvalue) >= flow){
							$("#month").val(flow);
						}
					}else{		//如果不是数字
						$("#month").val(1);
					}
				}
			 }
		';
		}else{
			$endjs.='
			/*
			 * @投标金额事件
			 * @uid			1减2加3最大金额4键入时
			 * @gpfd		借款还需金额
			 * @yu			余额
			 * @surplus		可投金额
			 * @maxs		最大金额
			 * @mins		最小金额
			 */
			 function Totalprice(uid,gpfd,yu,maxs,mins){		
				var ordvalue=$("#price").val();						//获取输入框的值
				var strP=/^\d+$/;										//数字正则
				var surplus="";
				var smalls="";
				if(maxs>0){
					if(yu>gpfd && yu>maxs){
						if(gpfd>maxs){
							surplus=maxs;
						}else{
							surplus=gpfd;
						}
					}else if(yu<=gpfd && yu<=maxs){
						surplus=yu;
					}else if(yu<=gpfd && yu>=maxs){
						surplus=maxs;
					}else{
						surplus=gpfd;
					}
				}else{
					if(yu>gpfd){
						surplus=gpfd;
					}else{
						surplus=yu;
					}
				}
				if(mins>1 && mins>gpfd){
					smalls=gpfd;
				}else if(mins>1 && mins<=gpfd){
					smalls=mins;
				}else{
					smalls=1;
				}
				if(uid==1){		//减
					var cut=parseInt(ordvalue)-1;							//减1
						if(parseInt(ordvalue) <= smalls){
								$("#price").val(smalls);
						}else{
								$("#price").val(cut);
						}
				}else if(uid==2){	//加
					var add=parseInt(ordvalue)+1;								//加1
						if(parseInt(ordvalue) >= (surplus)){
								if(jcpkc==smalls){
									$("#price").val(smalls);
								}else{
								$("#price").val(surplus);
								}
						}else{
							$("#price").val(add);
						}
				}else if(uid==3){	//最大金额
					$("#price").val(Math.floor(surplus));
				}else if(uid==4){	//键入时
					if(strP.test(ordvalue)){		//如果是数字
						if(parseInt(ordvalue) <= smalls){
							$("#price").val(smalls);
							var ordvalue=smalls;
						}else if(parseInt(ordvalue) >= surplus){
							$("#price").val(surplus);
						}
					}else{		//如果不是数字
						$("#price").val(smalls);
					}
				}
			 }
		';
		}
		$endjs.='timeCount("limittimes");';
		$this->assign('endjs',$endjs);
		 //标题、关键字、描述
		$integral=$borr->field('title')->where('id='.$id)->find();
		$integral['link']=1;
		$this->assign('si',$integral);
		$integral['title']=','.$integral['title'];
		$this->assign('so',$integral);
		$head="<script src='__PUBLIC__/js/timecount.js'></script>";
		$this->assign('head',$head);
		$active['loan']='active';
		$this->assign('active',$active);
		$this->display();
	}
	
	//我的账户
    public function account(){
		$this->WxVerify();	//验证权限
		$this->assign('title','我的账户');
		$list=R('Sharing/user_details');	
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
}
?>