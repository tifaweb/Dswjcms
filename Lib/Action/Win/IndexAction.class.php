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
class IndexAction extends WinAction {
	//投资页
    public function invest(){
		
		$this->assign('title','我要投资');
		$Borrowing = D('Borrowing');
		import('ORG.Util.Page');// 导入分页类
			$where='(state=1 or state=10 or state=11)';
		if($this->_get('search')){
			$where.=" and `title` LIKE '%".$this->_get('search')."%'";
		}
		$count      = $Borrowing->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数数
		$show       = $Page->show();// 分页显示输出
		$borrow=$this->borrow_unicoms($where,$Page->firstRow.','.$Page->listRows);
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
		$state=$this->_param('state')==1?'(state=1 or state=10)':"state =".($this->_param('state'));	//借款状态
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
		$borrow=$this->borrow_unicoms($where,$Page->firstRow.','.$Page->listRows);
		if(!$borrow){
			echo '<div class="invest_loading"><div>暂无数据</div> </div>';
			exit;
		}
		$content.='<ul class="thumbnails">';
		foreach($borrow as $id=>$lt){
			if($lt['type']==7){
				$content='
				 <li class="span6">
				<a href="'.__ROOT__.'/Win/Loan/loan/id/'.$lt['id'].'.html">
				<div class="thumbnail">
				  <img src="/Public/uploadify/uploads/mark/'.$lt['img'].'" style="width:150px;height:150px;"/>
				  <div class="caption">
					<h6>'.$lt['title'].'</h6>
						<div class="progress" style="margin-bottom:0px;"  data-rel="tooltip" title="'.$lt['flow_ratio'].'%">
						  <div class="bar" style="width: '.$lt['flow_ratio'].'%;"></div>
						</div>
				  </div>
				</div>
				</a>
			  </li>
			';
			}else{
			$content.='
			
			<li class="span6">
				<a href="'.__ROOT__.'/Win/Loan/loan/id/'.$lt['id'].'.html">
				<div class="thumbnail">
				  <img src="/Public/uploadify/uploads/mark/'.$lt['img'].'" style="width:150px;height:150px;"/>
				  <div class="caption">
					<h6>'.$lt['title'].'</h6>
				  ';
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
                </div>
				</div>
				</a>
			  </li>';
		}
		}
		$content.='
		<div class="pagination pagination-centered loan_page">
        <ul>'.$show.'</ul>
        </div>
		</ul>
		
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

	
	//计算器
    public function counter(){	
	$this->assign('title','计算器');
		$endjs='
			function switchover(id){
				if(id==1){
					$("#deadline1").hide();$("#deadline").show();$("#deadline").attr("name","deadline");$("#deadline1").attr("name","");
				}else{
					$("#deadline1").show();$("#deadline").hide();$("#deadline1").attr("name","deadline");$("#deadline").attr("name","");
				}
			}
			function counterOclick(){
				var money=$("[name= \'money\']").val();
				var rate=$("[name= \'rate\']").val();
				var units=$("[name= \'unit\']").val();
				var deadline=$("[name= \'deadline\']").val();
				var way=$("[name= \'way\']").val();
				$("#sounter_result").html(\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div></div>\');
				$("#sounter_result").load("__URL__/counterAdd", {money:money,rate:rate,units:units,deadline:deadline,way:way});
			}
		';
		$this->assign('endjs',$endjs);
		$this->display();
    }
	
	//显示结果
    public function counterAdd(){	
		$counters=$this->counters($this->_post('money'),$this->_post('rate'),$this->_post('deadline'),$this->_post('units'),$this->_post('way'));
		if($this->_post('money')<1){
			echo '
				<div class="invest_loading">
					<div>没有数据</div>
				</div>
				';
				exit;
		}
		$total=$counters['total'];
		$interest=$counters['interest'];
		unset($counters['total']);
		unset($counters['interest']);
		foreach($counters as $id=>$i){
			$ajax.='
				 <tr>
                    <td>'.($id+1).'</td>
                    <td>'.$i['refund'].'</td>
                    <td>'.$i['capital'].'</td>
                    <td>'.$i['interest'].'</td>
                    <td>'.$i['remaining'].'</td>
                 </tr>
					';
		}
		echo " <div class='couter_total'><span>累计支付利息：<i>".number_format($interest,2,'.',',')."元</i></span><span>累计还款总额：<i>".number_format($total,2,'.',',')."元</i></span></div>";
		echo '
			<table class="table table-striped">
				<thead>
					<tr>
						<th class="span2">期数</th>
						<th class="span3">还款总额</th>
						<th class="span2">还款本金</th>
						<th class="span2">还款利息</th>
						<th class="span3">还需还款本金</th>
					</tr>
				</thead>
				
				<tbody>	
			';
		echo $ajax;
		echo '
			</tbody>
        </table>
			';
    }
}
?>