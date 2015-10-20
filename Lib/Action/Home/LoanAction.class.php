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
class LoanAction extends HomeAction {
//-------------投资页--------------
	public function index(){
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['loan']='active';
		$this->assign('active',$active);
		$dirname=F('dirname');
		$endjs.='
//AJAX分页
$(function(){ 
	$(".pagination-centered a").click(function(){ 
		var loading=\'<div class="invest_loading"><div><img src="./Public/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
		$(".loan_ajax").html(loading);
		$.get($(this).attr("href"),function(data){ 
			$("body").html(data); 
		}) 
		return false; 
	}) 
}) 
	
//条件选择数据保存
function integral(type,value){
	var types=$("#type").val();	//借款类型
	var states=$("#state").val();	//借款状态
	var loading=\'<div class="invest_loading"><div><img src="./Public/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	';
		$endjs.='$(".loan_ajax").html(loading);
		if(type=="type"){
			$("#type").val(value);	
			$("#types li").removeClass("active");
			$(".loan_ajax").load("__URL__/loanAjax", {type:value,state:states});
		}
		if(type=="state"){
			$("#state").val(value);	
			$("#states li").removeClass("active");
			$(".loan_ajax").load("__URL__/loanAjax", {type:types,state:value});
		}
		
	}
			';
		$this->assign('endjs',$endjs);
		$head="<script src='__PUBLIC__/js/timecount.js'></script>";
		$this->assign('head',$head);
		//名词解释
		$explanation=$this->someArticle(28,5);
		$this->assign('explanation',$explanation);
		//平台公告
		$new=$this->someArticle(32,5);
		$this->assign('new',$new);
		//帮助中心
		$help=$this->someArticle(31,5);
		$this->assign('help',$help);
		$this->display();
    }
	
