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
class GuaranteeModel extends RelationModel{
	protected $_validate = array(
		array('proving','require','验证码必须！'), 
		array('proving','checkCode','验证码错误!',0,'callback',1),
		array('agelimit','number','注册年限有误！'),
		array('assets','number','注册资金有误！'),
		array('worth','number','净值资产有误！'),
		array('annual','number','上年度企业经营现金流入有误！'),
		array('key','number','是否省、市重点企业有误！'),
		array('business','require','经营情况有误！'),
		array('reporting','require','征信记录有误！'),
		array('guarantee','require','担保情况有误！'),
		array('cguarantee','require','反担保情况有误！'),
		array('review','require','风险控制综述有误！'),
		array('financial','require','财务状况有误！'),
	);
	
	protected function checkCode($code){
		if(md5($code)!=session('verify')){
			return false;
		}else{
			return true;
		}
	}
	
	protected $_link=array(
		'guaranteeapply'=> array(  
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'guaranteeapply',
            'foreign_key'=>'gid',
            'mapping_name'=>'ply',
			'mapping_fields'=>'gcompany',
			'as_fields'=>'gcompany:gcompany',
		),
	);
}
?>