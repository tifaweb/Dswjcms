<?php
/*
*Author :xq
*use :站内信
*/
class msgEvent extends Action{
	private $type = 1 ;//类型0管理发给所有用户  1 用户<->用户 , 2 用户<->管理员 ，3 管理员 <->用户 ， 4 管理员<->管理员
	private $info = array();
	private $mod ='';
	private $shield = ''; //屏蔽列表 由于所有用户都共同拥有系统群发信息 所以每个人删除群发信息时都会添加到删除屏蔽列表
    private $read = '' ; //已读系统信息列表	
	private $in_shield = false; //是否已获取屏蔽列表
	public function _initialize(){
      $this->mod = D("Instation");

	  
   }


    /*获取短消息 Condition用户名或用户id $field获取内容、unread未读 read已读inbox收信箱outbox发信箱 默认获取全部 
	
	 默认返回 array('unread'=array(),'read'=array(),'inbox'=array(),'outbox'=array(),)
	*/ 
	function msgInfo($Condition,$field=""){
		if(!$Condition){
			return array("msg"=>"请指定用户名或id");
		}
        if(!$this->in_shield){
			$this->_getShield($Condition);
		}
		$where = gettype($Condition)=="string" ? " friendname='".$Condition."'" :"  friendid=".$Condition;
		$where = "(".$where." or type =0 )  and id not in (".$this->shield.") and status_f = 1"; 
        if($field){
			if($field =="unread"){
				$where = gettype($Condition)=="string" ? "friendname='".$Condition."'" : " friendid=".$Condition;
				$where ="(".$where." or type =0 )  and rd < 1 and id not in(".$this->read.") ";
				
	
			}elseif($field =="read"){
				$where = gettype($Condition)=="string" ? "friendname='".$Condition."'" : " friendid=".$Condition;
				$where ="(".$where." or type =0 ) and rd = 1 or id in(".$this->read.") ";
				
	
			}elseif($field =="inbox"){
				$where = gettype($Condition)=="string" ? "friendname='".$Condition."'" : " friendid=".$Condition;
				$where ="(".$where." or type =0 )  and id not in (".$this->shield.") and status_f = 1"; 
	
			}else{
				$where = gettype($Condition)=="string" ? " hostid=".$Condition :"hostname=".$Condition."'";
				$where = "  and status_h = 1";
			}
			$list = $this->mod->where($where)->select();
			return $list;
	   }
		
		$list = $this->mod->where($where)->select();
		
		$ret= array();
		if($list){
			$rarr = explode(",",$this->read);
			foreach($list as $k=>$v){
	
/*				if(intval($v['hostid']) ==$Condition || $v['hostname']==$Condition){
					$ret['outbox'][]=$v;
				}*/
				if(intval($v['friendid']) ==$Condition || $v['friendname']==$Condition){
					$ret['inbox'][]=$v;
					if(intval($v['rd']) ==1 || in_array($v['id'],$rarr)){
						$ret['read'][]=$v;
					}else{
						$ret['unread'][]=$v;
					}
					
				}			
				
			}
		}
        
		return $ret;
		
	}
	
	
	/**
	*树状显示， 回复也主题的关系转为树型结构
	$Condition 用户名或密码 
	*/
	function msgTree($Condition){
		if(!$Condition){
			return array("msg"=>"请指定用户名或id");
		}
        if(!$this->in_shield){
			$this->_getShield($Condition);
		}
		$where = gettype($Condition)=="string" ? "hostname ='".$Condition ."' or friendname='".$Condition."'" :"hostid =".$Condition ." or friendid=".$Condition;
		$where .= " or type = 0  and id not in (".$this->shield.") and status_f = 1 and status_h = 1 "; 
		$field = "id,hostid,hostname,friendid,friendname,pid,concat(catpid,'-',id) as absPath,title,msg,type,rd,status_h,status_f";
		$order = " absPath,id ";

		$list = $this->mod->where($where)->field($field)->order($order)->select();
		
		return $list;	
	}
	/*
	*分页显示
	*/
	
