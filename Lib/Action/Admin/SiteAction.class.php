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
class SiteAction extends AdminCommAction {
//--------栏目管理-----------

    //查看栏目
	public function index($pid=0){
	  $mod = D("Site");
	  $field = "id,pid,title,link,concat(catpid,'-',id) as absPath,title,type,status,order,orde";
	  $order = " absPath,id ";
	  if($pid){
		  $site = $mod->field($field)->where("pid=".$pid)->order($order)->select();	
	  }else{
	      $site = $mod->field($field)->order($order)->select();	
	  }

	  $this->assign('site',$site);
	  $this->display();
	}
	
	//添加栏目
	public function addSite($pid=0){
		$user_id = $_SESSION['admin_uid'] ?$_SESSION['admin_uid'] : 0;
		if($pid){
			$list = D("Site")->where('id="'.$pid.'"')->find();
			$catpid=$list['catpid']."-".$pid;
			$list['newOrder'] = intval($list['order'])+1;
		}else{
			$mod = D("Site");
		    $field = "id,pid,concat(catpid,'-',id) as absPath,title,order";
		    $order = " absPath,id ";
			$site = $mod->field($field)->order($order)->select();
			$list=0;
			$pid=0;
			$catpid=0;
		}
		$this->assign('user_id',$user_id);
		$this->assign('pid',$pid);
		$this->assign('catpid',$catpid);
		$this->assign('site',$site);
		$this->assign('list',$list);
		$this->display();
	}
	
	//编辑栏目
	public function editSite($id){
		if(!$id){
			$this->error("请选择栏目");
		}	
		$mod = D("Site");	
		$field = "id,pid,concat(catpid,'-',id) as absPath,title,order";
		$order = " absPath,id ";
		$site = $mod->field($field)->order($order)->select();
		$list = $mod->where('id="'.$id.'"')->relation("site_add")->find();
		$sites = D("Site")->where('id="'.$list['pid'].'"')->find();	
		if(!$sites){
			$sites['title']='顶级类目';
		}
		$this->assign('list',$list);
		$this->assign('site',$site);
		$this->assign('sites',$sites);
		$this->display();
	}
	
	//添加文章
	public function addArticle($id=0){
		$mod = D("Site");
		if(!$id){
			$field = "id,pid,concat(catpid,'-',id) as absPath,title,order";
			$order = " absPath,id ";
			$list = $mod->field($field)->where('type=2')->order($order)->select();
		}else{
		     $site = $mod->where('id="'.$id.'"')->find();
		}
		$user_id = $_SESSION['admin_uid'];
		$this->assign('site',$site);
		$this->assign('list',$list);
		$this->assign('user_id',$user_id);
		$this->display();		
	}
	
	//保存添加文章
	public function saveArticle($id=0){
        $add = D("Article_add");
		$art = D("Article");
		if($add->create()){
			$ret1 = $add->add();
			if($ret1){
				
				
				if($art->create()){
					$art->fid = $ret1;
					$ret2 = $art->add();
					if($ret2){
						$this->Record('文章添加成功','__APP__/TIFAWEB_DSWJCMS/Site/articleList');//后台操作
						$this->success("添加成功");
						//echo "添加成功";
					}else{
						$this->error( "添加失败art");
					//	echo "添加失败art";
					}
				}else{
					$this->Record('文章添加失败');//后台操作
					$this->error( "art不能添加");
					//echo "art不能添加";
				}
			}
		}else{
			$this->error("添加失败add");
			//echo "添加失败add";
		}

		
	}	
	
	//显示文章
	public function articleList($id=0){
        if($id){
	       $where = 'catid="'.$id.'"';
		}else{
			$where = "id>0";

		}
		$field = "id,pid,concat(catpid,'-',id) as absPath,title,order";
		$order = " absPath,id ";
		$site = D("Site")->field($field)->where('type=2')->order($order)->select();
        if($id){
	       foreach($site as $k=>$v){
			   if(intval($v['id']) == $id){
				   $title = $v['title'];
			   }
		   }
		}else{
			$title = "显示全部";

		}		
		$mod = D("Article");
		
	    import('ORG.Util.Page');// 导入分页类
	    $count      = $mod->where($where)->count();// 查询满足要求的总记录数
	    $Page       = new Page($count,$pageNub);// 实例化分页类 传入总记录数和每页显示的记录数
	    $show       = $Page->show();// 分页显示输出
	  // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
	    $list = $mod->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();

	    $this->assign('list',$list);// 赋值数据集
	    $this->assign('Page',$show);// 赋值分页输出				
		$this->assign('site',$site);
		$this->assign('id',$id);
		$this->assign('title',$title);
	    $this->display();	
		
	}
	
