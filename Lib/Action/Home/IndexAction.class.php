<?php
// +----------------------------------------------------------------------
// | dswjcms
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.tifaweb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
// +----------------------------------------------------------------------
// | Author: tfweb <dianshiweijin@126.com>
// +----------------------------------------------------------------------
defined('THINK_PATH') or exit();
class IndexAction extends HomeAction {
    public function index(){
		$shuffling = M('shuffling');
		$shufflings=$shuffling->field('title,img')->order('`order` ASC')->select();
		$this->assign('shuff',$shufflings);
		$head="<link href='__PUBLIC__/css/jslides.css' rel='stylesheet'>";
		$this->assign('head',$head);
		//标题、关键字、描述
		$Site = D("Site");
		$site=$Site->field('keyword,remark,title,link')->where('link="'.$_SERVER['REQUEST_URI'].'"')->find();
		$this->assign('si',$site);
		$active['index']='active';
		$this->assign('active',$active);
		
		$mod = D("Article");
		$list = $mod->field('id,title')->where("catid=1 or catid=13 or catid=14")->order('`addtime` DESC')->limit('2')->select();
		$this->assign('list',$list);
		$this->display();
    }
}