	public function msgPage($Condition,$field="",$pageNub=10){

		if(!$Condition){
			return array("msg"=>"请指定用户名或id");
		}
		 if(!$this->in_shield){
			$this->_getShield($Condition);
		}
		$where = gettype($Condition)=="string" ? " friendname='".$Condition."'" :"  friendid=".$Condition;
		$where = "(".$where." or type =0 )  and id not in (".$this->shield.") and status_f = 1"; 
        if($field){
			if($field =="unread"){
				$where = gettype($Condition)=="string" ? "friendname='".$Condition."'" : " friendid=".$Condition;
				$where ="(".$where." or type =0 )   and rd < 1 and id not in(".$this->read.") ";
				
	
			}elseif($field =="read"){
				$where = gettype($Condition)=="string" ? "friendname='".$Condition."'" : " friendid=".$Condition;
				$where ="(".$where." or type =0 ) and rd = 1 and id in(".$this->read.") ";
				
	
			}elseif($field =="inbox"){
				$where = gettype($Condition)=="string" ? "friendname='".$Condition."'" : " friendid=".$Condition;
				$where ="(".$where." or type =0 )  and id not in (".$this->shield.") and status_f = 1"; 
	
			}else{
				$where = gettype($Condition)=="string" ? " hostid=".$Condition :"hostname=".$Condition."'";
				$where = "  and status_h = 1";
			}
	   }	


	  import('ORG.Util.Page');// 导入分页类
	  $count      = $this->mod->where($where)->count();// 查询满足要求的总记录数
	  $Page       = new Page($count,$pageNub);// 实例化分页类 传入总记录数和每页显示的记录数
	  $show       = $Page->show();// 分页显示输出
	  // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
	  $msglist = $this->mod->where($where)->order('addline')->limit($Page->firstRow.','.$Page->listRows)->select();
	  $this->assign('msgList',$msglist);// 赋值数据集
	  $this->assign('msgPage',$show);// 赋值分页输出



   
	   	
	}
	
	
	/*统计短消息 $Condition 用户名或id 
	 $count 统计内容  unread,read,inbox outbox
	 默认返回 array('unread'=nubmer,'read'=nubmer,'inbox'=nubmer),'outbox'=nubmer)
	*/
	function msgCount($Condition,$field =""){
		if(!$Condition){
			return array("msg"=>"请指定用户名或id");
		}
		 if(!$this->in_shield){
			$this->_getShield($Condition);
		}		
		$where = gettype($Condition)=="string" ? " friendname='".$Condition."'" :"  friendid=".$Condition;
		$where ="(".$where." or type =0 )  and id not in (".$this->shield.") and status_f = 1"; 
        if($field){
			if($field =="unread"){
				$where = gettype($Condition)=="string" ? "friendname='".$Condition."'" : " friendid=".$Condition;
				$where ="(".$where." or type =0 )   and rd < 1 or id not in(".$this->read.") ";
				
	
			}elseif($field =="read"){
				$where = gettype($Condition)=="string" ? "friendname='".$Condition."'" : " friendid=".$Condition;
				$where ="(".$where." or type =0 ) and rd = 1 or id in(".$this->read.") ";
				
	
			}elseif($field =="inbox"){
				$where = gettype($Condition)=="string" ? "friendname='".$Condition."'" : " friendid=".$Condition;
				$where = "(".$where." or type =0 )  and id not in (".$this->shield.") and status_f = 1"; 
	
			}else{
				$where = gettype($Condition)=="string" ? " hostid=".$Condition :"hostname=".$Condition."'";
				$where = "  and status_h = 1";
			}
			$list = $this->mod->where($where)->count();
			
			return $list;
	   }
		
		$list = $this->mod->where($where)->select();
		$unread = 0;
		$read = 0;
		$inbox = 0;
		$outbox = 0;
		$where = gettype($Condition)=="string" ? " hostname='".$Condition."'" :"  hostid=".$Condition;
		$where.=" and id not in (".$this->shield.") and status_f = 1"; 
		$lists = $this->mod->where($where)->count();
		$retarr= array();
		if($list){
			foreach($list as $k=>$v){
	

				if(intval($v['friendid']) ==$Condition || $v['friendname']==$Condition){
					$inbox++;
					if(intval($v['rd']) ==1 || in_array($v['id'],$rarr)){
						$read++;
					}else{
						$unread++;
					}
					
				}	
				
			}
		}
		$ret['unread'] = $unread;
		$ret['read'] = $read;
		$ret['inbox'] = $inbox;
		$ret['outbox'] = $lists;
		return $ret;
		
	}	
	
	
	/*发送消息
	*type 查看类的type 属性
	*title 标题 msg信息内容 host 发送者用户名或者id friend接收者用户或id
	*/
	function sendMsg($type,$title,$msg,$host,$friend){
		if(!$msg || !$host ){
			return array("msg"=>"请指定用户名或id于及发送的信息内容");
		}
		$this->type = $type;
		if(gettype($host) =="string"){
		  $arr = $this->_getInfo($host,$friend);
		}else{
		 $arr = $this->_getInfo2($host,$friend);
		}
		
		$this->info['title'] = $title;
		$this->info['hostid'] = $arr['hostid'];
		$this->info['hostname'] = $arr['hostname'];
		$this->info['friendid'] = $arr['friendid'];
		$this->info['friendname'] = $arr['friendname'];	
		$this->info['type'] = $arr['type'];			
        $this->info['msg'] = $msg;	
		$this->info['addline']=time();
		
		$ret = $this->mod->add($this->info);
		  return $ret;


        
	}
		
