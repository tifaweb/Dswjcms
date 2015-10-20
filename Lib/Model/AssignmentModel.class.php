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
class AssignmentModel extends RelationModel{
	protected $_validate = array(
		array('bid','number','有误！'),
		array('coefficient','number','系数有误！'),
		array('subscribe','number','每份认购金额有误！'),
	);
	
	protected $_link=array(
		'borrowing'=> array(  
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'borrowing',
            'foreign_key'=>'bid',
            'mapping_name'=>'borrowing',
		),
	);
	
}
?>