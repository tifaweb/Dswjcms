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
class CommAction extends SharingAction{
	/*
	*参数说明
	*	q		//需要操作的表
	*	n		//跳转提示语
	*	u		//跳转地址
	*	m		//存放LOG的数据并区分前后台		m[0]:1前台2后台3同时 其他为各LOG所需的数据
	*	i		//积分值
	*   o		//积分参数
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public   $modtab = array(
		'us'		=>'User',
		'borrow'	=>'Borrowing',
		'ufo'		=>'Userinfo',
		'sys'		=>'System',
		'with'		=>'Withdrawal',
		'off'		=>'Offline',
		'rech'		=>'Recharge',
		'int'		=>'Integral',
		'intgr'		=>'Integralconf',
		'forr'		=>'Forrecord',
		'unite'		=>'Unite',
		'memgrade'	=>'Membership_grade',
		'vip'		=>'Vip_points',
		'ag'		=>'Auth_group',
	  	'aga'		=>'Auth_group_access',
	  	'ar'		=>'Auth_rule',
	 	'am'		=>'Admin',
	  	'sta' 		=>'Site_add',
	  	'art' 		=>'Article',
	  	'atd' 		=>'Article_add',
	  	'cm'		=>'Commision',
		'Guar'		=>'Guaranteeapply',
		'Gcomp'		=>'Guaranteecomp',
		'on'		=>'Online',
	);
	
	public function _list($array=array()){
		$map = $array['map'];
		$field = $array['field'] ? $array['field'] :'';
		$order = $array['order'] ? $array['order'] : " id " ;
		$group = $array['group'] ? $array['group'] : '';
		$pagenub = $array['pagenub'] ?$array['pagenub'] :10;
		if($model){
			$mod= $this->modtab[$model];
			
		}else{
			$mod = $this->getActionName();
		}
		$mod= D($mod);
		import('ORG.Util.Page');
		$count  = $mod->where($map)->count();// 查询满足要求的总记录数
        $Page  = new Page($count,$pagenub);// 实例化分页类 传入总记录数和每页显示的记录数
        $show  = $Page->show();// 分页显示输出
		if($field && $group){
			$list = $mod->where($map)
			->field($field)
			->order($order)
			->group($group)
			->limit($Page->firstRow.','.$Page->listRows)
			->select();
		}elseif($field && !$group){
		  $list = $mod->where($map)
			->field($field)
			->order($order)
			->limit($Page->firstRow.','.$Page->listRows)
			->select();
		}elseif(!$field && $group){
		  $list = $mod->where($map)
			->order($order)
			->group($group)
			->limit($Page->firstRow.','.$Page->listRows)
			->select();
		}else{
		  $list = $mod->where($map)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		}
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		
	}
	
   public function upda(){
		$q=$_REQUEST['q'];	
		$sid=intval($_REQUEST['sid']);
		$u=$_REQUEST['u']?$_REQUEST['u']:'/';
		$n=$_REQUEST['n']?$_REQUEST['n']:'更新';
		
		if($q){
			$model= $this->modtab;
			$model = D($model[$q]);
			
		}else{
		   $name=$this->getActionName();
		   $model = D ($name);
		}
		$pk = $_REQUEST['g']?$_REQUEST['g']:$model->getPk();
		if($model->create()){
			  $result = $model->where(array($pk=>$sid))->save();
			 if($result){
				if(GROUP_NAME=='Admin'){
					$this->Record($n.'成功');//后台操作
				}else{
					$this->userLog($n.'成功');//前台操作
				}
				 $this->success($n."成功",$u);
				  
				
			 }else{
				if(GROUP_NAME=='Admin'){
					$this->Record($n.'失败');//后台操作
				}else{
					$this->userLog($n.'失败');//前台操作
				}
				$this->error($n."失败");
			 }			 			
		}else{
			 $this->error($model->getError());
		}

	}
	
	public function del(){
		$q=$_REQUEST['q'];
		$id=intval($_REQUEST['id']);
		$u=$_REQUEST['u']?$_REQUEST['u']:'';
		$n=$_REQUEST['n']?$_REQUEST['n']:'删除';
		if(!$id){
			 dwzSt();
			exit();
		}
		if(isset($_REQUEST['q'])){
			$model= $this->modtab;
	     	$model = D($model[$q]);
		}else{
		   $name=$this->getActionName();
		   $model = D ($name);
		}		
		$pk = $model->getPk();
         $result = $model->where(array($pk=>$id))->delete();
		if($result){
			if(GROUP_NAME=='Admin'){
				$this->Record($n.'成功');//后台操作
			}else{
				$this->userLog($n.'成功');//前台操作
			}
			 $this->success($n."成功",$u);
				
		}else{
			if(GROUP_NAME=='Admin'){
				$this->Record($n.'失败');//后台操作
			}else{
				$this->userLog($n.'失败');//前台操作
			}
			$this->error($n."失败");
		}			 			
	

	}
	
	public function add(){
		$q=$_REQUEST['q'];	
		$n=$_REQUEST['n']?$_REQUEST['n']:'添加';
		$u=$_REQUEST['u']?$_REQUEST['u']:'/';
		if($q){
			$model= $this->modtab;	
	     	$model = D($model[$q]);
		}else{
		   $name=$this->getActionName();
		   $model = D ($name);
		}
        if($model->create()){
		     $result = $model->add();
			if($result){
				if(GROUP_NAME=='Admin'){
					$this->Record($n.'成功');//后台操作
				}else{
					$this->userLog($n.'成功');//前台操作
				}
				$this->success($n."成功",$u);			
			}else{
				if(GROUP_NAME=='Admin'){
					$this->Record($n.'失败');//后台操作
				}else{
					$this->userLog($n.'失败');//前台操作
				}
				 $this->error($n."失败");
			}	
		}else{
			$this->error($model->getError());
			
		}
		
	}
			
	//过滤器
	    public function dsFilter(){
		$name= ACTION_NAME;
        if(array_key_exists($name,$this->Filter)){
		}
	}
}
?>