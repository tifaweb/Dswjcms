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
class SiteAction extends WinAction{
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
		$si=$mod->field('keyword,remark,title,catid')->where('id='.$id)->find();
		$sb=$Site->field('title,link')->where('id='.$si['catid'])->find();
		$si['title']=$sb['title']."-".$si['title'];
		$si['link']=$sb['link']?$sb['link']:1;
		$si[$si['link']]='active';
		$this->assign('si',$si);
		$active[$si['link']]='active';
		$this->assign('active',$active);
		$this->assign('title',$sb['title']);
		  //左边
		 $site=$Site->field('title,link,id,type')->where('type>1 and status=1')->select();
		 $this->assign('site',$site);
		 if(!$id){
			 $this->error("请先选择栏目");
		 }	
         if(!$id){
			 $this->error("请先选择文章");
		 }
		 
		 $list = $mod->where("id=".$id)->relation("site")->find();
		 $this->assign('list',$list);
		 $artic=$Site->field('title,id')->where('id='.$list['catid'])->find();
		 $this->assign('artic',$artic);
		 $this->display($list['content_tpl']);
	  }
	  
      //封面
	  
	  public function page($id=0){
		  //标题、关键字、描述
		$Site = D("Site");
		 //左边
		 $Site = D("Site");
		 $site=$Site->field('title,id,type')->where('type>1 and status=1')->select();
		 $this->assign('site',$site);
		 $artic=$Site->field('title')->where('id='.$id)->find();
		 $this->assign('artic',$artic);
		 $this->assign('title',$artic['title']);
		 if(!$id){
			 $this->error("请先选择栏目");
		 }	
		 $mod = D("Site");
		 $list = $mod->where("id=".$id)->relation("site_add")->find();
		 $this->assign('list',$list);
		$this->display($list['page_tpl']);
	  }
	  
	  //列表
	  public function listTpl($id=0){
		//标题、关键字、描述
		$Site = D("Site");
		$si=$Site->field('keyword,remark,title,link')->where('id='.$id)->find();
		if(!$si['link']){
			$si['link']=1;
		}
		$this->assign('si',$si);
		$active[$si['link']]='active';
		$this->assign('active',$active);
		$this->assign('title',$si['title']);
		  //左边
		 $Site = D("Site");
		 $site=$Site->field('title,id,type')->where('type>1 and status=1')->select();
		 $this->assign('site',$site);
		 $artic=$Site->field('title')->where('id='.$id)->find();
		 $this->assign('artic',$artic);
		 if(!$id){
			 $this->error("请先选择栏目");
		 }
		$mod = D("Article");
		$list = $mod->where("catid=".$id)->relation("site")->select();
		 $this->assign('list',$list);
		 $this->display("list");
	  }	  
	 
	  
}
?>
