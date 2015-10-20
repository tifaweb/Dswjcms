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
class SiteAction extends HomeAction{
      //index send
      public function index(){
         if(!$id){
			 $this->error("请先选择栏目");
		 }	
		 $this->display();
	  }
	  
	  //预览文章
	  public function article($id=0){
		  //标题、关键字、描述
		$Site = D("Site");
		$mod = D("Article");
		$si=$mod->field('keyword,remark,title,catid')->where('id="'.$id.'"')->find();
		$sb=$Site->field('title,link')->where('id='.$si['catid'])->find();
		$sin['title']=','.$si['title'];
		$this->assign('so',$sin);
		$si['title']=$si['title']."-".$sb['title'];
		$si['link']=$sb['link']?$sb['link']:1;
		$si[$si['link']]='active';
		$this->assign('si',$si);
		$active[$si['link']]='active';
		$this->assign('active',$active);
		
		
		//左边
		 $site=$Site->field('title,id,type')->where('type>1 and status=1')->select();
		 $this->assign('site',$site);
		 if(!$id){
			 $this->error("请先选择栏目");
		 }	
         if(!$id){
			 $this->error("请先选择文章");
		 }
		 
		 $list = $mod->where('id="'.$id.'"')->relation("site")->find();
		 $this->assign('list',$list);
		 $artic=$Site->field('title,id')->where('id='.$list['catid'])->find();
		 $this->assign('artic',$artic);
		 $this->display($list['content_tpl']);
	  }
	  
      //封面
	  
	  public function page($id=0){
		  //标题、关键字、描述
		$Site = D("Site");
		$si=$Site->field('keyword,remark,title,link')->where('id="'.$id.'"')->find();
		if(!$si['link']){
			$si['link']=1;
		}
		$this->assign('si',$si);
		$active[$si['link']]='active';
		$this->assign('active',$active);
		$si['title']=','.$si['title'];
		$this->assign('so',$si);
		 //左边
		 $Site = D("Site");
		 $site=$Site->field('title,id,type')->where('type>1 and status=1')->select();
		 $this->assign('site',$site);
		 $artic=$Site->field('title')->where('id="'.$id.'"')->find();
		 $this->assign('artic',$artic);
		 if(!$id){
			 $this->error("请先选择栏目");
		 }	
		 $mod = D("Site");
		 $list = $mod->where('id="'.$id.'"')->relation("site_add")->find();
		 $this->assign('list',$list);
		//$this->display($list['page_tpl']);
		$this->display($list['content_tpl']);
	  }
	  
	  //列表
	  public function listTpl($id=0){
		//标题、关键字、描述
		$Site = D("Site");
		$si=$Site->field('keyword,remark,title,link')->where('id="'.$id.'"')->find();
		if(!$si['link']){
			$si['link']=1;
		}
		$this->assign('si',$si);
		$active[$si['link']]='active';
		$this->assign('active',$active);
		 
		$mod = D("Article");
		$list = $mod->where('catid="'.$id.'"')->select();
		import('ORG.Util.Page');// 导入分页类
		$count      =$mod->where('catid="'.$id.'"')->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$list = $mod->where('catid="'.$id.'"')->order('`id` DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('list',$list);
		
		 //左边
		 $Site = D("Site");
		 $site=$Site->field('title,id,type')->where('type>1 and status=1')->select();
		 $this->assign('site',$site);
		$this->display("list");
	  }	  
	 
	  
}
?>
