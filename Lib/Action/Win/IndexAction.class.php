<?php
// +----------------------------------------------------------------------
// | dswjcms
// +----------------------------------------------------------------------
// | Copyright (c) http://www.tifaweb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
// +----------------------------------------------------------------------
// | Author: 宁波市鄞州区天发网络科技有限公司 <dianshiweijin@126.com>
// +----------------------------------------------------------------------
// | Released under the GNU General Public License
// +----------------------------------------------------------------------
// | author: purl
// +----------------------------------------------------------------------
// | time: 2016-12-15
// +----------------------------------------------------------------------
defined('THINK_PATH') or exit();
class IndexAction extends WinAction {
    public function index(){
		$shuffling = M('shuffling');
		$shufflings=$shuffling->field('title,img,url')->where('`state`=0 and type=1')->order('`order` ASC')->select();
		$this->assign('shufflings',$shufflings);
		//借款列表
		import('@.Plugin.DswjcmsApp.Pages');
		$where='(`state`=1 or `state`=2 or `state`=5 or `state`=7) and code=0';
		$count      = M('borrowing')->where($where)->count();
		$Page       = new Pages($count,5);
		$Page->setConfig('theme',"%first% <li class='now'>%nowPage%/%totalPage%</li> %linkPage%");
		$show       = $Page->show();
		$borrow=M('borrowing')->field('id,time,type,title,money,rates,deadline,way,state,surplus,limittime')->where($where)->order('`stick` DESC,`time` DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($borrow as $id=>$bo){
			$borrow[$id]['ratio']=sprintf("%01.2f",($bo['money']-$bo['surplus'])/$bo['money']*100);
			if($bo['money']>=10000){
				$borrow[$id]['money']=number_format($bo['money']/10000,0,'.',',').'万';
			}else{
				$borrow[$id]['money']=number_format($bo['money'],2,'.',',');
			}
			if($bo['candra']==1){
				$borrow[$id]['deadline']=$bo['deadline'].'天';
			}else{
				$borrow[$id]['deadline']=$bo['deadline'].'个月';
			}
		}
		$this->assign('borrow',$borrow);
		$this->assign('show',$show);
		$this->display();
    }
	
//-------------投资详细页--------------
	public function invest(){
		$id=$this->_get('id');
		if($id<1){
			 echo "非法操作，你的操作已被记录，网警正在锁定";
			 exit;
		}	
		$borrow=reset($this->borrow_information($id));
		$borr=M('borrowing');
		$borrow['amount_total']=$borr->where('uid='.$borrow['uid'].' and state=9')->Sum();//借入总金额
		$borrow['amount_total']=$borrow[0]['amount_total']?$borrow['amount_total']:0;
		$borrow['amount_number']=$borr->where('uid='.$borrow['uid'].' and state=9')->Count();//成功借入数
		$borrow['standard']=$borr->where('uid='.$borrow['uid'].' and state=4')->Count();//流标数
		$borrow['stay_number']=$borr->where('uid='.$borrow['uid'].' and state=7')->Count();//待还
		$coverdue=M('coverdue');
		$borrow['overdue_number']=$coverdue->where('uid='.$borrow['uid'])->Count();//逾期
		$this->assign('borrow',$borrow);
		$data=explode(";",$borrow['data']);
		$pact=array_filter(explode(",",$data[0]));
		$indeed=array_filter(explode(",",$data[1]));
		unset($data);
		$this->assign('img',$pact);
		$this->assign('imgs',$indeed);
		$userinfo=M('userinfo');
		$userin=$userinfo->field('assure')->where('uid='.$this->_session('user_uid'))->find();
		$this->assign('userin',$userin);
		$this->display();
	}
	
	//投标确认
	public function cast(){
		$this->homeVerify();
		$id=$this->_get('id');
		if($id<1){
			echo "非法操作，你的操作已被记录，网警正在锁定";
			 exit;
		}
		
		$borrow=M('borrowing')->where('`id`="'.$this->_get('id').'"')->find();
		$borrow['available']=M('money')->where('`uid`="'.$this->_session('user_uid').'"')->getField('available_funds');
		$this->assign('borrow',$borrow);
		$this->display();
	}
	
	//投标记录
	public function record(){
		$id=$this->_get('id');
		if($id<1){
			echo "非法操作，你的操作已被记录，网警正在锁定";
			 exit;
		}

		import('@.Plugin.DswjcmsApp.Pages');
		$where='(`bid`='.$id.' and `type` =4)';
		$count      = M('borrow_log')->where($where)->count();
		$Page       = new Pages($count,10);
		$Page->setConfig('theme',"%first% <li class='now'>%nowPage%/%totalPage%</li> %linkPage%");
		$show       = $Page->show();
		$log=M('borrow_log')->field('uid,type,time,actionname')->where($where)->order('`time` DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach ($log as $i=>$l){
			$actionname=json_decode($l['actionname'], true);
			$log[$i]['operation']=$actionname['operation'];
			$log[$i]['uname']=mb_substr($actionname['uname'],0,1,'utf-8')."***".mb_substr($actionname['uname'],-1,1,'utf-8');
			unset($actionname);
		}
		$this->assign('log',$log);
		$this->assign('show',$show);
		$this->display();
	}
}