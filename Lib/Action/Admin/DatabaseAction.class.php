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
defined('THINK_PATH') or exit();
class DatabaseAction extends AdminCommAction {
//--------数据库显示-----------
     public function index(){
      	import('ORG.Custom.backupsql');
		$db = new DBManage ( C('DB_HOST'),C('DB_USER'), C('DB_PWD'), C('DB_NAME'), 'utf8' );
		
		$tables=$db->list_tables('dswjjd');
		$this->assign('audit',$tables);
        $this->display();
    }
	//数据表添加页
	public function editdat(){
		import('ORG.Custom.backupsql');
		$db = new DBManage ( C('DB_HOST'),C('DB_USER'), C('DB_PWD'), C('DB_NAME'), 'utf8' );
		$tables=$db->list_tables('dswjjd',$this->_get('table'));
		$this->assign('audit',$tables);
        $this->display();
    }
	
	//数据表添加
	public function addda(){
		$Model = new Model();
		$table=$this->_post('table');//表名
		$ttype=$this->_post('ttype');//表类型
		$coding=$this->_post('coding');//表编码
		$field=$this->_post('field');//字段名称
		$type=$this->_post('type');//字段类型
		$key=$this->_post('key');//字段关键
		$unique=$this->_post('unique');//字段唯一
		$null=$this->_post('null');//字段空
		$default=$this->_post('default');//字段默认
		$extra=$this->_post('extra');//字段额外
		foreach($field as $id=>$fd){
			$nulls=$null[$id]==1?"NULL":"NOT NULL";
			$add.="`$fd` $type[$id] $nulls $extra[$id],";
		}
		$Model->query("CREATE TABLE IF NOT EXISTS `$table` ( $add PRIMARY KEY (`$key`),UNIQUE KEY `".$unique."_2` (`$unique`), KEY `$key` (`$key`)) ENGINE=$ttype DEFAULT CHARSET=$coding AUTO_INCREMENT=1 ;"); 
		$this->success('添加成功', '__URL__');
	}
	
	//查看数据表SQL
	public function viewda(){
		import('ORG.Custom.backupsql');
		$db = new DBManage ( C('DB_HOST'),C('DB_USER'), C('DB_PWD'), C('DB_NAME'), 'utf8' );
		$backup=$db->Sqlbackup($this->_post('table'));
		echo $backup;
	}
	
	//数据表删除
	public function deleteda(){
		$Model = new Model();
		$Model->query("DROP TABLE ".$this->_get('table'));
		$this->success('删除成功', '__URL__');	
	}
}
?>