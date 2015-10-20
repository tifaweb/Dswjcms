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
class OfflineModel extends Model{
	protected $_validate = array(
				array('type','number','充值类型有误！'),
                array('bank_name','/^[\w\x{4e00}-\x{9fa5}]+$/u','开户支行名称有误！'),
				array('name','/^[\w\x{4e00}-\x{9fa5}]+$/u','收款人称有误！'),
				array('bank','number','银行账户有误！'),
				array('order','number','排序有误！'),
		);
}
?>