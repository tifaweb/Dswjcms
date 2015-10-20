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
class GuaranteecompModel extends RelationModel{
	protected $_validate = array(
		array('proving','require','验证码必须！'), 
		array('proving','checkCode','验证码错误!',0,'callback',1),
		array('name','require','担保公司名称有误！'),
		array('logo','require','LOGO有误！'),
		array('qq','number','qq有误！'),
		array('cellphone','number','手机号码有误！'),
		array('fixed_line','number','固话有误！'),
		array('img[]','require','证件有误！'),
		array('keyword','require','关键字有误！'),
		array('remark','require','描述有误！'),
		array('content','require','公司介绍有误！'),
	);
	
	protected function checkCode($code){
		if(md5($code)!=session('verify')){
			return false;
		}else{
			return true;
		}
	}
}
?>