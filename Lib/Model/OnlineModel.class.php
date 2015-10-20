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
class OnlineModel extends Model{
	protected $_validate = array(
				array('pid','require','合作者ID有误！'),
                array('checking','require','密钥有误！'),
				array('account','require','收款账户有误！',2),
				array('introduce','require','介绍有误！'),
				array('state','number','状态有误！'),
				array('order','number','排序有误！'),
		);
}
?>