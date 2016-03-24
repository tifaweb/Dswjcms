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
class PluginsAction extends AdminCommAction {
	public function index(){
		$directory=$this->templateDatas('./Lib/Plugin');
		$this->assign('directory',$directory);
		$this->display();
	}
	
	/**
	*
	* @获取插件
	* @dirname	要遍历的目录名字
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	protected function templateDatas($dirname){
		$templateDatas=F('templateDatas');
		if($templateDatas['num']>0){
			return $templateDatas;
		}else{
			$dir_handle=opendir($dirname);
			while($file=readdir($dir_handle))
			{
			 if($file!="."&&$file!="..")
			 {
				 
				$ex=explode(".",$file);
				
				if($ex[1]==''){
					
					$fileget=file_get_contents('./Lib/Plugin/'.$file.'/explain.tf');
					$fileget=json_decode($fileget, true);
					
					$array['data'][]=$fileget;
					unset($fileget);
					$num++;
				}
			 }
			}
			
			$array['num']=$num;
			closedir($dir_handle);
			F('templateDatas',$array);
			return $array;
		}
	}
	
	//ajax安装、更新、卸载
	public function ajaxplugins(){
		$type=$this->_post('type');
		$name=$this->_post('name');
		F('templateDatas',NULL);
		import('@.Plugin.'.$name.'.'.$name);
		$install = new $name();
		if($type==1){	//安装
			$install->install();
			
		}else if($type==2){	//更新
		
		}else if($type==3){	//卸载
			$install->delete();
		}else{
			$this->ajaxReturn(0,"插件不完整，请重装安装",0);
		}
	}
	
	//显示说明和配置
	public function ajaxdata(){
		$id=$this->_post('id');
		$type=$this->_post('type');
		
		$directory=$this->templateDatas('./Lib/Plugin');
		$data=$directory['data'][$id];
		if($type==1){	//配置
			foreach($data['allocation'] as $id=>$a){
				$json.='
				  <tr>
					<td>
						   '.$a['name'].'：
					</td>
					<td>
					  <input name="'.$id.'" type="text" class="span5" placeholder="'.$a['name'].'" value="'.$a['value'].'">
					</td>
				  </tr>
				';
			}
			$json.='<input name="logo" type="hidden" value="'.$data['name'].'" />';
		}else{	//说明
			$json=$data['instructions'];
		}
		echo $json;
	}
	
	//配置保存
	public function configurationSave(){
		$logo=$this->_post('logo');
		$explain=file_get_contents('./Lib/Plugin/'.$logo.'/explain.tf');
		$explain=json_decode($explain, true);
		foreach($explain['allocation'] as $id=>$all){
			$explain['allocation'][$id]['value']=$this->_post($id);
		}
		$explain=json_encode($explain);
		file_put_contents('./Lib/Plugin/'.$logo.'/explain.tf', $explain);
		F('templateDatas',NULL);
		$this->success("保存成功");
	}
	
	//插件提交
	public function addpluginss(){
		$explain['title']=$this->_post('title');
		$explain['author']=$this->_post('author');
		$explain['name']=$this->_post('name');
		$explain['describe']=$this->_post('describe');
		$explain['instructions']=$_POST['instructions'];
		$explain['version']=$this->_post('version');
		//生成插件目录及文件
		$dir='./Lib/Plugin/'.$this->_post('name');
		mkdir ( $dir, true ) or die ( '创建文件夹失败' );
		fopen($dir."/explain.tf", "w");
		fopen($dir."/install.tf", "w");
		fopen($dir."/".$this->_post('name').".class.php", "w");
		$explain=json_encode($explain);
		file_put_contents('./Lib/Plugin/'.$this->_post('name').'/explain.tf', $explain);
		$class='<?php
// +----------------------------------------------------------------------
// | '.$this->_post('name').' 
// +----------------------------------------------------------------------
defined(\'THINK_PATH\') or exit();
import(\'@.Plugin.Basis\');
class '.$this->_post('name').' extends Basis {
	/**
	 * 构造方法
	 */
	public function __construct(){
		$this->pluginname=\''.$this->_post('name').'\';
	}
	//插件安装
	public function install(){
		$this->Dssucceed(\'插件安装成功\');
	}
	
	//插件删除
	public function delete(){
		$this->Dssucceed(\'插件已成功卸载\');
	}
	
	//插件更新
	public function renewal(){
		
	}
}
?>
		';
		file_put_contents('./Lib/Plugin/'.$this->_post('name').'/'.$this->_post('name').'.class.php', $class);
		F('templateDatas',NULL);
		$this->success("插件创建成功","__APP__/TIFAWEB_DSWJCMS/Plugins/index.html");
	}
	
	//刷新插件
	public function ajaxPluginsRefresh(){
		F('templateDatas',NULL);
	}
}
?>