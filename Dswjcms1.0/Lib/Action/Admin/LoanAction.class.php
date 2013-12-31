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
class LoanAction extends AdminCommAction {
//--------待审核-----------
    public function review(){
		$borrow=R('dswjjd://Sharing/borrow_unicom',array(0,'state=0'));
		$this->assign('borrow',$borrow);
		$this->display();
    }
//--------满标待审核-----------
    public function pending(){
		$borrow=R('dswjjd://Sharing/borrow_unicom',array(0,'state=5'));	
		$this->assign('borrow',$borrow);
		$this->display();
    }
//--------贷款列表-----------
    public function entry(){
		$borrow=R('dswjjd://Sharing/borrow_unicom');
		$this->assign('borrow',$borrow);
		$this->display();
    }
	//推荐AJAX
	public function entry_stick(){
		$Borrowing=M('borrowing');
		$id=$this->_post('id');
		$stick=$this->_post('stick');
		$data['id']			= $id;
		if(isset($stick)){
		$data['stick']		= $stick;	
		}
		$Borrowing->save($data);
    }
	//审核页
    public function review_page(){
			$id=(int)$this->_get('id');
			$borrow=R('dswjjd://Sharing/borrow_information',array($id));
			$img=array_splice(explode(",",$borrow[0]['data']),1);
			$this->assign('borrow',$borrow);
			$this->assign('img',$img);
			$this->display();
    }
	
