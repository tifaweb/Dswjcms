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
class UserModel extends RelationModel{
	protected $_validate = array(
		array('proving','require','验证码必须！'), 
		array('proving','checkCode','验证码错误!',0,'callback',1),
		array('email','email','email有误！'),
		array('email','','邮箱已被注册！',0,'unique',1),
		array('username','require','用户必须填写！'),
		array('username','','用户已经存在！',0,'unique',1),
		array('username','/^\w{2,}$/','用户名必须2个字母以上！',0,'regex',1),
		array('passwd','require','原始密码必须！'), 
		array('passw','require','密码必须！'), 
		array('password','require','重复密码必须！'), 
		array('passw','password','确认密码不正确！',0,'confirm'), 
		array('pay_pasd','require','原始交易密码必须！'),
		array('npay_password','require','交易密码必须！'), 
		array('npay_password','pay_password','重复交易密码必须！',0,'confirm'), 
		array('uid','number','推荐人ID有误！',2),
	);
	
	protected $_auto = array ( 
		array('password','userMd5',3,'callback',1), 
		array('pay_password','userPayMd5',3,'callback',1), 
		array('time','time',3,'function',1), 
	);
	
	protected $_link=array(
		'money'=> array(  
			'mapping_type'=>HAS_ONE,
			'class_name'=>'money',
            'foreign_key'=>'uid',
            'mapping_name'=>'money',
			'mapping_fields'=>'total_money,available_funds,freeze_funds,due_in,stay_still,stay_interest,make_interest,make_reward,overdue',
			'as_fields'=>'total_money:total_money,available_funds:available_funds,freeze_funds:freeze_funds,due_in:due_in,stay_still:stay_still,stay_interest:stay_interest,make_interest:make_interest,make_reward:make_reward,overdue:overdue',
		),
		'promote_integral'=> array(  
			'mapping_type'=>HAS_ONE,
			'class_name'=>'promote_integral',
            'foreign_key'=>'uid',
            'mapping_name'=>'promote_integral',
			'mapping_fields'=>'total,available,freeze',
			'as_fields'=>'total:promote_total_score,available:promote_available_integral,freeze:promote_freezing_points',
		),
		'vip_points'=> array(  
			'mapping_type'=>HAS_ONE,
			'class_name'=>'vip_points',
            'foreign_key'=>'uid',
            'mapping_name'=>'vip_points',
			'mapping_fields'=>'total,available,freeze,audit,checktime,deadline,unit,opening_time,expiration_time',
			'as_fields'=>'total:vip_total_score,available:vip_available_integral,freeze:vip_freezing_points,audit:vip_audit,checktime:vip_checktime,deadline:vip_deadline,unit:vip_unit,opening_time:vip_opening_time,expiration_time:vip_expiration_time',
		),
		'ufees'=> array(  
			'mapping_type'=>HAS_ONE,
			'class_name'=>'ufees',
            'foreign_key'=>'uid',
            'mapping_name'=>'ufees',
			'mapping_fields'=>'total,available,freeze',
			'as_fields'=>'total:member_total_score,available:member_available,freeze:member_freeze',
		),
		'userinfo'=> array(  
			'mapping_type'=>HAS_ONE,
			'class_name'=>'userinfo',
            'foreign_key'=>'uid',
            'mapping_name'=>'userinfo',
		),
		
		'user_commision'=>array(
			'mapping_type'=>HAS_ONE,
			'class_name'=>'user_commision',
            'foreign_key'=>'uid',
		),
	);
	
	protected function checkCode($code){
		if(md5($code)!=session('verify')){
			return false;
		}else{
			return true;
		}
	}
		
	public function userMd5($password){
		return MD5(substr(MD5(substr(MD5($password),2,30).DS_ENTERPRISE),10,20).DS_EN_ENTERPRISE);
	}
	
	public function userPayMd5($password){
		return MD5(substr(MD5(substr(MD5($password),5,24).DS_EN_ENTERPRISE),20,5).DS_ENTERPRISE);
	}
	public function _after_insert($data,$options){
		if($_REQUEST['gid']){
			$mod = M("User");
			$list = $mod->where('id="'.intval($_REQUEST['uid']).'"')->find();
			
   
			$catpid = $list['catpid'];
			if(strlen($catpid) >1){
				$arr = explode("-",$catpid);
				if(!intval($arr[0])){
					$str = substr($catpid,2,strlen($catpid)-1);
					
				}else{
				   $str =   $catpid."-".$list['id'];
				}
			}else{
			   $str =   $list['id'];
			}

		   $this->where(array('id'=>$data['id']))->save(array('catpid'=>$str));
			

			$data['uid'] = $data['id'];
			$data['group_id'] = intval($_REQUEST['gid']);
			$ret=M("user_commision")->add($data);

			
		}
	
		
	}
}
?>