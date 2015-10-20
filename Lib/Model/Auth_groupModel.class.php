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
class Auth_groupModel extends  RelationModel{
	protected $_validate = array(
		array('title','require','分组名必须！'),
		array('fid','require','分组必须！'),
	);
	public function _after_delete($data,$options){

		$id=intval($_REQUEST['id']);
		$mod = D("Auth_group_access");
		$mod->where('group_id="'.$id.'"')->delete();
	}
	

	
}

?>
