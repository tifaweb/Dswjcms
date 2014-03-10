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
class SmtpModel extends Model{
	protected $_validate = array(
                array('smtp','require','SMTP服务器有误！'),
                array('validation','require','错误'),
                array('send_email','email','邮箱地址有误！'),
                array('password','require','邮箱密码有误！'),
				array('addresser','require','发件人有误！'),
		);
}
?>