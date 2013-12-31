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
class InstationModel extends  RelationModel{

//  protected $_validate = array (
//	  array ('', 'require', 'Firstname is required!' ),
//  );
	protected $_auto=array(

	array('addline','time',1,'function'),

	);

	protected $_link = array(
//	   'property'=> array(  
//          'mapping_type'=>HAS_ONE,
//          'class_name'=>'property',
//          'foreign_key'=>'hostid',
//          'mapping_name'=>'property',
//          'as_fields'=>'username,email,points,avatar_small,avatar_big',
//	  ),


    );
	
  	
	
	public function getMsg($uid=0){
		if(!$uid){
			return false;
		}
		$where['hostid'] = $uid;
		$where['friendid'] = $uid;
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$map['type'] = 2;
		$map['status'] = 1;
		
	}
	
/*    public function _after_insert(&$data,$options){
		if(!$data['friendName']){
		  $Model = new Model(); 
		  
		  $Model->execute("update `ec_property` set msg =msg +1 WHERE id <>0");
		}else{
			$uid = $data['friendid'];
		  $Model = new Model(); 
		  
		  $Model->execute("update `ec_property` set msg =msg +1 WHERE uid =".$uid);
		}

	}*/	
	

	
}

?>
