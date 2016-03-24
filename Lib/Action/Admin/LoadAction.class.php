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
//提供jquery load加载
class LoadAction extends Action{
	public function viewGroupUser($id){
				if($id){
			$where= 'group_id ="'.$id.'"';
		}else{
			$this->error("请选择用户组");
		}
		$mod = D("auth_group_access");
		$list = $mod->where($where)->relation("admin")->select();
		$this->assign('list',$list);// 赋值分页输出
		$this->assign('id',$id);// 赋值分页输出
        $this->display();
	}
	
	//显示用户组
	public function userGroups(){
		$mod = D("auth_group");
		$list = $mod->select();
		$this->assign('list',$list);// 赋值分页输出
        $this->display();
	
	}
	
	//重新加载提成分组成员
	public function viewCommisionUser($id){
				if($id){
			$where= 'group_id ="'.$id.'"';
		}else{
			$this->error("请选择用户组");
		}
		$mod = D("user_commision");
		$list = $mod->where($where)->relation("user")->select();
		$this->assign('list',$list);// 赋值分页输出
		$this->assign('id',$id);// 赋值分页输出
        $this->display();
	}	
		

}

?>