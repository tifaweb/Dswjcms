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
class IndexAction extends HomeAction {
    public function index(){
		$this->copyright();
		$where='state=1 or state=10';
		$borrow=$this->borrow_unicoms($where,'','`stick` DESC,`time` DESC');
		$this->assign('borrow',$borrow);
		$shuffling = M('shuffling');
		$shufflings=$shuffling->field('title,img')->order('`order` ASC')->select();
		$this->assign('shuff',$shufflings);
		$head="<link href='__PUBLIC__/css/jslides.css' rel='stylesheet'>";
		$this->assign('head',$head);
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['index']='active';
		$this->assign('active',$active);
		//各种标的比例
		$borrowing = M('borrowing');
		$array[1]=$borrowing->where('type=0')->count();	//秒还标
		$array[2]=$borrowing->where('type=1')->count();	//抵押标
		$array[3]=$borrowing->where('type=2')->count();	//质押标
		$array[4]=$borrowing->where('type=3')->count();	//净值标
		$array[5]=$borrowing->where('type=4')->count();	//信用标
		$array[6]=$borrowing->where('type=5')->count();	//担保标
		$array[7]=$borrowing->where('type=7')->count();	//流转标
		//注册性别
		$userinfo = M('userinfo');
		$sex[1]=$userinfo->where('gender=0')->count();	//男
		$sex[2]=$userinfo->where('gender=1')->count();	//男
		$endjs='
		//首页轮播
		$(function(){$("#kinMaxShow").kinMaxShow();});
		//pie chart
	var data = [
	{ label: "秒还标",  data: '.$array[1].'},
	{ label: "抵押标",  data: '.$array[2].'},
	{ label: "质押标",  data: '.$array[3].'},
	{ label: "净值标",  data: '.$array[4].'},
	{ label: "信用标",  data: '.$array[5].'},
	{ label: "担保标",  data: '.$array[6].'},
	{ label: "流转标",  data: '.$array[7].'}
	];
	
	var datas = [
	{ label: "男",  data: '.$sex[1].'},
	{ label: "女",  data: '.$sex[2].'}
	];
	if($("#piecharts").length)
	{
		$.plot($("#piecharts"), data,
		{
			series: {
					pie: {
							show: true
					}
			},
			grid: {
					hoverable: true,
					clickable: true
			},
			legend: {
				show: false
			}
		});
	}
	
	//donut chart
	if($("#donutcharts").length)
	{
		$.plot($("#donutcharts"), datas,
		{
				series: {
						pie: {
								innerRadius: 0.5,
								show: true
						}
				},
				legend: {
					show: false
				}
		});
	}
		';
		$this->assign('endjs',$endjs);
		$this->display();
    }
//-------计算器
	public function counter(){
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['counter']='active';
		$this->assign('active',$active);
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
				var units=$("input[name=\'unit\']:checked").val(); ;
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