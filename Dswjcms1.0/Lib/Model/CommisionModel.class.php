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
class CommisionModel extends  Model{

  protected $_validate = array (
	  array('name','','名称已被注册！',0,'unique',1),
  );
	protected $_auto=array(

	    array('addtime','time',1,'function'),
       array('catpid','setCatpid',3,'callback',1), 

	);

  protected function setCatpid(){
	  $catpid = $_REQUEST['catpid'];
	  if(strlen($catpid) >1){
		  $arr = explode("-",$catpid);
		  if(!intval($arr[0])){
			  $str = substr($catpid,2,strlen($catpid)-1);
			  return $str;
		  }else{
			 return  $catpid;
		  }
	  }else{
		 return $_REQUEST['catpid'];
	  }
	  
  }

	

	

	
}

?>