	//获取发送者与接收者详细信息
	private function _getInfo($hostname,$friendname=''){
	$whostname=is_numeric($whostname)?" id='".$hostname."'":" username='".$hostname."'";
	$wfriendname=is_numeric($friendname)?" id='".$friendname."'":" username='".$friendname."'";
	//1 群发
	if($this->type == 0){	
	    $list = D("Admin")->where(" username=".$hostname)->find();
		$arr['hostid'] = $lsit['id'];
		$arr['hostname'] = $list['username']?$list['username'] :"admin";
		$arr['friendid'] = 0;
		$arr['friendname'] = 'all';	
		$arr['type'] = 0;		   
	}elseif($this->type == 1){//1 用户<->用户
			$mod = D("User");
            $list = $mod->where(" username='".$hostname."' or username='".$friendname."'")->field("id,username")->select();
			
			$arr = array();
			foreach($list as $k=>$v){
				if($hostname == $v['username']){
					$arr['hostid'] = $v['id'];
					$arr['hostname'] = $v['username'];
				}else{
					$arr['friendid'] = $v['id'];
					$arr['friendname'] = $v['username'];
				}
			}
			$arr['type'] = 1;
		}elseif($this->type == 2){//2 用户<->管理员
		    $list1 = D("User")->where($whostname)->find();
			$list2 = D("Admin")->where($wfriendname)->find();
			$arr['hostid'] = $lsit1['id'];
			$arr['hostname'] = $list1['username'];
			$arr['friendid'] = $lsit2['id'];
			$arr['friendname'] = $list2['username'];	
			$arr['type'] = 2;		
		}elseif($this->type == 3){//3 管理员<->用户
		    $list1 = D("Admin")->where($whostname)->find();
			$list2 = D("User")->where($wfriendname)->find();
			$arr['hostid'] = $lsit1['id'];
			$arr['hostname'] = $list1['username'];
			$arr['friendid'] = $lsit2['id'];
			$arr['friendname'] = $list2['username'];	
			$arr['type'] = 3;		
		}else{
			$mod = D("Admin");
            $list = $mod->where(" username='".$hostname."' or username='".$friendname."'")->field("id,username")->select();
			$arr = array();
			foreach($list as $k=>$v){
				if($hostname == $v['username']){
					$arr['hostid'] = $v['id'];
					$arr['hostname'] = $v['username'];
				}else{
					$arr['friendid'] = $v['id'];
					$arr['friendname'] = $v['username'];
				}
			}			
			
			$arr['type'] = 4;
		}	
		return $arr;
		
	}
	
