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
class UniteModel extends RelationModel{
	protected $_validate = array(
		array('name','require','联动名有误！'),
		array('value','require','联动值有误！'),
		array('state','number','状态有误！'),
		array('pid','number','所属联动有误！'),
		array('order','number','排序有误！'),
	);
}
?>