	//修改文章
	public function editArticle($id){
		if(!$id){
			$this->error("请先选择文章");
		}	
		$mod = D("Article");
		$list = $mod->where('id="'.$id.'"')->relation("Article_add")->find();	
		if(!$list){
			$this->error("找不到文章");
		}	
		$mod = D("Site");
		$field = "id,pid,concat(catpid,'-',id) as absPath,title,order";
		$order = " absPath,id ";
		$site = $mod->field($field)->where('type=2')->order($order)->select();
		$sites = D("Site")->where('id="'.$list['catid'].'"')->find();	
		$this->assign('site',$site);
		$this->assign('sites',$sites);
		$this->assign('list',$list);
		$this->display();			
	}
	
	//删除文章
	public function dellelist($id){
		if(!$id){
			$this->error("请先选择文章");
		}
		$mod = M("article");
		$dele=$mod->where('id="'.$id.'"')->delete();
		if($dele){
			$this->Record('删除文章成功');//后台操作
			$this->success('删除成功');
		}else{
			
			$this->Record('删除文章失败');//后台操作
			$this->error("删除失败");
		}	
	}
	
	//删除栏目
	public function delSite($id){
		if(!$id){
			$this->error("请先选择栏目");
		}		
		$where['catpid']  = array('like', '%'.$id.'%');
		$where['id']  = array('eq',$id);
		$where['_logic'] = 'or';
		$mod = D("Site");
		$mod_add = D("Site_add");
		$list = $mod->where($where)->select();
		$delList = array();  //site删除id列表
		$delAddList = array();//site_add删除id列表
		foreach($list as $k=>$v){
			array_push($delList ,$v['id']);
			array_push($delAddList ,$v['aid']);
		}
		$article = D("Article");
		$ar_add = D("Article_add");
		$alist = $article->where(array('catid'=>array('in',$delList)))->select();
		$delaList = array();//article删除id列表
		$delarddList = array();//article_add删除id列表
		foreach($alist as $k=>$v){
			array_push($delaList ,$v['id']);
			array_push($delarddList ,$v['fid']);
		}
		if(count($delList)>1){
	       $ret1 = $mod->where(array('id'=>array('in',$delList)))->delete();
		   $ret2 = $mod_add->where(array('id'=>array('in',$delAddList)))->delete();
		}else{
	       $ret1 = $mod->where(array('id'=>$id))->delete();
		   $ret2 = $mod_add->where(array('id'=>$delAddList[0]))->delete();			
		}
		
		if($ret1 && $ret2){
			if($alist){

				  if(count($delaList)>1){
					 $ret3 = $article->where(array('id'=>array('in',$delaList)))->delete();
					 $ret4 = $ar_add->where(array('id'=>array('in',$delarddList)))->delete();
					
				  }else{
					 $ret3 = $article->where(array('id'=>$delaList[0]))->delete();
					 $ret4 = $ar_add->where(array('id'=>$delarddList[0]))->delete();	
					 	
				  }	
				  if($ret3){
					  $this->Record('删除栏目成功');//后台操作
					 $this->success('删除成功');
					 // echo '删除成功';
				  }else{
					  $this->Record('删除栏目下的文章失败');//后台操作
				  	$this->error("删除栏目下的文章失败");
				 // echo "删除栏目下的文章失败";
					  
				  }				  
				  
				  
		    }else{
				 $this->Record('删除栏目成功');//后台操作
				 $this->success('删除成功');
			}

			
		}else{
			$this->Record('栏目删除失败');//后台操作
			$this->error('栏目删除失败');
		}
		
	}


}
?>