	//获取发送者与接收者详细信息
	private function _getInfo2($hostid,$friendid=''){
	//1 群发
	if($this->type == 0){	
	    $list = D("Admin")->where(" id=".$hostid)->find();
		$arr['hostid'] = $lsit['id'];
		$arr['hostname'] = $list['username']?$list['username'] :"admin";
		$arr['friendid'] = 0;
		$arr['friendname'] = 'all';	
		$arr['type'] = 0;		   
	}elseif($this->type == 1){//1 用户<->用户
			$mod = D("User");
            $list = $mod->where(" id=".$hostid." or id=".$friendid)->field("id,username")->select();
			$arr = array();
			foreach($list as $k=>$v){
				if($hostid == intval($v['hostid'])){
					$arr['hostid'] = $v['id'];
					$arr['hostname'] = $v['username'];
				}else{
					$arr['friendid'] = $v['id'];
					$arr['friendname'] = $v['username'];
				}
			}
			$arr['type'] = 1;
		}elseif($this->type == 2){//2 用户<->管理员
		    $list1 = D("User")->where(" id=".$hostid)->find();
			$list2 = D("Admin")->where(" id=".$friendid)->find();
			$arr['hostid'] = $lsit1['id'];
			$arr['hostname'] = $list1['username'];
			$arr['friendid'] = $lsit2['id'];
			$arr['friendname'] = $list2['username'];	
			$arr['type'] = 2;		
		}elseif($this->type == 3){//3 管理员<->用户
		    $list1 = D("Admin")->where(" id=".$hostid)->find();
			$list2 = D("User")->where(" id=".$friendid)->find();
			$arr['hostid'] = $lsit1['id'];
			$arr['hostname'] = $list1['username'];
			$arr['friendid'] = $lsit2['id'];
			$arr['friendname'] = $list2['username'];	
			$arr['type'] = 3;		
		}else{
			$mod = D("Admin");
            $list = $mod->where(" ide=".$hostid." or id=".$friendid)->field("id,username")->select();
			$arr = array();
			foreach($list as $k=>$v){
				if($hostnid == intval($v['hostname'])){
					$arr['hostid'] = $v['id'];
					$arr['hostname'] = $v['username'];
				}else{
					$arr['friendid'] = $v['id'];
					$arr['friendname'] = $v['username'];
				}
			}			
			
			$arr['type'] = 4;
		}
		return $arr;
		
	}
	//获取屏蔽列表，主是要针对系统统群发信息
	private function _getShield($Condition='',$status=1){
		if(!$Condition){
			$Condition = intval($_SESSION['user_uid']);
		}
		if(!$Condition){

			return array("msg"=>"请指定用户名或id");
			
		}
		$where = gettype($Condition)=="string" ? "name ='".$Condition ."'" :"uid =".$Condition ;
		$mod=  D("shield_msg");
		$list = $mod->where($where)->select();
		$shield = array();
		$read= array();
		foreach($list as $k=>$v){
			if(intval($v["status"]) == 1 || intval($v["status"]) == 2){
			  array_push($shield,$v['shieldId']);
			}elseif(intval($v["rd"]) == 1){
				array_push($read,$v['shieldId']);
			}
		}
		$arr['shield'] = $shield;
		$arr['read']= $read;
		$this->shield = $arr['shield'] ? implode(",",$arr['shield']) : "0";
	    $this->read = $arr['read'] ? implode(",",$arr['read']) :"0";
		$this->in_shield = true;
		return $arr;
		
	}

	
	//回复
	public function reply($id,$title,$msg){
		
		if(!$_SESSION['user_name']){
			return array("msg"=>"请指定用户名或id");
		}

		$list = $this->mod->where('id='.intval($id)." and friendname='".$_SESSION['user_name']."'")->find();
		if(!$list){
			return array("msg"=>"找不到回复主题");
		}
		
		$data['pid']=$list['id'];
		$data['catpid']=$list['catpid'].'-'.$list['id'];
		$data['msg']=$msg;
		$data['title']=$title;
		if(intval($list['type'])<1){
			$data['hostid'] = intval($_SESSION['user_uid']);
			$data['hostname'] = $_SESSION['user_name'];
			$data['friendid'] = 1;
			$data['friendname'] = "admin";			
		}
		$data['hostid'] = $list['friendid'];
		$data['hostname'] = $list['friendname'];
		$data['friendid'] = $list['hostid'];
		$data['friendname'] = $list['hostname'];
		
		$ret = $this->mod->add($data);
		return $ret;
		
	}
	
