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
		import('ORG.Util.Page');
        $count      = M('borrowing')->where('state=0')->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();
        $borrow=$this->borrow_unicom(0,'state=0',$Page->firstRow.','.$Page->listRows);
		$this->assign('borrow',$borrow);
		$this->assign('page',$show);
		$this->display();
    }
	
	//--------普通标显示-----------
	public function index(){
		$linkage=$this->borrowLinkage();
		$this->assign('linkage',$linkage);
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['borrow']='active';
		$this->assign('active',$active);
		$head='<link  href="__PUBLIC__/css/style.css" rel="stylesheet">';
		$this->assign('head',$head);
		$Borrow=D("Borrowing");
		$borrow=$Borrow->where('id="'.$this->_get('id').'"')->find();
		$data=explode(";",$borrow['data']);//分割合同协议和企业实地照片
		$borrow['img']=array_filter(explode(",",$data[0]));
		$borrow['agr']=array_filter(explode(",",$data[1]));
		unset($data);
		$this->assign('b',$borrow);
		$this->display();
    }

//--------普通标编辑-----------
	public function ordinaryEdit(){
		$model=D('Borrowing');
		$money=M('money');
		$mone=$money->where('uid="'.$this->_post('user_uid').'"')->find();
		$systems=$this->systems();
		if($create=$model->create()){
			$img=implode(",",$this->_post('img'));
			$agr=implode(",",$this->_post('agr'));
			$create['data']=$img.';'.$agr;
			$result = $model->where(array('id'=>$this->_post('sid')))->save($create);
			if($result){
				//记录添加点
				$this->Record('对'.$create['title'].'的修改');//后台操作
				$this->success("更新成功");			
			}else{
				 $this->error("更新失败");
			}
		}else{
			$this->error($model->getError());
			
		}
	}

//--------满标待审核-----------
    public function pending(){
		import('ORG.Util.Page');// 导入分页类
        $count      = M('borrowing')->where('`state`=5')->count();// 查询满足要求的总记录数
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $borrow=$this->borrow_unicom(0,'`state`=5',$Page->firstRow.','.$Page->listRows);
		$this->assign('borrow',$borrow);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
    }
