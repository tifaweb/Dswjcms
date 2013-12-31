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
		$Borrowing = D('Borrowing');
		import('ORG.Util.Page');// 导入分页类
			$where='(state=1 or state=10 or state=11)';
		if($this->_get('search')){
			$where.=" and `title` LIKE '%".$this->_get('search')."%'";
		}
		$count      = $Borrowing->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数数
		$show       = $Page->show();// 分页显示输出
		$borrow=$this->borrow_unicoms($where,$Page->firstRow.','.$Page->listRows,'`stick` DESC,`time` DESC');
		$this->assign('borrow',$borrow);
		$this->assign('page',$show);// 赋值分页输出
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['loan']='active';
		$this->assign('active',$active);
		$endjs='
//AJAX分页
$(function(){ 
	$(".pagination-centered a").click(function(){ 
		var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
		$(".loan_top").html(loading);
		$.get($(this).attr("href"),function(data){ 
			$("body").html(data); 
		}) 
		return false; 
	}) 
}) 
//积分商城条件选择数据保存
function integral(type,value){
	var types=$("#type").val();	//借款类型
	var states=$("#state").val();	//借款状态
	var scopes=$("#scope").val();	//还款方式
	var classifys=$("#classify").val();	//借款期限
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".loan_top").html(loading);
	if(type=="type"){
		$("#type").val(value);	
		$("#types dd").removeClass("pitch");
		$(".loan_top").load("__URL__/loanAjax", {type:value,state:states,scope:scopes,classify:classifys});
	}
	if(type=="state"){
		$("#state").val(value);	
		$("#states dd").removeClass("pitch");
		$(".loan_top").load("__URL__/loanAjax", {type:types,state:value,scope:scopes,classify:classifys});
	}
	if(type=="scope"){
		$("#scope").val(value);	
		$("#scopes dd").removeClass("pitch");
		$(".loan_top").load("__URL__/loanAjax", {type:types,state:states,scope:value,classify:classifys});	
	}
	if(type=="classify"){
		$("#classify").val(value);	
		$("#classifys dd").removeClass("pitch");
		$(".loan_top").load("__URL__/loanAjax", {type:types,state:states,scope:scopes,classify:value});
	}
	
}
		';
		$this->assign('endjs',$endjs);
		$head="<script src='__PUBLIC__/js/timecount.js'></script>";
		$this->assign('head',$head);
		
		$this->display();
    }
	
	//标AJAX显示
	public function loanAjax(){
		$Borrowing = D('Borrowing');
		import('ORG.Util.Page');// 导入分页类
		$type=$this->_param('type')==0?'':"type =".($this->_param('type')-1);	//借款类型
		$state=$this->_param('state')==0?'(state=1 or state=10)':"state =".($this->_param('state'));	//借款状态
		$classify=$this->_param('classify')==0?'':"way =".($this->_param('classify')-1);	//还款方式
		$scope=$this->_param('scope')==0?'':"candra =".($this->_param('scope')-1);	//借款期限
		if($type || $state || $classify || $scope){
			$type=$type?$type." and ":'';
			$state=$state?$state." and ":'';
			$scope=$scope?$scope." and ":'';
			$classify=$classify?$classify." and ":'';
			$where=$type.$state.$scope.$classify;
			
		}
		
		//$where.='(state=1 or state=10)';
		$where.='min>1';
		$count      = $Borrowing->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$borrow=$this->borrow_unicoms($where,$Page->firstRow.','.$Page->listRows,'`stick` DESC,`time` DESC');
		if(!$borrow){
			echo '<div class="invest_loading"><div>暂无数据</div> </div>';
			exit;
		}
		$content.='
				<dl>
        	<dd class="span2 loan_img">图片</dd>
            <dd class="span2">标题/借款者/所在地</dd>
            <dd class="span2">金额/利率/用途</dd>
            <dd class="span2">进度/已投/剩余</dd>
            <dd class="span2">等级/期限/付款方式</dd>
            <dd class="span2">&nbsp;</dd>
        </dl>';
		foreach($borrow as $id=>$lt){
			if($lt['type']==7){
				$content.='
		<dl class="loan_nr">
        	<dd class="span2 loan_img">
                <a href="'.__ROOT__.'/Loan/invest/'.$lt['id'].'.html">
                    <img src="/Public/uploadify/uploads/mark/'.$lt['img'].'" style="width:100px;height:100px;"/>
                </a>
            </dd>
            <dd class="span2">
            <ul>
            	<li>
                    <a href="'.__ROOT__.'/Loan/invest/'.$lt['id'].'.html" data-rel="tooltip" title="'.$lt['title'].'">'.$lt['title'].'</a>
                    <i class="mark-ico-color mark-ico-flow" data-rel="tooltip" title="'.$lt['type_name'].'"></i>';
			if($lt['candra']==1){ 
				$content.='<i class="mark-ico-color mark-ico-day" data-rel="tooltip" title="天标"></i>';
			}
			if($lt['reward_type']>0){ 
				$content.='<i class="mark-ico-color mark-ico-prize" data-rel="tooltip" title="'.$lt['rewards'].'"></i>';
			}
			if($lt['stick']==1){ 
				$content.='<i class="mark-ico-color mark-ico-push" data-rel="tooltip" title="推荐标"></i>';
			}
			if($lt['code']==1){ 
				$content.='<i class="icon icon-color icon-locked" data-rel="tooltip" title="密码标"></i>';
			}
			$content.='  
                </li>
                <li>
                    发布者:'.$lt['username'].'    
                </li>
                <li>
                    所在地：'.$lt['location'].'
                </li>
            </ul>
            </dd>
             <dd class="span2">
            <ul>
            	<li>
                    流转金额：'.$lt['money'].'元
                </li>
                <li>
                    利率：'.$lt['rates'].'%     
                </li>
                <li>
                    可认购数：'.$lt['subscribe'].'份     
                </li>
            </ul>
            </dd>
            <dd class="span2">
            <ul>
            	<li>
                    <div class="progress" style="margin-bottom:0px;"  data-rel="tooltip" title="'.$lt['flow_ratio'].'%">
                      <div class="bar" style="width: '.$lt['flow_ratio'].'%;"></div>
                    </div>
                </li>
                <li>
                    正在流转 '.$lt['flows'].' 份   
                </li>
                <li>
                    已回购 '.$lt['repos'].' 份
                </li>
            </ul>
            </dd>
           <dd class="span2">
            <ul>
            	<li>
                    <img src="/Public/uploadify/uploads/grade_img/'.$lt['member_total_img'].'" title="'.$lt['member_total_name'].'" data-rel="tooltip"/>
                </li>
                <li>
                    流转期限：'.$lt['flow_deadlines'].'
                </li>
                <li>
                    '.$lt['way'].'     
                </li>
            </ul>
            </dd>
            <dd class="span2 loan_sumb">';
			if($lt['state']==1 or $lt['state']==10){
				$content.='<a class="btn btn-primary loan_btn" href="'.__ROOT__.'/Loan/invest/'.$lt['id'].'.html">立即认购</a>';
			}else{
				$content.='<a class="btn loan_btn" >'.$lt['state_name'].'</a>';
			}
			$content.='
            </dd>
        </dl>
				';
			}else{
			$content.='
		<dl class="loan_nr">
        	<dd class="span2 loan_img">
                <a href="'.__ROOT__.'/Loan/invest/'.$lt['id'].'.html">
                    <img src="/Public/uploadify/uploads/mark/'.$lt['img'].'" style="width:100px;height:100px;"/>
                </a>
            </dd>
            <dd class="span2">
            <ul>
            	<li>
                    <a href="'.__ROOT__.'/Loan/invest/'.$lt['id'].'.html" data-rel="tooltip" title="'.$lt['title'].'">'.$lt['title'].'</a>';
                    switch ($lt['type']){
						case '0':
						$content.='<i class="mark-ico-color mark-ico-seconds" data-rel="tooltip" title="'.$lt['type_name'].'"></i>';
						break;
						case '1':
						$content.='<i class="mark-ico-color mark-ico-bet" data-rel="tooltip" title="'.$lt['type_name'].'"></i>';
						break;
						case '2':
						$content.='<i class="mark-ico-color mark-ico-bet" data-rel="tooltip" title="'.$lt['type_name'].'"></i>';
						break;
						case '3':
						$content.='<i class="mark-ico-color mark-ico-net" data-rel="tooltip" title="'.$lt['type_name'].'"></i>';
						break;
						case '4':
						$content.='<i class="mark-ico-color mark-ico-letter" data-rel="tooltip" title="'.$lt['type_name'].'"></i>';
						break;
						case '5':
						$content.='<i class="mark-ico-color mark-ico-bear" data-rel="tooltip" title="'.$lt['type_name'].'"></i>';
						break;
						case '6':
						$content.='<i class="mark-ico-color mark-ico-group" data-rel="tooltip" title="'.$lt['type_name'].'"></i>';
						break;
					}
					if($lt['candra']==1){
						$content.='<i class="mark-ico-color mark-ico-day" data-rel="tooltip" title="天标"></i>';
					}
					if($lt['reward_type']>0){
						$content.='<i class="mark-ico-color mark-ico-prize" data-rel="tooltip" title="'.$lt['rewards'].'"></i>';
					}
					if($lt['stick']==1){
						$content.='<i class="mark-ico-color mark-ico-push" data-rel="tooltip" title="推荐标"></i>';
					}
					if($lt['code']==1){
						$content.='<i class="icon icon-color icon-locked" data-rel="tooltip" title="密码标"></i>';
					}
                $content.='
                </li>
                <li>
                    发布者:'. $lt['username'].'    
                </li>
                <li>
                    所在地：'. $lt['location'].'
                </li>
            </ul>
            </dd>
            <dd class="span2">
            <ul>
            	<li>
                    金额：'.$lt['money'].'元
                </li>
                <li>
                    利率：'.$lt['rates'].'%     
                </li>
                <li>
                    '.$lt['use'].'     
                </li>
            </ul>
            </dd>
            <dd class="span2">
            <ul>
            	<li>';
				if($lt['state']==10){
					$content.='<div class="progress" style="margin-bottom:0px;"  data-rel="tooltip" title="'. $lt['ratios'].'%">
                      <div class="bar" style="width: '. $lt['ratios'].'%;"></div>
                    </div>';
				}else{
					$content.='
						<div class="progress" style="margin-bottom:0px;"  data-rel="tooltip" title="'. $lt['ratio'].'%">
                      <div class="bar" style="width: '. $lt['ratio'].'%;"></div>
                    </div>
					';
				}
				$content.='
                </li>
                <li>
                    已有'. $lt['bid_records_count'].'笔投标   
                </li>
                <li>';
                 if($lt['state']==7 or $lt['state']==8 or $lt['state']==9 ){
                    $content.='<span style="color:#F00">已完成</span>';
				 }else{
				 	$content.=' <span id="limittime'.$lt['id'].'" endtime="'.date("Y/m/d H:i:s",$lt['endtime']).'"></span>';
				 }
			$content.='
                </li>
            </ul>
            </dd>
           <dd class="span2">
            <ul>
            	<li>
                    <img src="/Public/uploadify/uploads/grade_img/'. $lt['member_total_img'].'" title="'. $lt['member_total_name'].'" data-rel="tooltip"/>
                </li>
                <li>
                    '. $lt['deadlines'].'
                </li>
                <li>
                    '. $lt['way'].'     
                </li>
            </ul>
            </dd>
            <dd class="span2 loan_sumb">
			';
			if($lt['state']==1 or $lt['state']==10){
              $content.='<a class="btn btn-primary loan_btn" href="'.__ROOT__.'/Loan/invest/'. $lt['id'].'.html">'. $lt['state_name'].'</a>';
			}else{
              $content.='<a class="btn loan_btn" >'. $lt['state_name'].'</a>';
			}
           $content.='
            </dd>
        </dl>
        <script>timeCount("limittime'.$lt['id'].'");</script> 
		';
		}
		}
		$content.='
		<div class="pagination pagination-centered loan_page">
        <ul>'.$show.'</ul>
        </div>
		<script>
		//AJAX分页
		$(function(){ 
			$(".pagination-centered a").click(function(){ 
				var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
				$(".loan_top").html(loading);
				$.get($(this).attr("href"),function(data){ 
					$(".loan_top").html(data); 
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
		$borrow=R('dswjjd://Sharing/borrow_information',array($id));
		$borr=M('borrowing');
		$borrow[0]['amount_total']=$borr->where('uid='.$borrow[0]['uid'].' and state=9')->Sum();//借入总金额
		$borrow[0]['amount_total']=$borrow[0]['amount_total']?$borrow[0]['amount_total']:0;
		$borrow[0]['amount_number']=$borr->where('uid='.$borrow[0]['uid'].' and state=9')->Count();//成功借入数
		$borrow[0]['standard']=$borr->where('uid='.$borrow[0]['uid'].' and state=4')->Count();//流标数
		$borrow[0]['stay_number']=$borr->where('uid='.$borrow[0]['uid'].' and state=7')->Count();//待还
		$borrow[0]['overdue_number']=$borr->where('uid='.$borrow[0]['uid'].' and state=8')->Count();//逾期
		if($borrow[0]['type']==7){//流转标的数据
			if($borrow[0]['candra']==0){	//获取用户选择的是月标还是天标
				$limtime=floor(($borrow[0]['limittime']-time())/30/86400);	//还可认购时间
				if($limtime>=$borrow[0]['flow_deadline']){	//当还可认购时间大于等于流转期限时（因每个月按30天算，会有时间差）
					$limtime=$limtime-1;
				}
			}else{
				$limtime=floor(($borrow[0]['limittime']-time())/86400);	//还可认购时间
			}
			if($limtime>=$borrow[0]['flow_deadline']){//借款可认购总期限比流转期限大
				$borrow[0]['also_deadline']=$borrow[0]['flow_deadline']-1;//还可认购期限
			}else{
				$borrow[0]['also_deadline']=$limtime;
			}
		}
		
		$this->assign('borrow',$borrow);
		$img=array_filter(explode(",",$borrow[0]['data']));
		$this->assign('img',$img);
		$money=M('money');
		$money=$money->field('total_money,available_funds,freeze_funds')->where('`uid`='.$this->_session('user_uid'))->select();
		$money=reset($money);
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
}