	//回收
	public function retrieveMsg($id,$type=1){
		if(!$_SESSION['user_name']){
			return array("msg"=>"请指定用户名或id");
		}
		$name = $_SESSION['user_name'];
		$uid = intval($_SESSION['user_uid']);
		if($type >= 1){
			$list = $this->mod->where("id=".$id)->find();
			if(intval($list['hostid']) == $uid || $list['hostname'] ==$name){
				$ret = $this->mod->where("id=".$id)->save(array('status_h'=>2));
			}elseif(intval($list['friendid']) == $uid || $list['friendname'] == $name){
				$ret = $this->mod->where("id=".$id)->save(array('status_f'=>2));
			}			
			if($ret){
				return array("status"=>1,"msg"=>"回收成功","info"=>"",'id'=>$id);
			}else{
				return array("status"=>0,"msg"=>"回收失败","info"=>"",'id'=>$id);
			}			

		}else{
			$data['uid'] = intval($_SESSION['user_uid']);
			$data['name'] = $_SESSION['user_name'];
			$data['shieldId'] = $id;
			$mod = D("shield_msg");
			$where = "uid=".$data['uid']." or name='".$data['name']."' and shieldId = " .$id;
			$ret = $mod->where($where)->save(array("status"=>1));
			
			if($ret){
              return  array("status"=>1,"msg"=>"回收成功","info"=>"",'id'=>$id);
			}else{
				$data['status'] = 1;
				$ret = $mod->add($data);
				//echo $mod->getLastSql();
				if($ret){
					return array("status"=>1,"msg"=>"回收成功","info"=>"系统信息回收",'id'=>$id);
				}else{
					return array("status"=>0,"msg"=>"回收失败","info"=>"系统信息回收失败",'id'=>$id);
				}
			}
		}
	
		
	}
	
	//删除
	public function delMsg($id,$type=1){
		if(!$_SESSION['user_name']){
			return array("msg"=>"请指定用户名或id");
		}
		$name = $_SESSION['user_name'];
		$uid = intval($_SESSION['user_uid']);
		$where = "id=".$id;
		
		if($type >= 1){//如果不是系统群发信息
			$list =  $this->mod->where($where)->find();
			//如果执行删除者是发送方，判断接收是否已删除或回收
			if(intval($list['hostid']) == $uid || $list['hostname'] == $name ){
				if(intval($list['status_f']) >2){//达到删除条件
					  $ret =  $this->mod->where($where)->delete();//执行删除动作
					  if($ret){
						  return array("status"=>1,"msg"=>"删除成功","info"=>"发送方删除",'id'=>$id);
					  }else{
						  return array("status"=>0,"msg"=>"删除失败","info"=>"发送方删除",'id'=>$id);
					  }
				}else{
					$ret = $this->mod->where($where)->save(array("status_h"=>3));
					if($ret){
						return array("status"=>1,"msg"=>"回收成功","info"=>"发送方回收",'id'=>$id);
					}else{
						return array("status"=>0,"msg"=>"回收失败","info"=>"发送方回收",'id'=>$id);
					}					
				}
					
			}
			//如果执行删除者是接收方，判断发送方是否已删除或回收	
			if(intval($list['friendid']) == $uid || $list['friendname'] == $name ){
				if(intval($list['status_h']) >2){
					$ret =  $this->mod->where("id=".$id)->delete();//执行删除动作
					if($ret){
						return array("status"=>1,"msg"=>"删除成功","info"=>"接收方删除","id"=>$id);
					}else{
						return array("status"=>0,"msg"=>"删除失败","info"=>"接收方删除","id"=>$id);
					}				
				}else{
					$ret = $this->mod->where($where)->save(array("status_f"=>3));
					if($ret){
						return array("status"=>1,"msg"=>"删除成功","info"=>"发送方未删除","id"=>$id);
					}else{
						return array("status"=>0,"msg"=>"回收失败","info"=>"发送方未删除","id"=>$id);
					}	
				}
				
			}

		}else{//如果是系统群发信息
			$data['uid'] = intval($_SESSION['user_uid']);
			$data['name'] = $_SESSION['user_name'];
			$data['shieldId'] = $id;
			$mod = D("shield_msg");
			$where = "uid=".$data['uid']." or name='".$data['name']."' and shieldId = " .$id;
			$ret = $mod->where($where)->save(array("status"=>2));
			
			if($ret){
				
				return array("status"=>1,"msg"=>"操作成功","id"=>$id);
			}else{
				$data['status'] = 2;
				$ret = $mod->add($data);
				if($ret){
					return array("status"=>1,"msg"=>"操作成功","id"=>$id,'addid'=>$ret);
				}else{
					return array("status"=>1,"msg"=>"操作失败","id"=>$id);
				}
			}
		}
	
		
	}	
	
