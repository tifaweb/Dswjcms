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
class SystemModel extends Model{
	protected $_validate = array(
                array('state','require','参数说明有误！'),
				array('state','','参数说明已经存在',0,'unique'),
                array('prompt','/^[\w\x{4e00}-\x{9fa5}]+$/u','输入提示有误！',2),
               // array('value','require','参数值有误！',2),
                array('name','/^[\w\x{4e00}-\x{9fa5}]+$/u','变量名有误！'),
				array('name','','变量名已经存在',0,'unique'),
                array('type','number','表单类型有误！'),
		);
}
?>