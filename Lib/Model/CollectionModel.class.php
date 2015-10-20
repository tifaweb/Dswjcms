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
class CollectionModel extends RelationModel{
	protected $_link=array(
		'borrowing'=> array(  
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'borrowing',
            'foreign_key'=>'bid',
            'mapping_name'=>'borrowing',
			'mapping_fields'=>'title,type,candra,way',
			'as_fields'=>'title:title,type:types,candra:candra,way:boway',
		),
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