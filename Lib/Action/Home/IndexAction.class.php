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
		$where='(`state`>0 and `state` !=12 and `state` !=13 and `state` !=14 and `state` !=16 and `state` !=8)';
		$borrow=$this->borrow_unicoms($where,'5','`stick` DESC,`state` ASC,`time` DESC');
		
		$this->assign('borrow',$borrow);
		$shuffling = M('shuffling');
		$shufflings=$shuffling->field('title,img,url')->where('`state`=0 and type=0')->order('`order` ASC')->select();
		$shcount=$shuffling->field('title,img,url')->where('`state`=0')->count();
		$this->assign('shcount',$shcount);
		$this->assign('shuff',$shufflings);
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['index']='active';
		$this->assign('active',$active);
		//累计投资金额
		$borrowing = M('borrowing');
		$accumulate['sum']=$borrowing->where('`state`=7 or `state`=8 or `state`=9')->sum('money');
		//累计预期收益
		$money = M('money');
		$accumulate['benefit']=$money->sum('`stay_interest`+`make_interest`+`make_reward`');
		//年化收利率
		$accumulate['avg']=$borrowing->avg('rates');
		//注册人数
		$user = M('user');
		$accumulate['enrollment']=$user->count();
		$this->assign('accumulate',$accumulate);
		$endjs='
		//首页轮播
		$(function(){$("#kinMaxShow").kinMaxShow();});
		
		';
		$this->assign('endjs',$endjs);
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
	
//-------计算器
	public function counter(){
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['counter']='active';
		$this->assign('active',$active);
		$linkage=$this->borrowLinkage();
		$this->assign('linkage',$linkage);
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