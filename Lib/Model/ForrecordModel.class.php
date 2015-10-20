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
class ForrecordModel extends RelationModel{
	protected $_validate = array(
		array('proving','require','验证码必须！'), 
		array('proving','checkCode','验证码错误!',0,'callback',1),
		array('gid','number','商品ID有误！'),
		array('location','require','收货地区有误！'),
		array('detailed','require','收货详细地址有误！'),
		array('number','number','数量有误！'),
		array('integral','number','兑换积分有误！'),
		array('company','/^\w+$/u','物流公司有误！'),
		array('indent','/^[\w\x{4e00}-\x{9fa5}]+$/u','运单号有误！'),
		array('explain','require','撤销说明必须！'),
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
		'integral'=> array(  
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'integral',
            'foreign_key'=>'gid',
            'mapping_name'=>'integral',
			'mapping_fields'=>'title,category',
			'as_fields'=>'title:integral_title,category:integral_category',
		),
	);
}
?>