	//审核保存
    public function review_validation(){
			$msgTools = A('msg','Event');
			$Borrowing=D('Borrowing');
			$automatic=D('Automatic');
			$system=$this->systems();
            if(!$Borrowing->create()){
                    $this->error($Borrowing->getError());
            }
			$create=$Borrowing->create();
			$Borrow = $Borrowing->relation(true)->where('id='.$create['id'])->select();		
			$create['checktime']			=time();	//审核时间
			$create['endtime']			=time()+86400*$Borrow[0]['valid'];	//失效时间
			if($Borrow[0]['type']==7){	//流转标计算最终到期时间
				if($Borrow[0]['candra']==0){	//获取用户选择的是月标还是天标
					$month=$Borrow[0]['deadline'];
					$create['limittime']=strtotime("+$month month");
				}else{
					$day=$Borrow[0]['deadline'];
					$create['limittime']=strtotime("+$day day");
				}
			}
            $Borrowing->save($create);
			if($this->_post('state')==1){	//判断审核状态
				$stat=$state="通过";
			}else{
				$state="失败";
				$stat="失败<br>失败原因：".$Borrow['review_note'];
			}
			/*
			//自动投标
			if($Borrow[0]['max']>0){	//如果有设置最高投资金额
				$max=' and money<='.$Borrow[0]['max'];
			}
			$auto=$automatic->where('type=1 and plan=1 and uid !='.$Borrow[0]['uid'].'total >='.$Borrow[0]['min'].' and money>='.$Borrow[0]['min'].$max)->order('`time`  DESC')->select();//固定资金用户
			$count=$automatic->where('type=1 and plan=0 and uid !='.$Borrow[0]['uid'])->count();//系统分配总人数
			$cast=$Borrow[0]['money']*$system['sys_autoscale']*0.01;//可投金额
			$autos=$automatic->where('type=0 and plan=1 and uid !='.$Borrow[0]['uid'].'total >='.$Borrow[0]['min'].' and money>='.$Borrow[0]['min'].$max)->order('`time`  DESC')->select();//系统分配用户
			if($system['sys_automaticBid']==1 && $this->_post('state')!==10){	//已启动自动投标并且不是担保标
				if($system['sys_automaticBid']==1){	//已启动秒标功能
					foreach($auto as $ao){
						$arr['id']=$create['id'];
						$arr['price']=$ao['money'];
						$arr['surplus']=$Borrow[0]['surplus'];
						$arr['uid']=$Borrow[0]['uid'];
						$arr['uname']=$Borrow[0]['username'];
						$arr['total']=$Borrow[0]['total'];
						if(($cast-$ao['money'])>=0){	//如果扣掉用户固定投资资金后不为负数就执行投标
							$this->autoTender($arr);
							$cast=$cast-$ao['money'];//更新可投资金额
						}else{
							$type=1;	//设置type为1下面就不再进行系统分配
							break;//跳出循环
						}
					}
					if(!$type && $count>0){	//如果可投金额还有，就进行系统分配
						$pric=floor($cast/$count);	//计算出每个用户可以投资的金额
						foreach($autos as $ao){
							if($ao['total']>=$pric){	//可用投资金金额比需要投资金额大
								$arr['id']=$create['id'];
								$arr['price']=$pric;
								$arr['surplus']=$Borrow[0]['surplus'];
								$arr['uid']=$Borrow[0]['uid'];
								$arr['uname']=$Borrow[0]['username'];
								$arr['total']=$Borrow[0]['total'];
								$this->autoTender($arr);
							}
						}
					}
				}else{
					if(!$Borrow[0]['type']==0){	//不是秒标
						foreach($auto as $ao){
							$arr['id']=$create['id'];
							$arr['price']=$ao['money'];
							$arr['surplus']=$Borrow[0]['surplus'];
							$arr['uid']=$Borrow[0]['uid'];
							$arr['uname']=$Borrow[0]['username'];
							$arr['total']=$Borrow[0]['total'];
							if(($cast-$ao['money'])>=0){	//如果扣掉用户固定投资资金后不为负数就执行投标
								$this->autoTender($arr);
								$cast=$cast-$ao['money'];//更新可投资金额
							}else{
								$type=1;	//设置type为1下面就不再进行系统分配
								break;//跳出循环
							}
						}
						if(!$type && $count>0){	//如果可投金额还有，就进行系统分配
							$pric=floor($cast/$count);	//计算出每个用户可以投资的金额
							foreach($autos as $ao){
								if($ao['total']>=$pric){	//可用投资金金额比需要投资金额大
									$arr['id']=$create['id'];
									$arr['price']=$pric;
									$arr['surplus']=$Borrow[0]['surplus'];
									$arr['uid']=$Borrow[0]['uid'];
									$arr['uname']=$Borrow[0]['username'];
									$arr['total']=$Borrow[0]['total'];
									$this->autoTender($arr);
								}
							}
						}
					}
				}
			}
			*/
			//记录添加点
			$this->Record('对'.$Borrow[0]['title'].'的审核');//后台操作
			$this->userLog($Borrow[0]['title'].'审核'.$state);	//会员记录
			$msgTools->sendMsg(3,$Borrow[0]['title'].'审核'.$state,'<a href="'.__ROOT__.'/Home/Loan/invest/'.$Borrow[0]['id'].'.html">【'.$Borrow[0]['title'].'】</a>'.'审核'.$stat,'admin',$Borrow[0]['uid']);//站内信
			$this->success('审核成功', '__URL__/entry');
			
    }
	
	/*
	*
	*复审
	*
	*/
	public function borrowUpda(){
		$sid=intval($this->_post('id'));
		$state=intval($this->_post('state'));
		$borr=R('dswjjd://Sharing/borr',array($sid));
		$model = M('borrowing');
		if($borr['candra']==0){	//获取用户选择的是月标还是天标
			$month=$borr['deadlinea'];
			$limittime=strtotime("+$month month");
		}else{
			$day=$borr['deadlinea'];
			$limittime=strtotime("+$day day");
		}
		$create['reviewtime']			=time();
		$create['limittime']			=$limittime;	//逾期时间
		$create['state']				=$state;
		$result = $model->where(array('id'=>$sid))->save($create);
		$borr['state']=$state;	//获取审核状态
		if($result){
			 R('dswjjd://Sharing/fullApproval',array($borr));
			 $this->Record('对'.$borr['title'].'的复审通过');//后台操作
			 //$this->success('复审通过',$u);
			 echo '<p class="green">复审通过</p>';
			echo '<p class="jump">
			页面自动 <a href="__ROOT__/Admin/Loan/pending.html">跳转</a> 等待时间： <b>3秒</b>
			</p>';
			
		}else{
			$this->Record('对'.$borr['title'].'的复审失败');//后台操作
			//$this->error("复审失败");
			echo '<p class="red">复审失败！</p>';
			echo '<p class="jump">
			页面自动 <a href="__ROOT_//Admin/Loan/pending.html">跳转</a> 等待时间： <b>3秒</b>
			</p>';
			}			 			
	}
	
