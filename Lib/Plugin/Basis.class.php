<?php
// +----------------------------------------------------------------------
// | Dswjcms
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.dswjcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
// +----------------------------------------------------------------------
// | Author: 宁波市鄞州区天发网络科技有限公司 <dianshiweijin@126.com>
// +----------------------------------------------------------------------
defined('THINK_PATH') or exit();
class Basis{
	/**
	 * @移动图片
	 * @original	原图片		
	 * @objective	目标
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	protected function Dsrenames($original,$objective){
		if (file_exists ( $original )) {
			rename($original,$objective);	//移动文件
			return 1;
		}else{
			$this->Dserror('移动文件出错，请重新下载安装');
		}
	}
	
	/**
	 * @错误提示
	 * @data	提示的内容
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	protected function Dserror($data){
		$msg['status']=0;
		$msg['info']=$data;
		exit(json_encode($msg));
		//echo $data;
	}
	
	/**
	 * @成功提示
	 * @data	提示的内容
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	protected function Dssucceed($data){
		$msg['status']=1;
		$msg['info']=$data;
		exit(json_encode($msg));
		//echo $data;
	}
	
	/**
	 * @记录配置
	 * @arr		配置信息
	 * @logo	接口标识
	 * @type	1检测2记录
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	protected function Dsdata($arr,$logo,$type){
		
		if (file_exists('./Lib/Plugin/'.$logo.'/install.tf')) {
			
			$fileget=file_get_contents('./Lib/Plugin/'.$logo.'/install.tf');
			$fileget=json_decode($fileget, true);
			if($fileget['version']>$arr['version']){
				$this->Dserror('当前插件版本高于需要安装的插件版本');
			}else if($fileget['version']==$arr['version']){
				$this->Dserror('插件已安装，请不要重复安装');
			}else if($fileget['version'] && $fileget['version']<$arr['version']){
				$this->Dserror('当前插件版本较低，请进行更新操作');
			}
			
			if($type !=1){
				$update_str=json_encode($arr);
				file_put_contents('./Lib/Plugin/'.$logo.'/install.tf', $update_str);
				$explain=file_get_contents('./Lib/Plugin/'.$logo.'/explain.tf');
				$explain=json_decode($explain, true);
				$explain['type']=1;
				$explain['allocationtype']=$arr['allocationtype'];
				$explain['allocation']=$arr['allocation'];
				$explain=json_encode($explain);
				file_put_contents('./Lib/Plugin/'.$logo.'/explain.tf', $explain);
			}
		}else{
			$this->Dserror('插件不完整，请重新下载安装');
		}
	}
	
	/**
	 * @删除配置信息
	 * @logo	接口标识
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	protected function Dsdeletedata($logo){
		
		if (file_exists('./Lib/Plugin/'.$logo.'/install.tf')) {
			$update_str='';
			file_put_contents('./Lib/Plugin/'.$logo.'/install.tf', $update_str);
			$explain=file_get_contents('./Lib/Plugin/'.$logo.'/explain.tf');
			$explain=json_decode($explain, true);
			$explain['type']=2;
			$explain=json_encode($explain);
			file_put_contents('./Lib/Plugin/'.$logo.'/explain.tf', $explain);
			return 1;
		}else{
			$this->Dserror('插件不完整，无法删除');
		}
	}
	
	/**
	 * @修改指定文件内容
	 * @path	文件路径
	 * @find	查找的内容（正则表达式）
	 * @starttags	原标记开始位，用于添加新内容时保留原查找的标记位
	 * @endtags	原标记结束位可为空
	 * @replace 替换的内容
	 * @logo	接口标识
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	protected function DsreadFile($path,$find,$starttags='',$endtags='',$replace,$logo){
		if($endtags){
			$endtags='
			'.$endtags;
		}
		if (file_exists($path)) {
			$patharr=explode("/",$path);
			if($patharr[1]=='Tpl'){	//HTML注释
				$replacestart='<!--';
				$replaceend='-->';
			}else{	//其它注释
				$replacestart='/*';
				$replaceend='*/';
			}
			$fileget=file_get_contents($path);
			$replace=$starttags.$replacestart.$logo.' start'.$replaceend.$replace.$replacestart.$logo.' end'.$replaceend.$endtags;
			
			$update_str = preg_replace($find, $replace, $fileget); 
			file_put_contents($path, $update_str);
			return 1;
		}else{
			$this->Dserror('插件安装失败，请检查项目是否有读写权限或请重新下载');
		}
	}
	
	/**
	 * @删除指定内容
	 * @path	文件路径
	 * @logo	接口标识
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	protected function DsdeleteFile($path,$logo){
		if (file_exists($path)) {
			$patharr=explode("/",$path);
			if($patharr[1]=='Tpl'){	//HTML注释
				$replacestart='<!--';
				$replaceend='-->';
			}else{	//其它注释
				$replacestart='\/\*';
				$replaceend='\*\/';
			}
			$fileget=file_get_contents($path);
			$find="/".$replacestart.$logo." start".$replaceend.".*?".$replacestart.$logo." end".$replaceend."/s";;
			$update_str = preg_replace($find,'', $fileget); 
			file_put_contents($path, $update_str);
			return 1;
		}else{
			 $this->Dserror('插件删除失败，请检查项目是否有读写权限或请重新下载');
		}
	}
	
	/**
	 * @删除数据库
	 * @path	文件路径
	 * @logo	接口标识
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	protected function DsdeleteDatabase($logo){
		if ($logo) {
			$fileget=file_get_contents('./Lib/Plugin/'.$logo.'/install.tf');
			$fileget=json_decode($fileget, true);
			foreach($fileget['data'] as $id=>$f){
				M($id)->where('`id`='.$f)->delete();
			}
			
			return 1;
		}else{
			 $this->Dserror('插件删除失败，请检查项目是否有读写权限或请重新下载');
		}
	}
}