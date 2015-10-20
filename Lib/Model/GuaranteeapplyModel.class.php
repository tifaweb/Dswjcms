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
class GuaranteeapplyModel extends RelationModel{
	protected $_validate = array(
		array('proving','require','验证码必须！'), 
		array('proving','checkCode','验证码错误!',0,'callback',1),
		array('name','require','企业名称有误！'),
		array('contact','require','联系人有误！'),
		array('cellphone','number','手机号码有误！'),
		array('money','number','意向融资金额有误！'),
		array('location','number','所在区域有误！'),
		array('deadline','number','预期融资期限有误！'),
		array('industry','number','所属行业有误！'),
		array('scope','require','备注说明有误！'),
	);
	
	protected $_auto = array ( 
		array('time','time',1,'function',1), 
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
			'mapping_fields'=>'username',
			'as_fields'=>'username:username',
		),
		'guarantee'=> array(  
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'guarantee',
            'foreign_key'=>'id',
            'mapping_name'=>'guarantee',
		),
	);
}
?>