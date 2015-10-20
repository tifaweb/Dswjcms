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
class BorrowingModel extends RelationModel{
	protected $_validate = array(
		array('proving','require','验证码必须！'), 
		array('proving','checkCode','验证码错误!',0,'callback',1),
		array('title','require','标题有误！'),
		array('rates','/^\d{1,}\.{0,1}\d{0,2}$/','年利率有误！'),
		array('money','number','借款金额有误！'),
		array('surplus','number','剩余金额有误！',2),
		array('mortgage','require','抵押物信息有误！',2),
		array('data','require','资料上传至少一张图！'),
		array('use','require','借款用途有误！'),
		array('deadline','require','借款期限有误！'),
		array('flow_deadline','require','流转期限有误！'),
		array('min_limit','require','最低认购期限有误！'),
		array('candra','require','借款期限有误！'),
		array('way','require','还款方式有误！'),
		array('valid','require','有效时间有误！'),
		array('code','require','密码标有误！',2),
		array('password','require','密码有误！',2),
		array('min','require','最低投标金额有误！'),
		array('max','require','最高投标金额有误！'),
		array('privacy','require','公开隐私有误！'),
		array('content','require','详细说明有误！',2),
		array('review_note','require','审核备注有误！',2),
	);
	
	protected function checkCode($code){
		if(md5($code)!=session('verify')){
			return false;
		}else{
			return true;
		}
	}
	protected $_link=array(
		'user'=> array(  
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'user',
            'foreign_key'=>'uid',
            'mapping_name'=>'user',
			'mapping_fields'=>'username,time',
			'as_fields'=>'username:username,time:join_date',
		),
	);
}
?>