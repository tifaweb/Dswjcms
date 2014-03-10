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
}