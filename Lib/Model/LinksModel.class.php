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
class ShufflingModel extends RelationModel{
	protected $_validate = array(
				array('title','/^[\w\x{4e00}-\x{9fa5}]+$/u','标题有误！'),
				array('title','','标题已经存在',0,'unique'),
				array('url','/^(\w+:\/\/)?\w+(\.\w+)+.*$/','链接有误！'),
				array('img','require','图片有误！'),
                array('state','number','状态有误！'),
                array('order','number','排序有误！'),
		);
}
?>