//--------贷款列表-----------
    public function entry(){
		$this->assign('borrow',$borrow);
		if($this->_get('title')){
			if($this->_get('title')){
				$uid=M('user')->field('id')->where('`username`="'.$this->_get('title').'"')->find();
				$uid=$uid['id'];
				$uid=$uid?'`uid`="'.$uid.'" or ':'';
				$bids=is_numeric($this->_get('title'))?'`id`="'.$this->_get('title').'" or ':"";
				$where.="(".$uid.$bids."`title` LIKE '%".$this->_get('title')."%')";
			}
		}
		
		if($this->_get('type')>0){
			$where.=' and `type`="'.$this->_get('type').'"';
		}
		if(is_numeric($_GET['state'])){
			$where.=" and `state`='".$this->_get('state')."'";
		}
		$where=trim($where,'and ');
		
		import('ORG.Util.Page');// 导入分页类
        $count      = M('borrowing')->where($where)->count();// 查询满足要求的总记录数
      
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		
        $borrow=$this->borrow_unicom(0,$where,$Page->firstRow.','.$Page->listRows);
		 
		$this->assign('borrow',$borrow);
		$this->assign('page',$show);// 赋值分页输出
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
			$borrow=$this->borrow_information($id);
			$data=explode(";",$borrow[0]['data']);//分割合同协议和企业实地照片
			$borrow[0]['img']=array_filter(explode(",",$data[0]));
			$borrow[0]['agr']=array_filter(explode(",",$data[1]));
			unset($data);
			$this->assign('borrow',$borrow);
			
			$this->display();
    }
	
	//审核保存
    public function review_validation(){
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
			if($Borrow[0]['type']==7){	//债权转让计算最终到期时间
				if($Borrow[0]['candra']==0){	//获取用户选择的是月标还是天标
					$month=$Borrow[0]['deadline'];
					$create['limittime']=strtotime("+$month month");
				}else{
					$day=$Borrow[0]['deadline'];
					$create['limittime']=strtotime("+$day day");
				}
			}
           if($Borrow[0]['type']==0){	//秒还标
				$counters=$this->counters($Borrow[0]['money'],$Borrow[0]['rates'],$Borrow[0]['deadline'],$Borrow[0]['candra'],$Borrow[0]['way']);	//利息计算
				if($Borrow[0]['reward_type']==1){	//金额
					$reward=$Borrow[0]['reward'];
				}else if($Borrow[0]['reward_type']==2){	//百分比
					$reward=$Borrow[0]['reward']*0.01*$Borrow[0]['money'];
				}
				$models = new Model();
				$models->query("UPDATE `ds_money` SET `available_funds` = `available_funds`-".($counters['interest']+$reward).", `freeze_funds` = `freeze_funds`+".($counters['interest']+$reward)." WHERE `uid` =".$Borrow[0]['uid']);
		   }
			$Borrowing->save($create);
			if($this->_post('state')==1){	//判断审核状态
				$stat=$state="通过";
				if($Borrow[0]['type']==0){	//秒还标
					$money=M('money');
					$moneys=$money->where('uid='.$Borrow[0]['uid'])->find();	
					$this->moneyLog(array(0,'【'.$Borrow[0]['title'].'】审核通过,冻结资金',($counters['interest']+$reward),'平台',$moneys['total_money'],$moneys['available_funds'],$moneys['freeze_funds'],$Borrow[0]['uid']),18);	//资金记录
				}
			}else{
				$state="失败";
				$stat="失败<br>失败原因：".$Borrow['review_note'];
			}
			//记录添加点
			$this->Record('对'.$Borrow[0]['title'].'的审核');//后台操作
			$this->userLog($Borrow[0]['title'].'审核'.$state,$Borrow[0]['uid']);	//会员记录
			$this->silSingle(array('title'=>'【'.$Borrow[0]['title'].'审核'.$state.'】','sid'=>$Borrow[0]['uid'],'msg'=>'<a href="'.__ROOT__.'/Home/Loan/invest/'.$Borrow[0]['id'].'.html">【'.$Borrow[0]['title'].'】</a>'.'审核'.$stat));//站内信
			$this->success('审核成功', '__APP__/TIFAWEB_DSWJCMS/Loan/entry');
			
    }
	
	/*
	*
	*复审
	*
	*/
	public function borrowUpda(){
		$sid=intval($this->_post('id'));
		$state=intval($this->_post('state'));
		$borr=D('Borrowing')->relation(true)->where('`id`="'.$sid.'"')->find();
		$model = M('borrowing');
		if($borr['candra']==0){	//获取用户选择的是月标还是天标
			$month=$borr['deadline'];
			$limittime=strtotime("+$month month");
		}else{
			$day=$borr['deadline'];
			$limittime=strtotime("+$day day");
		}
		$create['reviewtime']			=time();
		$create['limittime']			=$limittime;	//逾期时间
		$create['state']				=$state;
		//解决多次提交导致的误操作
		$number=$this->orderNumber();
		$this->bidPretreatment($number,1);
		$result = $model->where(array('id'=>$sid))->save($create);
		$borr['state']=$state;	//获取审核状态
		if($result){
			 $this->fullApproval($borr,$number);
			 $this->Record('对'.$borr['title'].'的复审通过');//后台操作
			 //$this->success('复审通过',$u);
			 echo '<p class="green">复审通过</p>';
			echo '<p class="jump">
			页面自动 <a href="'.__APP__.'/'.TIFAWEB_DSWJCMS.'/Loan/pending.html">跳转</a> 等待时间： <b>3秒</b>
			</p>';
			
		}else{
			$this->Record('对'.$borr['title'].'的复审失败');//后台操作
			//$this->error("复审失败");
			echo '<p class="red">复审失败！</p>';
			echo '<p class="jump">
			页面自动 <a href="'.__APP__.'/'.TIFAWEB_DSWJCMS.'/Loan/pending.html">跳转</a> 等待时间： <b>3秒</b>
			</p>';
			}			 			
	}

		
	//导出EXCEL
	public function integExport(){
		$typ=$this->_post('type');
		$stat=$this->_post('state');
		$type=($typ=='0' || $typ)?"type=".$typ:'';
		$state=($stat=='0' || $stat)?"state=".$stat:'';
		$where=trim($type." and ".$state,' and ');
		$fid=$this->_post('fid');
		if($fid>0){
		$list=$borrow=$this->borrow_unicom(0,$where,$fid.',5000');
		}else{
		$list=$borrow=$this->borrow_unicom(0,$where,5000);
		}
		
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
			$this->success("导出成功","__APP__/TIFAWEB_DSWJCMS/Loan/entry");
		
	}
	
	//用户编辑显示AJAX
     public function userajax(){
		$id=$this->_post("id");
		$borrow=$this->borrow_information($id);
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
	
	//--------逾期-----------
    public function overduebid(){
		$this->assign('borrow',$borrow);
		import('ORG.Util.Page');// 导入分页类
        $count      = M('overdue')->count();// 查询满足要求的总记录数
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $overdue=$this->verdue('','',$Page->firstRow.','.$Page->listRows);//逾期信息
		$this->assign('audit',$overdue);
		$this->assign('page',$show);// 赋值分页输出
		
		
		$endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__APP__/TIFAWEB_DSWJCMS/Loan/userajax", {id:id});
}
		';
		$this->assign('endjs',$endjs);
		$this->display();
	}
	
	//--------机构担保-----------
    public function institutions(){
		
		$Guaranteeapply = D("Guaranteeapply");
		$list=$Guaranteeapply->relation(true)->select();
		//城市
		$system=$this->systems();
		$inst=explode(",",$system['sys_institution']);
		//担保公司
		$guara=$this->guaranteeComp();
		foreach($list as $id => $li){
			$list[$id]['locations']=$inst[$list[$id]['location']];
			$list[$id]['gcompanys']=$guara[$list[$id]['gcompany']];
			$bo=$this->borrows($list[$id]['guarantee']['bid']);
			$list[$id]['stick']=$bo['stick'];
			$list[$id]['state']=$bo['state'];
			reset($bo);
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	/**
	* @标信息
	* @id		单条借款传入ID
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function borrows($id){
		$borrowing = M("borrowing");
		return $borrowing->where('id="'.$id.'"')->field('stick,state')->find();
	}
	
	
	
	//投资记录
	public function irecord(){
		$bid=$this->_get('bid');
		import('ORG.Util.Page');// 导入分页类
        $count      =M('borrow_log')->count();// 查询满足要求的总记录数
		
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $record=$this->bRecord($bid,$Page->firstRow.','.$Page->listRows);
		
		$this->assign('record',$record);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	
	//导出EXCEL(投资记录)
	public function recordExport(){
		
		$data['title']="投资记录";
		$fid=$this->_post('fid');
		if($fid>0){
		$list=$this->bRecord('',$fid.',5000','`id` ASC');
		}else{
		$list=$this->bRecord('',5000,'`id` ASC');
		}
		$data['name']=array(
							array('n'=>'ID','u'=>'id'),
							array('n'=>'用户ID','u'=>'uid'),
							array('n'=>'用户组','u'=>'type'),
							array('n'=>'总金额','u'=>'total'),
							array('n'=>'可用金额','u'=>'available'),
							array('n'=>'操作金额','u'=>'operation'),
							array('n'=>'操作说明','u'=>'instructions'),
							);
		foreach($list as $l){
			switch($l['type']){
				case 1:
				$type="借款标发布";
				break;
				case 2:
				$type="借款标审核后";
				break;
				case 3:
				$type="借款中投资人操作";
				break;
				case 4:
				$type="借款中借款人操作";
				break;
				case 5:
				$type="复审中投资人操作";
				break;
				case 6:
				$type="复审中借款人操作";
				break;
				case 7:
				$type="复审后投资人操作";
				break;
				case 8:
				$type="复审后借款人操作";
				break;
				case 9:
				$type="借款到期收款";
				break;
				case 10:
				$type="借款到期还款";
				break;
				case 11:
				$type="逾期";
				break;
				case 12:
				$type="提现";
				break;
				case 15:
				$type="债权转让投资人操作";
				break;
				case 16:
				$type="债权转让借款人操作";
				break;
				case 17:
				$type="流标";
				break;
			}
			$content[]=array(
							'id'				=>' '.$l['id'],
							'uid'				=>' '.$l['actionname']['uid'],
							'type'				=>$type,
							'total'				=>$l['actionname']['total'],
							'available'			=>$l['actionname']['available'],
							'operation'			=>$l['actionname']['operation'],
							'instructions'		=>strip_tags($l['actionname']['instructions'])
							);
		}
		$data['content']=$content;
		$excel=$this->excelExport($data);
		$this->Record('投资记录导出成功');//后台操作
			$this->success("导出成功","__APP__/TIFAWEB_DSWJCMS/Loan/record.html");
		
	}
	
}
?>