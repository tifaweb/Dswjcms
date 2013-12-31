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
class AutomaticModel extends RelationModel{
	protected $_validate = array(
		array('type','number','有误！',2),
		array('title','/^[\w\x{4e00}-\x{9fa5}]+$/u','标题有误！'),
		array('total','number','有误！'),
		array('plan','number','有误！'),
		array('money','number','有误！',2),
		array('way','number','有误！'),
		array('candra','number','有误！'),
		array('rates','number','有误！',2),
		array('reward','number','有误！'),
	);
	
	protected $_link=array(
		'user'=> array(  
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'user',
            'foreign_key'=>'uid',
            'mapping_name'=>'user',
			'mapping_fields'=>'username',
			'as_fields'=>'username:username',
		),
	);
	
}
?>