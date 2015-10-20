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
class IntegralModel extends RelationModel{
	protected $_validate = array(
		array('title','require','商品名称有误！'),
		array('title','','商品名称已经存在',0,'unique'),
		array('price','/^\d{1,}\.{0,1}\d{0,2}$/','价格有误！'),
		array('kind','number','积分类型有误！'),
		array('integral','number','积分值有误！'),
		array('number','number','数量有误！'),
		array('days','number','期限有误！'),
		array('deadline','number','期限单位有误！'),
		array('amount','number','可兑换数量有误！'),
		array('category','number','类目有误！'),
		array('content','require','内容有误！',2),
		array('state','number','状态有误！'),
		array('sort','number','排序有误！'),
	);
	
	protected $_auto = array ( 
		array('time','time',3,'function',1),
		array('img','imgArr',3,'callback',1), 
	);
	
	public function imgArr($img){
		return implode(",",$img);
	}
}
?>