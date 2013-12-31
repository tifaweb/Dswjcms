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
	public function index($q=0){
		if($q){
			$zz="/^[a-z]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i"; 
			if(gettype($q)=="string" && preg_match($q,$zz)){
				$where=array('email'=>$q);
			}elseif(gettype($q)=="string"){
				$where = array('username'=>$q);
			}else{
				$where = array('id'=>$q);
			}
		}else{
			$where['id']=array('GT',0);
		}
		$mod = D("User");
	    
	    $list = $mod->where($where)->select();
	    $this->assign('list',$list);
		 $endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__URL__/userajax", {id:id});
}
function edits(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#editss").load("__URL__/passajax", {id:id});
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
		$borrow=$mod->where('id='.$id)->find();
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
			 $result = $mod->where("id=".$this->_get('id'))->delete();
			 if($result){
				 $this->Record('删除用户成功');//后台操作
				$this->success('删除成功');
			}else{
				$this->Record('删除用户失败');//后台操作
				$this->error("删除失败");
			}		
		}  
   }
	
	//显示用户组
	public function userGroups(){
		$mod = D("Auth_group");
		$list = $mod->select();
		$this->assign('list',$list);
		
        $this->display();
	
	}
	
	//查看用户组下所有用户
	public function viewUser($id=0){
		if($id){
			$where= "group_id =".$id;
		}else{
			$this->error("请选择用户组");
		}
		$mod = D("Auth_group_access");
		$list = $mod->where($where)->relation("admin")->select();
		$this->assign('list',$list);
		$this->assign('id',$id);
        $this->display();
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
		$("#edits").load("__URL__/adminajax", {id:id});
}
		';
		$this->assign('endjs',$endjs);
        $this->display();
	}	
	
	//管理员修改AJAX
    public function adminajax(){
		$id=$this->_post("id");
		$mod = D("Admin");
		$borrow=$mod->where('id='.$id)->find();
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
			 $result = $mod->where("id=".$this->_get('id'))->delete();
			 if($result){
				 $this->Record('删除管理员成功');//后台操作
				$this->success('删除成功');
			}else{
				$this->Record('删除管理员失败');//后台操作
				$this->error("删除失败");
			}		
		}  
   }
	
	//添加用户组
    public function addGroup(){
		
		$mod = D("Auth_group");	
		 if($mod->create()){
		 	$result = $mod->add();
			if($result){
				$this->Record('添加用户组成功');//后台操作
				$this->success("添加用户组成功");
			}else{
				$this->Record('添加用户组失败');//后台操作
				$this->error("添加用户组失败");
			}
		 }else{
		 	$this->error($mod->getError());
		 }
	}	
	
	//为分组添加成员 
	public function saveUser(){
		$data = array();
		foreach($_POST['guser'] as $k=>$v ){
			$data[$k]['group_id']=intval($_POST['group_id']);
			$data[$k]['uid']=intval($v);
		}
		$mod = D("auth_group_access");
		$ret = $mod->addAll($data);
		if($ret){
			 $this->Record('添加分组成员成功');//后台操作
			 $this->success("添加分组成员成功");
		}else{
			 $this->Record('添加分组成员失败');//后台操作
			 $this->error("添加分组成员失败");
		}		
	}
	
	//分组添加用户
	public function addGroupUser(){
		$data = array();
		foreach($_POST['params'] as $k=>$v ){
			$data[$k]['group_id']=intval($_POST['group_id']);
			$data[$k]['uid']=intval($v);
		}
		$mod = D("Auth_group_access");
		$ret = $mod->add($data);
		if($ret){
			
		}else{
			
		}
	}
	
	//为小组分配权限
	public function editUserGroups($id){
		if(!$id){ 
		   $this->error("请选择分组");
		}
		$group = D("Auth_group")->where("id=".$id)->find();
		$rule =  D("Auth_rule")->field('id,name')->order('fid  ASC ,id ASC')->select();
		$inarr = explode(",",$group['rules']);
		foreach($rule as $k=>$v){
			if(in_array($v['id'],$inarr)){
				$rule[$k]['cla']="y";
			}else{
				$rule[$k]['cla']="n";
			}
		}
		
		$this->assign('group',$group);
		$this->assign('rule',$rule);
		$this->display();		
	}

   //权限规则管理
   public function competence(){
	  $mod = D("Auth_rule");
	  $list = $mod->order('id DESC')->select();
	  $this->assign('list',$list);
	  $unite=M('unite');
	  $unt=$unite->where('`state`=0 and `pid`=17')->order('`order` asc,`id` asc')->select();
	  $this->assign('unt',$unt);
	  $endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__URL__/editajax", {id:id});
}
		';
		$this->assign('endjs',$endjs);
	  $this->display();	
   }
   
   //权限编辑显示AJAX
    public function editajax(){
		$unite=D('Auth_rule');
		$id=$this->_post("id");
		$list=$unite->where('`id`='.$id)->find();
		$unite=M('unite');
		$unt=$unite->where('`state`=0 and `pid`=17')->order('`order` asc,`id` asc')->select();
		echo '
			<table class="table">
        <tbody>
          <tr>
            <td>授权名称：</td>
            <td><input name="name" type="text" class="span6" placeholder="请输入权限名称..." value="'.$list['name'].'"></td>
          </tr>
		  <tr>
			<td>
				   分组：
			</td>
			<td>
				<select name="fid">';
			foreach($unt as $lt){
				if($list['fid']==$lt['value']){
					echo '<option value="'.$lt['value'].'" selected>'.$lt['name'].'</option>';
				}else{
					echo '<option value="'.$lt['value'].'">'.$lt['name'].'</option>';
				}
			}
		echo '
				</select>
			</td>
		  </tr>
          <tr>
            <td>控制器：</td>
            <td><input name="condition" type="text" class="span6" placeholder="Group-Controller-action" value="'.$list['condition'].'"></td>
          </tr>
		  <input name="sid" type="hidden" value="'.$id.'" />
        </tbody>      
    </table>
		';
    }
   
   //删除权限
   public function exitcom(){
	   $mod =  D("Auth_rule");
		if($this->_get('id')){
			 $result = $mod->where("id=".$this->_get('id'))->delete();
			 if($result){
				 $this->Record('删除权限成功');//后台操作
				$this->success('删除成功');
					
			}else{
				$this->Record('删除权限失败');//后台操作
				$this->error("删除失败");
			}		
		}  
   }
   
   //删除用户下的某个用户
   public function delGroupUser($uid=0){
	   if(!$uid){
		   $this->error("选择删除用户");
	   }
	   $uid = intval($_REQUEST['uid']);
	   $mod =  D("Auth_group_access");
	   $result = $mod->where("uid=".$uid)->delete();
		if($result){
			$this->Record('删除某个用户成功');//后台操作
			$this->success('删除成功');
				
		}else{
			$this->Record('删除某个用户失败');//后台操作
			$this->error("删除失败");
		}		   
   }

}
?>