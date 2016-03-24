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
class UserAction extends AdminCommAction {
	//显示所有用户
	public function index(){
		if($this->_get('title')){
			$where="`username`='".$this->_get('title')."' or `id`='".$this->_get('title')."'";
		}
		
	    import('ORG.Util.Page');// 导入分页类
        $count      = D("User")->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = D("User")->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id')->select();
	    $this->assign('list',$list);
        $this->assign('page',$show);// 赋值分页输出
	    
		 $endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__APP__/TIFAWEB_DSWJCMS/User/userajax", {id:id});
}
function edits(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#editss").load("__APP__/TIFAWEB_DSWJCMS/User/passajax", {id:id});
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
	
	//用户修改密码AJAX
    public function passajax(){
		$id=$this->_post("id");
		$mod = D("User");
		$borrow=$mod->where('id="'.$id.'"')->find();
		echo '
			<table class="table">
        <tbody>
          <tr>
            <td>用户名：</td>
            <td>'.$borrow['username'].'</td>
          </tr>
          <tr>
            <td>密码：</td>
            <td><input name="password" type="password" class="span2"></td>
          </tr>
		  <input name="sid" type="hidden" value="'.$id.'" />
        </tbody>      
    </table>
		';
    }
	
	//删除用户
   public function exituse(){
	   $mod =  D("User");
		if($this->_get('id')){
			 $result = $mod->where('id="'.$this->_get('id').'"')->delete();
			 if($result){
				 $this->Record('删除用户成功');//后台操作
				$this->success('删除成功');
			}else{
				$this->Record('删除用户失败');//后台操作
				$this->error("删除失败");
			}		
		}  
   }
		
	//管理员
	public function manage(){
		$mod = D("Admin");
		$list = $mod->select();
		$this->assign('list',$list);
		$endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__APP__/TIFAWEB_DSWJCMS/User/adminajax", {id:id});
}
		';
		$this->assign('endjs',$endjs);
        $this->display();
	}	
	
	//管理员修改AJAX
    public function adminajax(){
		$id=$this->_post("id");
		$mod = D("Admin");
		$borrow=$mod->where('id="'.$id.'"')->find();
		echo '
			<table class="table">
        <tbody>
          <tr>
            <td>用户名：</td>
            <td>'.$borrow['username'].'</td>
          </tr>
          <tr>
            <td>密码：</td>
            <td><input name="password" type="password" class="span2"></td>
          </tr>
		  <tr>
            <td>邮箱：</td>
            <td><input name="email" type="text" class="span2" value="'.$borrow['email'].'"></td>
          </tr>
		  <input name="sid" type="hidden" value="'.$id.'" />
        </tbody>      
    </table>
		';
    }
	
	//删除管理员
   public function exitman(){
	   $mod =D("Admin");
		if($this->_get('id')){
			 $result = $mod->where('id="'.$this->_get('id').'"')->delete();
			 if($result){
				 $this->Record('删除管理员成功');//后台操作
				$this->success('删除成功');
			}else{
				$this->Record('删除管理员失败');//后台操作
				$this->error("删除失败");
			}		
		}  
   }
}
?>