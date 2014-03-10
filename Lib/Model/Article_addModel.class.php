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
class Article_addModel extends Model{

	protected $_auto=array(

		array('modified','time',1,'function'),

	);  

   

/*	  public   function _after_insert(&$data,$options){
			
		
         $mod = D("Article");
		 $da['fid']=$data['id'];
		 $field = $mod->getDbFields();
		 foreach($_POST as $k=>$v){
			 if(in_array($k,$field)){
				 $da[$k]=$v;
			 }
		 }

		 $mod->add($da);
		
		 

	}*/

	
}

?>