	//担保额度申请
	public function assurefor(){
		$llines	=	D('Lines');
		$user=$llines->relation(true)->select();
		$this->assign('list',$user);
		$endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__URL__/userajax", {id:id});
}
		';
		$this->assign('endjs',$endjs);
		$this->display();
	}
	
	//担保额度申请审核
	public function assureforadd(){
		$models = new Model();
		 if($this->_post('state')==2){	//通过
			$result = $models->query("UPDATE `ds_userinfo` SET `assure` = `assure`+".$this->_post('upass')." WHERE `uid` =".$this->_post('uid'));
			$result = $models->query("UPDATE `ds_lines` SET`state` = 2 WHERE `id` =".$this->_post('sid'));
		 }else{
			$result = $models->query("UPDATE `ds_lines` SET`state` = 3 WHERE `id` =".$this->_post('sid'));	
		 }
		 $this->Record('额度申请审核成功');//后台操作
		$this->success("申请成功");		 			
	}
		
	//导出EXCEL
	public function integExport(){
		$typ=$this->_post('type');
		$stat=$this->_post('state');
		$type=($typ=='0' || $typ)?"type=".$typ:'';
		$state=($stat=='0' || $stat)?"state=".$stat:'';
		$where=trim($type." and ".$state,' and ');
		$list=$borrow=$this->borrow_unicom(0,$where);
		$data['title']="贷款列表";
		$data['name']=array(
							array('n'=>'用户名','u'=>'username'),
							array('n'=>'标题','u'=>'title'),
							array('n'=>'金额','u'=>'money'),
							array('n'=>'利率','u'=>'rates'),
							array('n'=>'期限','u'=>'deadlines'),
							array('n'=>'类型','u'=>'type_name'),
							array('n'=>'状态','u'=>'state_name')
							);
		foreach($list as $l){
			$content[]=array(
							'username'		=>$l['username'],
							'title'			=>$l['title'],
							'money'			=>$l['money'],
							'rates'			=>$l['rates'],
							'deadlines'		=>$l['deadlines'],
							'type_name'		=>$l['type_name'],
							'state_name'	=>$l['state_name']
							);
		}
		$data['content']=$content;
		$excel=$this->excelExport($data);
			$this->Record('导出“贷款列表”');//后台操作
			$this->success("导出成功","__URL__/entry");
		
	}
	
	//用户编辑显示AJAX
     public function userajax(){
		$id=$this->_post("id");
		$borrow=R('dswjjd://Sharing/borrow_information',array($id));
		$tmp.='
			<div class="modal-body">
    <table class="table table-striped table-bordered table-condensed">
    <tbody>
    <tr><th>会员ID：</th><td>'.$borrow[0]['id'].'</td><th>用户名：</th><td>'.$borrow[0]['username'].'</td></tr>
    <tr><th>真实姓名：</th><td>'.$borrow[0]['name'].'</td><th>性别：</th><td>'.$borrow[0]['gender'].'</td></tr>
    <tr><th>民族：</th><td>'.$borrow[0]['national'].'</td><th>出生日期：</th><td>'.date('Y-m-d H:i:s',$borrow[0]['born']).'</td></tr>
    <tr><th>身份证：</th><td>'.$borrow[0]['idcard'];
	foreach($borrow[0]['idcard_img'] as $id=>$img){
		$tmp.='&nbsp;&nbsp;&nbsp;&nbsp;<a href="/Public/uploadify/uploads/idcard/'.$img.'" class="cboxElement">证件'.($id+1).'</a>';
	}
	$tmp.='</td><th>籍贯：</th><td>'.$borrow[0]['native_place'].'</td></tr>
    <tr><th>所在地：</th><td>'.$borrow[0]['location'].'</td><th>婚姻状况：</th><td>'.$borrow[0]['marriage'].'</td></tr>
    <tr><th>学历：</th><td>'.$borrow[0]['education'].'</td><th>月收入：</th><td>'.$borrow[0]['monthly_income'].'</td></tr>
    <tr><th>住房条件：</th><td>'.$borrow[0]['housing'].'</td><th>购车情况：</th><td>'.$borrow[0]['buy_cars'].'</td></tr>
    <tr><th>行业：</th><td>'.$borrow[0]['industry'].'</td><th>公司：</th><td>'.$borrow[0]['company'].'</td></tr>
    <tr><th>QQ：</th><td>'.$borrow[0]['qq'].'</td><th>邮箱：</th><td>'.$borrow[0]['email'].'</td></tr>
    <tr><th>固话：</th><td>'.$borrow[0]['fixed_line'].'</td><th>手机：</th><td>'.$borrow[0]['cellphone'].'</td></tr>
    <tr><!--<th>微信：</th><td>'.$borrow[0]['wechat'].'</td>--><th>认证：</th>
	<td>';
	if($borrow[0]['email_audit']>1){
		$tmp.='<a class="icon icon-orange icon-envelope-closed ajax-link" href="#"  data-rel="tooltip" title="邮箱已认证"></a>';
	}else{
		$tmp.='<a class="icon icon-envelope-closed ajax-link" href="#" data-rel="tooltip" title="邮箱未认证"></a>';
	}
	if($borrow[0]['certification']>1){
		$tmp.='<a class="icon icon-orange icon-profile" href="#"  data-rel="tooltip" title="实名已认证"></a>';
	}else{
		$tmp.='<a class="icon icon-profile" href="#" data-rel="tooltip" title="实名未认证"></a>';
	}
	if($borrow[0]['video_audit']>1){
		$tmp.='<a class="icon icon-orange icon-comment-video ajax-link" href="#"  data-rel="tooltip" title="视频已认证"></a>';
	}else{
		$tmp.='<a class="icon icon-comment-video ajax-link" href="#" data-rel="tooltip" title="视频未认证"></a>';
	}
	if($borrow[0]['site_audit']>1){
		$tmp.='<a class="icon icon-orange icon-users ajax-link" href="#"  data-rel="tooltip" title="现场已认证"></a>';
	}else{
		$tmp.='<a class="icon icon-users ajax-link" href="#" data-rel="tooltip" title="现场未认证"></a>';
	}
	if($borrow[0]['cellphone_audit']>1){
		$tmp.='<a class="icon icon-orange icon-cellphone ajax-link" href="#"  data-rel="tooltip" title="手机已认证"></a>';
	}else{
		$tmp.='<a class="icon icon-cellphone ajax-link" href="#" data-rel="tooltip" title="手机未认证"></a>';
	}
	$tmp.='</td>
	<th></th><td></td><th>
	</tr>
    
	</tbody>
    </table>
 
    </div>
		';
		echo $tmp;
    }
	
	//--------待审核-----------
    public function overduebid(){
		$overdue=$this->verdue();//逾期信息
		$this->assign('audit',$overdue);
		$endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__URL__/userajax", {id:id});
}
		';
		$this->assign('endjs',$endjs);
		$this->display();
	}
	
}
?>