	//标AJAX显示
	public function loanAjax(){
		
		$Borrowing = D('Borrowing');
		import('ORG.Util.Page');// 导入分页类
		$type=$this->_param('type')==0?'':"`type` =".($this->_param('type')-1);	//借款类型
		$state=$this->_param('state')==0?'`state`>0 and `state` !=12 and `state` !=13 and `state` !=14 and `state` !=16 and `state` !=8':"`state` =".($this->_param('state'));	//借款状态
		if($type || $state){
			
			$type=$type?$type." and ":'';
			if($this->_param('state')==1){
				$state.=" or `state`=2";
			}
			$state=$state?$state." and ":'';
			$where=$type.$state;
			
		}
		$where.='min>1';
		$count      = $Borrowing->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$borrow=$this->borrow_unicoms($where,$Page->firstRow.','.$Page->listRows,'`stick` DESC,`time` DESC');
		if(!$borrow){
			echo '<div class="invest_loading"><div>暂无数据</div> </div>';
			exit;
		}
		
			foreach($borrow as $id=>$lt){
				//普通标
					$content.='
					<!-- 普通标 state--> 
					<div class="project-summary wall" style="position: relative;">
					';
					if($lt['state']==1 or $lt['state']==2 or $lt['state']==10){
					}else{
						$content.='<div class="bid-completed-stamp"></div>';
					}
					$content.='
						<div class="row-fluid">
							<div class="span8 ">
								<div style="min-height: 75px;">
									<h4  class="index_h4">
										<a href='.__ROOT__.'"/Loan/invest/'.$lt['id'].'.html" data-rel="tooltip" title="'.$lt['title'].'">'.$lt['title'].'</a>
									</h4>
									<p class="project-tags">
										<span class="label label-success">
					';
					if($lt['state']==1 or $lt['state']==2 or $lt['state']==10){
						$content.='投标中';
					}else{
						$content.=$lt['state_name'];
					}
					$content.='
						</span>
						<span class="tag" data-rel="tooltip" title="'.$lt['type_name'].'"><i class="icon icon-darkgray icon-tag"></i>'.$lt['type_name'].'</span>
						';
					if($lt['candra']==1){
						$content.='<span class="tag" data-rel="tooltip" title="按天计算"><i class="icon icon-darkgray icon-clock"></i>天</span>';
					}
					if($lt['reward_type']>0){
						$content.='<span class="tag" data-rel="tooltip" title="奖励：'.$lt['reward'].'"><i class="icon icon-darkgray icon-archive"></i>奖励</span>';
					}
					if($lt['stick']==1){
						$content.='<span class="tag" data-rel="tooltip" title="推荐"><i class="icon icon-darkgray icon-lightbulb"></i>推荐</span>';
					}
					if($lt['code']==1){
						$content.='<span class="tag" data-rel="tooltip" title="需要密码"><i class="icon icon-darkgray icon-locked"></i>密码</span>';
					}
					$content.='
					</p>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div class="pull-left" style="margin-right: 10px;">
					';
					if($lt['state']==1 or $lt['state']==2 or $lt['state']==10){
						$content.='<a class="btn btn-large btn-primary btn-details" href="'.__ROOT__.'/Loan/invest/'.$lt['id'].'.html">我要投资</a>';
					}else{
						$content.='<a class="btn btn-large btn-details" >'.$lt['state_name'].'</a>';
					}
					$content.='
					</div>
                        <div class="pull-left">
                            <div class="project-progress">	
					';
					if($lt['state']==1 or $lt['state']==2 or $lt['state']==10){
						$content.='
						<div class="progress progress-striped active">
                                        <div class="bar" style="width: '.$lt['ratio'].'%;"></div>
                                    </div>
						';
					}else{
						$content.='
						<div class="progress progress-striped">
                                        <div class="bar" style="width: '.$lt['ratio'].'%;"></div>
                                    </div>
						';
					}
					$content.='</div>';
					if($lt['state']==1 or $lt['state']==2 or $lt['state']==10){
						$content.='<p class="project-progress-desc">已有'.$lt['bid_records_count'].'笔投标</p>';
					}else{
						$content.='<p class="project-progress-desc">'.$lt['way'].'</p>';
					}
					$content.='
					</div>
									</div>
								</div>
							</div>
							<div class="span4">
								<ul class="project-summary-items">
									<li><span class="title">融资金额</span>'.number_format($lt['money'],2,'.',',').' 元</li>
									<li><span class="title">年化收益</span> 
										<span class="important data-tips">
										'.$lt['rates'].'%
										</span>
									</li>
									<li><span class="title">融资期限</span>
										<span class="data-tips">
											'.$lt['deadlines'].'
										</span>
									</li>
									<li><span class="title">剩余时间</span>
										 <span class="data-tips">';
					if($lt['state']==7 or $lt['state']==8 or $lt['state']==9 ){
						$content.='已完成';
					 }else{
						$content.=' <span id="limittime'.$lt['id'].'" endtime="'.date("Y/m/d H:i:s",$lt['endtime']).'"></span>';
					 }
					$content.=' </span>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<script>timeCount("limittime'.$lt['id'].'");</script> 
					<!-- 普通标 end-->
					';
			}
			$content.='
			<div class="pagination pagination-centered">
			<ul>'.$show.'</ul>
			</div>
			<script>
			//AJAX分页
			$(function(){ 
				$(".pagination-centered a").click(function(){ 
					var loading=\'<div class="invest_loading"><div><img src="../Public/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
					$(".loan_ajax").html(loading);
					$.get($(this).attr("href"),function(data){ 
						$(".loan_ajax").html(data); 
					}) 
					return false; 
				}) 
			}) 		
			</script>';
		
		echo $content;
	}

//-------------投资详细页--------------
	public function invest(){
		$id=(int)$this->_get('id');	
		$borrow=$this->borrow_information($id);
		if($borrow[0]['id']<1){
			 $this->error('误操作！');
		}
		$Certification=D('Certification');
		$certification=$Certification->where('`uid`='.$borrow[0]['uid'].' and `state`=2')->select();
		foreach($certification as $cid=> $cert){
			$certi.=implode(",",json_decode($cert['cards'],true)).',';
		}
		$certi=array_filter(explode(",",$certi));
		
		$this->assign('certi',$certi);
		
		$borr=M('borrowing');
		$borrow[0]['amount_total']=$borr->where('uid='.$borrow[0]['uid'].' and state=9')->Sum();//借入总金额
		$borrow[0]['amount_total']=$borrow[0]['amount_total']?$borrow[0]['amount_total']:0;
		$borrow[0]['amount_number']=$borr->where('uid='.$borrow[0]['uid'].' and state=9')->Count();//成功借入数
		$borrow[0]['standard']=$borr->where('uid='.$borrow[0]['uid'].' and state=4')->Count();//流标数
		$borrow[0]['stay_number']=$borr->where('uid='.$borrow[0]['uid'].' and state=7')->Count();//待还
		$coverdue=M('coverdue');
		$borrow[0]['overdue_number']=$coverdue->where('uid='.$borrow[0]['uid'])->Count();//逾期
		$this->assign('borrow',$borrow);
		$data=explode(";",$borrow[0]['data']);
		$pact=array_filter(explode(",",$data[0]));
		$indeed=array_filter(explode(",",$data[1]));
		unset($data);
		$this->assign('img',$pact);
		$this->assign('imgs',$indeed);
		
		
		$money=M('money');
		$money=$money->field('total_money,available_funds,freeze_funds')->where('`uid`='.$this->_session('user_uid'))->find();
		$this->assign('money',$money);
		$userinfo=M('userinfo');
		$userin=$userinfo->field('assure')->where('uid='.$this->_session('user_uid'))->find();
		$this->assign('userin',$userin);
		$heads=$this->headPortrait('./Public/FaustCplus/php/img/big_user_'.$borrow[0]['uid'].'.jpg');
		$this->assign('heads',$heads);
		$endjs.='
			function changeVerify(){
				var timenow = new Date().getTime();
				document.getElementById("verifyImg").src="'.__APP__.'/Public/verify/"+timenow;
			}';
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
								$("#price").val(surplus);
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
							if(surplus>smalls){	//如果可投金额比最小的大
								$("#price").val(surplus);
							}else{
								$("#price").val(smalls);
							}
						}
					}else{		//如果不是数字
						$("#price").val(smalls);
					}
				}
			 }
		';
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
	
}