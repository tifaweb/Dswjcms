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
class User_commisionModel extends  RelationModel{


	protected $_link = array(
	   'user'=> array(  
          'mapping_type'=>BELONGS_TO,
          'class_name'=>'user',
          'foreign_key'=>'uid',
          'as_fields'=>'username,email,id,time',
	  ),
	  
	  'commision'=> array(  
          'mapping_type'=>BELONGS_TO,
          'class_name'=>'commision',
          'foreign_key'=>'group_id',
          'as_fields'=>'pid,catpid,name,level,ratio,status',
	  ),


    );
	

	
}

?>
