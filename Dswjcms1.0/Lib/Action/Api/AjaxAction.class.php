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
class AjaxAction extends CommAction {
	/**
	*
	* @ajax接口
	* @作者		wu
	*
	*/
	
	//以json方式返回分组的成员
    public function getGroupUsersJson(){
		$id = intval($_REQUEST['id']);
		if(!$id){
			RJson(array("status"=>"n",'info'=>'请选择分组id'));   
		}
	   $mod = D("auth_group_access");
	   $notList = $mod->field("uid")->select();
	   $arr = array();
	   foreach($notList as $k=>$v){
		   array_push($arr,$v['uid']);
	   }
	   $notin = $arr ? implode(",",$arr) : "0";
	   $admin = D("Admin");
	   $list = $admin->field("id,username")->where("id not in(".$notin.") and id!=1")->select();
	   
	   if($list){
        RJson($list);
	   }else{
		 RJson(array("status"=>"n"));   
	   }
	 
    }
	
	

	
	//获取权限规则的json数据
	public function getRuleJson(){
	   $mod = D("auth_rule");
	   $list= $mod->field("id,name")->select();
	   RJson($list);
	}
	
	//设置权限
	public function setCompetence(){
		$gid = intval($_REQUEST['gid']);
		$id = intval($_REQUEST['id']);
		$action = $_REQUEST['action'];
		if(!$gid || !$id || !$action){
		    RJson(array('info'=>"用户组id||制授权id||操作说明不能为空","status"=>'n'));	
		}
		$mod =  D("auth_group");		
		$list = $mod->where("id=".$gid)->find();
		if(!$list){
			RJson(array('info'=>"找不到记录","status"=>'n'));
		}
		$rule = explode(",",$list['rules']);
		if($action == "Authorize"){//添加权限
			if(in_array($id,$rule)){
				RJson(array('info'=>"已拥有权限无须再分配","status"=>'n'));
			}else{
				$data['id'] =  $gid;
				array_push($rule,$id);
				$data['rules']=implode(",",$rule);
				$ret = $mod->save($data);
				$sql = $mod->getLastSql();
				if($ret){
					RJson(array('info'=>"权限分配成功","status"=>'y'));
				}else{
					RJson(array('info'=>"权限分配失败","status"=>'n','sql'=>$sql));
				}
			}
		}elseif($action == "Cancel"){//取消权限
			if(!in_array($id,$rule)){
				RJson(array('info'=>"已取消，无须重复操作","status"=>'n'));
			}else{
				$data['id'] =  $gid;
				$newRule = array();
				foreach($rule as $k=>$v){
					if(intval($v) != intval($id)){
						$newRule[] = $v;
					}
				}
				$data['rules']=implode(",",$newRule);
				$ret = $mod->save($data);
				$sql = $mod->getLastSql();
				if($ret){
					RJson(array('info'=>"权限取消成功","status"=>'y'));
				}else{
					RJson(array('info'=>"权限取消失败","status"=>'n','sql'=>$sql));
				}
			}			
			
		}
		
		
	}
//------------------------------提成管理ajax--------------------------------
   //以json方式返回提成管理 分组的成员
    public function getCommisionGroupUsersJson(){
		$id = intval($_REQUEST['id']);
		if(!$id){
			RJson(array("status"=>"n",'info'=>'请选择分组id'));   
		}
	   $mod = D("user_commision");
	   $notList = $mod->field("uid")->select();
	   $arr = array();
	   foreach($notList as $k=>$v){
		   array_push($arr,$v['uid']);
	   }
	   $notin = $arr ? implode(",",$arr) : "0";
	   $admin = D("User");
	   $list = $admin->field("id,username")->where("id not in(".$notin.")")->select();
	   
	   if($list){
        RJson($list);
	   }else{
		 RJson(array("status"=>"n"));   
	   }		
	}
	
	//从用户表为提成分组添加成员 
	public function saveCommUser(){
		$user_uid = $_SESSION['user_uid'];
		$admin_uid = $_SESSION['admin_uid'];
		if(!$user_uid && !$admin_uid ){
			RJson(array("status"=>"n","info"=>"没有权限"));
		}
		
		$data = array();
		foreach($_POST['params'] as $k=>$v ){
			$data[$k]['group_id']=intval($_POST['group_id']);
			$data[$k]['uid']=intval($v);
		}
		$mod = D("user_commision");
		$ret = $mod->addAll($data);
		if($ret){
			 RJson($data);
		}else{
			 RJson(array("status"=>"n"));
		}		
	}	
	//手动为提成分组添加成员 
	public function maddUserClink(){

		$mod = D("User");
        
		if($mod->create()){
			$ret = $mod->add();
			if($ret){
				$cmod = D("user_commision");
				$data['uid'] = $ret;
				$data['group_id'] = $_POST['group_id'];
				 $cmod->add($data);
				
			 RJson(array("status"=>"y",'info'=>'添加成功'));
			
			}else{
				RJson(array("status"=>"n",'info'=>'失败user'));
			}
		}else{
			RJson(array("status"=>"n",'info'=>'create失败'));
		}
	
	}	
	
	//获取可分配比例
	public function getCbass(){
		$pid = intval($_REQUEST['pid']);
		if(!$pid){
			RJson(array("status"=>"n",'info'=>'请选择分组id'));   
		}	
		$list = D("Commision")->where("id=".$pid)->find();
		if(intval($list['level']) == 1){
		//if(intval($list['level']) == 1 || intval($list['level']) == 2){
			RJson(array("status"=>"y",'cbass'=>1,'info'=>'父级为顶级分类'));   
		}
		$in = explode('-',$list['catpid']);
		$in = implode(",",$in);
		$comm = D("Commision")->where("id in(".$in.") and level >1")->order("level asc")->select();
		if(!$comm){
			$comm = array();
		}
		array_push($comm,$list);
		$i = 1;
		foreach($comm as $k=>$v){
			$i = $i - floatval($v['bonus']);
		}
		RJson(array("status"=>"y",'info'=>'测试'.$pid,'cbass'=>$i));	
		//RJson($comm);
	}
	
	//重新分配比例前把整个上下线的比例设为0
	public function initializeBonus(){
		$pid = intval($_REQUEST['pid']);
		if(!$pid){
			RJson(array("status"=>"n",'info'=>'请选择分组id'));   
		}
		$where['catpid']  = array('like', '%'.$pid.'%');
		$where['id']  = array('eq',$pid);
		$where['_logic'] = 'or';
		$data['bonus']=0;
		$comm = D("Commision")->where($where)->save($data);	
		//$list = D("Commision")->where($where)->select();
		if($comm){	
		  RJson(array("status"=>"y",'info'=>'初始化成功'));
		}else{
			RJson(array("status"=>"n",'info'=>'初始化失败'));
		}
	}
	//保存修改结果
	public function saveBonus(){
		$id = intval(intval($_REQUEST['id']));
		$bonus = floatval($_REQUEST['bonus']);
		$data['id']=$id;
		$data['bonus']=$bonus;
		if(!$id){
			RJson(array("status"=>"n",'info'=>'请选择分组id'));   
		}
		$mod = D("Commision") ;
		$comm = $mod->save($data);	
		if($comm){	
		  RJson(array("status"=>"y",'info'=>'修改成功'));
		}else{
			RJson(array("status"=>"n",'info'=>'修改失败'.$id.'--'.$bonus));
		}		
	}


}