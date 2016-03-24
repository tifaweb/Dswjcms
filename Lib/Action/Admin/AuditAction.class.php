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
		if($this->_get('title')){
			$uid=M('user')->field('id')->where('`username`="'.$this->_get('title').'"')->find();
			$uid=$uid['id'];
			$where=$uid?'`id`="'.$uid.'"':'';
		}
		import('ORG.Util.Page');// 导入分页类
        $count      = M('userinfo')->where($where)->count();// 查询满足要求的总记录数
		
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $audit=$this->audit($where,1,$Page->firstRow.','.$Page->listRows);
		$this->assign('audit',$audit);
		$this->assign('page',$show);// 赋值分页输出
		$endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__APP__/TIFAWEB_DSWJCMS/Audit/userajax", {id:id});
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
		$("#edits").load("__APP__/TIFAWEB_DSWJCMS/Audit/userajax", {id:id});
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
		$("#edits").load("__APP__/TIFAWEB_DSWJCMS/Audit/userajax", {id:id});
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
		$("#edits").load("__APP__/TIFAWEB_DSWJCMS/Audit/userajax", {id:id});
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