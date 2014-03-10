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
class AuditAction extends AdminCommAction {
//--------认证列表-----------
    public function entry(){
		$audit=$this->audit(0,1);
		$this->assign('audit',$audit);
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
//--------实名认证-----------
    public function autonym(){
		$audit=$this->audit(1);
		$this->assign('audit',$audit);
		$this->display();
    }

//--------视频认证-----------
    public function video(){
		$audit=$this->audit(2);
		$this->assign('audit',$audit);
		$s=$this->systems();
		$this->assign('s',$s);
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
//--------现场认证-----------
    public function site(){
		$audit=$this->audit(3);
		$this->assign('audit',$audit);
		$s=$this->systems();
		$this->assign('s',$s);
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

//--------手机认证-----------
    public function phone(){
		$audit=$this->audit(4);
		$this->assign('audit',$audit);
		$s=$this->systems();
		$this->assign('s',$s);
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
	
	//用户编辑显示AJAX
     public function userajax(){
		$id=$this->_post("id");
		$borrow=$this->user_details($id);
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
	
//--------VIP-----------
    public function vip(){
		$vippoints=D('Vip_points');
		$vip=$vippoints->relation(true)->select();
		foreach($vip as $id=>$vp){
			$vip[$id]['type']=$vp['expiration_time']-time();
		}
		$this->assign('list',$vip);
		$endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__URL__/editajax", {id:id});
}
function changeVerify(){
    var timenow = new Date().getTime();
    document.getElementById("verifyImg").src="'.__APP__.'/Public/verify/"+timenow;
}
		';
		$this->assign('endjs',$endjs);
		$this->display();
    }
	
	//查看显示AJAX
    public function editajax(){
		$vippoints=D('Vip_points');
		$id=$this->_post("id");
		$list=$vippoints->relation(true)->where('`id`='.$id)->find();
		if($list['audit']==1){
			echo '<p class="form-inline">   
          <label class="radio"><input type="radio" name="audit" value="2" checked/> 通过</label>
          <label class="radio"><input type="radio" name="audit" value="3" /> 不通过</label>
		  <p class="span2"><textarea name="text" placeholder="失败原因以站内信发送..."></textarea></p>
        </p>
		<input name="sid" type="hidden" value="'.$id.'" />
		<input name="uid" type="hidden" value="'.$list['uid'].'" />
		<input name="deadline" type="hidden" value="'.$list['deadline'].'" />
		<input name="unit" type="hidden" value="'.$list['unit'].'" />
        <div class="span10">
      	  <img id="verifyImg" src="'.__APP__.'/Public/verify/" onClick="changeVerify()" title="点击刷新验证码" data-rel="tooltip" style="cursor:pointer;padding-left: 10px;"/>
          <input class="input-large" name="proving" type="text"  style="width:50px;margin-top: 10px;" title="验证码" data-rel="tooltip"/>
          <button class="btn btn-primary" type="submit">
              确认提交
          </button>
      	</div>';
		}else{
			echo '
			<table class="table">
        <tbody>
          <tr><td>用户名：</td><td>'.$list['username'].'</td></tr>
          <tr><td>总积分：</td><td>'.$list['total'].'</td></tr>
          <tr><td>可用积分：</td><td>'.$list['available'].'</td></tr>
		  <tr><td>冻结积分：</td><td>'.$list['freeze'].'</td></tr>
		  <tr>
            <td>状态：</td>';
		switch($list['audit']){
			case 0:
			echo "<td>未申请</td>";
			break;
			case 1:
			echo "<td>申请中</td>";
			break;
			case 2:
			echo "<td>已开通</td>";
			break;
			case 3:
			echo "<td>审核失败</td>";
			break;
			case 4:
			echo "<td>已过期</td>";
			break;
		}
		echo '
          </tr>
		  <tr><td>申请时间：</td><td>'.($list['checktime']?date("Y-m-d H:i:s",$list['checktime']):'未开通').'</td></tr>
		  <tr><td>有效期：</td>
		  <td>'.$list['deadline'].($list['unit']==1?'个月':'年').'</td></tr>
		  <tr><td>开通时间：</td><td>'.($list['opening_time']?date("Y-m-d H:i:s",$list['opening_time']):'未开通').'</td></tr>
		  <tr><td>到期时间：</td><td>'.($list['expiration_time']?date("Y-m-d H:i:s",$list['expiration_time']):'未开通').'</td></tr>
        </tbody>      
    </table>
		';
		}
    }
	
	//审核VIP
    public function exitvip(){
		$msgTools = A('msg','Event');
		$model=D('Vip_points');
		$money=M('money');
		$models = new Model();
		$systems=$this->systems();
		$inf=$this->integralConf();
		$arr['vip']=array('uid'=>$this->_post('uid'),'name'=>'vip_buy');
		if($model->create()){
			  $save['audit']=$this->_post('audit');
			  $deadline=$this->_post('deadline');
			  
			  if($this->_post('unit')==1){	//月
				 $limittime=strtotime("+$deadline month");
				 $vipcost=$systems['sys_vipm']*$deadline;
			  }else{	//年
			  	$limittime=strtotime("+$deadline years");
				$vipcost=$systems['sys_vipy']*$deadline;
			  }
			  	$available_funds=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$this->_post('uid'))->find();	//可用余额
				if($available_funds['available_funds']<$vipcost && $this->_post('audit')==2){
					$this->error("用户账户可能余额不足以开通VIP！");
				}
			  $save['opening_time']=time();
			  $save['expiration_time']=$limittime;
			  
			  $result = $model->where(array('id'=>$this->_post('sid')))->save($save);
			 if($result){
				 //记录添加点
				 $this->integralAdd($arr);	//积分操作
				 if($this->_post('audit')==2){
					$models->query("UPDATE `ds_money` SET `total_money` = total_money-".$vipcost.",`available_funds` = available_funds-".$vipcost." WHERE `uid` =".$this->_post('uid'));	//平台代付
				 	$msgTools->sendMsg(3,'VIP开通成功','您的VIP已成功开通！账户成功扣除'.$vipcost.'元','admin',$this->_post('uid'));//站内信
					$this->moneyLog(array(0,'VIP开通，扣除资金',$vipcost,'平台',($available_funds['total_money']-$vipcost),($available_funds['available_funds']-$vipcost),$available_funds['total_money'],$this->_post('uid')));	//资金记录
				 }else{
				 	$msgTools->sendMsg(3,'VIP审核失败',$this->_post('text'),'admin',$this->_post('uid'));//站内信
				 }
				 $this->Record('审核VIP成功');//后台操作
				 $this->success("审核成功");
				
			 }else{
				$this->Record('审核VIP失败');//后台操作
				$this->error("审核失败");
			 }			 			
		}else{
		     $msgTools->sendMsg(3,'VIP审核失败','您的账户余额不足以开通VIP！','admin',$this->_post('uid'));//站内信
			 $this->error($model->getError());
		}
	}
	
	//删除联动
    public function exitgan(){
		$unite=D('Unite');
		$result = $unite->where(array('id'=>$this->_get('id')))->delete();
		if($result){
			$this->Record('删除联动成功');//后台操作
			 $this->success("删除成功");
				
		}else{
			$this->Record('删除联动失败');//后台操作
			$this->error("删除失败");
		}		
	}
}
?>