	//管理员删除
	public function adminDel($id){
		if(!$_SESSION['user_name'] ||$_SESSION['user_name'] !="admin" ){
			return array("msg"=>"没有权限");
		}
		$name = $_SESSION['user_name'];
		$uid = intval($_SESSION['user_uid']);
		$where = "id=".$id;		
		$list =  $this->mod->where($where)->find();
		if($list['hostname'] != "admin" || intval($list['type'])>=1){
			return array("msg"=>"不能删除");
		}
		$ret1 = $this->mod->where($where)->delete();
		$mod=D("shield_msg");
		$ret2 = $mod->where("shieldId=".$id)->delete();
		if($ret1 && $ret2){
			 return array("msg"=>"删除成功","id"=>$id);
		}elseif($ret1 && !$ret2){
			 return array("msg"=>"shield_msg删除失败，请手动删除","id"=>$id);
		}elseif(!$ret1 && $ret2){
			 return array("msg"=>"instation删除失败，请手动删除","id"=>$id);
		}else{
			 return array("msg"=>"instation删除失败","id"=>$id);
		}
	}
	
	//标记为已读 
	public function markRead($id,$type=1){
		if(!$_SESSION['user_name']){
			return array("msg"=>"请指定用户名或id");
		}
		$name = $_SESSION['user_name'];
		$uid = intval($_SESSION['user_uid']);
		if($type >= 1){
			$list = $this->mod->where("id=".$id)->find();
            if(intval($list['friendid']) == $uid || $list['friendname'] == $name){
				$ret = $this->mod->where("id=".$id)->save(array('rd'=>1));
			}					
			if($ret){
				return array("status"=>1,"msg"=>"操作成功","id"=>$id);
			}else{
				return array("status"=>0,"msg"=>"操作失败","id"=>$id);
			}
		}else{
			$data['uid'] = intval($_SESSION['user_uid']);
			$data['name'] = $_SESSION['user_name'];
			$data['shieldId'] = $id;
			$mod = D("shield_msg");
			$where = "uid=".$data['uid']." or name='".$data['name']."' and shieldId = " .$id;
			$ret = $mod->where($where)->save(array("rd"=>1));
			
			if($ret){
				
				return array("status"=>1,"msg"=>"操作成功","id"=>$id);
			}else{
				$data['rd'] = 1;
				$ret = $mod->add($data);
				//echo $mod->getLastSql();
				if($ret){
					return array("status"=>1,"msg"=>"操作成功","id"=>$id,'addid'=>$ret);
				}else{
					return array("status"=>1,"msg"=>"操作失败","id"=>$id);
				}
			}
		}
	}
	
	
	/**
	 *
	 * @站内信单条信息
	 * @id		站内信ID
	 *
	 */ 
	function msgSingle($id){
		$where="id =".$id;
		$list = $this->mod->where($where)->select();
		return $list;
	}
	
	
	
}
?>