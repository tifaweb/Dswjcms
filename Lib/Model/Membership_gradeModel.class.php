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
class Membership_gradeModel extends Model{
	protected $_validate = array(
                array('name','/^[\w\x{4e00}-\x{9fa5}]+$/u','积分名称名称有误！'),
				array('img','require','图片必须！'),
				array('min','number','最小值有误！'),
				array('max','number','最大值有误！'),
		);
}
?>