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
/*
*提成管理
*/
class CommisionAction extends AdminCommAction {
	public function index(){
	}
	
	public function group($pid=0){
	
	  $mod = D("Commision");
	  $field = "id,pid,name,concat(catpid,'-',id) as absPath,level,ratio,status,bonus,addtime";
	  $order = " absPath,id ";
	  if($pid){
		  $list = $mod->field($field)->where("pid=".$pid)->order($order)->select();	
	  }else{
	      $list = $mod->field($field)->order($order)->select();	
	  }
	   
	  $this->assign('list',$list);
	 $this->display();		
	}
	
	//添加分组
	public function addGroup($pid=0){
		$user_id = $_SESSION['admin_uid'] ?$_SESSION['admin_uid'] : 0;
		$mod = D("Commision");
		if($pid){
			$list = $mod->where("id=".$pid)->find();
			if(!intval($list['if_downNode'])){
				$this->error("该分组不允许添加下级分组");
			}
			$catpid=$list['catpid']."-".$pid;
			$list['newLevel'] = intval($list['level'])+1;
		}else{
		    $field = "id,pid,name,concat(catpid,'-',id) as absPath,level,ratio,status,if_downNode";
		    $order = " absPath,id ";
			$group = $mod->field($field)->order($order)->select();
			$list=0;
			$pid=0;
			$catpid=0;
		}
		if(!$group){
			$group[0]['id'] = 0;
			$group[0]['absPath'] = 0;
			$group[0]['level'] = 0;
			$group[0]['name'] = "顶级分组";
			$group[0]['if_downNode'] = 1;
		}
		$this->assign('user_id',$user_id);
		$this->assign('pid',$pid);
		$this->assign('catpid',$catpid);
		$this->assign('group',$group);
		$this->assign('list',$list);
		$this->display();	
	}
	
	//编辑分组
	public function editGroup($id=0){
		if(!$id){
			$this->error("请选择分组");
		}	
	  $user_id = $_SESSION['admin_uid'] ?$_SESSION['admin_uid'] : 0;
	  $mod = D("Commision");
	  $list = $mod->where("id=".$id)->find();	
	  $field = "id,pid,name,concat(catpid,'-',id) as absPath,level,ratio,if_downNode,bonus";
	  $order = " absPath,id ";
	  $group = $mod->field($field)->order($order)->select();	
	  
	  $this->assign('list',$list);
	  $this->assign('group',$group);
	  $this->assign('user_id',$user_id);
	  if(isset($_REQUEST['q'])){
	     $this->display("editGroup2");	
	  }else{
	   $this->display();	
	  }
	}	
	
	//查看用户组下所有用户
	public function viewUser($id=0){
		if($id){
			$where= "group_id =".$id;
		}else{
			$this->error("请选择用户组");
		}
		$group = D("Commision")->where("id=".$id)->find();
		$mod = D("user_commision");
		$list = $mod->where($where)->relation("user")->select();
		$this->assign('list',$list);
		$this->assign('id',$id);
		$this->assign('group',$group);
        $this->display();
	}	
	
    //删除分组
	public function delGroup($id=0){
		if($id){
			$where= "group_id =".$id;
		}else{
			$this->error("请选择用户组");
		}	
		$where['catpid']  = array('like', '%'.$id.'%');
		$where['id']  = array('eq',$id);
		$where['_logic'] = 'or';
		$mod = D("Commision");
		$moduc = D("user_commision");
		$list = $mod->where($where)->select();
		$delList = array();  //删除id列表
		$upUser = array();//site_add删除id列表
		foreach($list as $k=>$v){
			array_push($delList ,$v['id']);
		}	
		$list2 = $moduc	->where("id in (".$delList.")")->select();
		foreach($list2 as $k=>$v){
			array_push($upUser ,$v['uid']);
		}	

		$ret1 = $mod->where(array('id'=>array('in',$delList)))->delete();		
		$ret2 = $moduc->where(array('group_id'=>array('in',$delList)))->delete();	
		if($ret1 && $ret2){
			$Model = new Model();
			$sql = "update ds_user set uid = 0 where id in(".$upUser.")";
			$Model->execute($sql,false);	
			$this->success('删除成功');
		}elseif($ret1 && !$ret2){
			$this->success('分组删除成功，分组与用户对应关系删除失败，请手动删除');
		}elseif(!$ret1 && $ret2){
			$this->success('，分组与用户对应关系删除成功，分组删除失败，请手动删除');
		}
	}
	
    //设置提成比例
	
	public function setRatio($id = 0){
		if(!$id){

			$this->error("请选择用户组");
		}		
		$mod = D("Commision");
		$field = "id,pid,name,concat(catpid,'-',id) as absPath,level,ratio,addtime";
		$order = " absPath,id ";
		$where['catpid']  = array('like', '%'.$id.'%');
		$where['id']  = array('eq',$id);
		$where['_logic'] = 'or';
		$list = $mod->field($field)->where($where)->order($order)->select();
        $this->assign('list',$list);
		$this->display();
	}

}
?>