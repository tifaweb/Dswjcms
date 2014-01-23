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
class WeixinAction extends CommAction {
    /**
	 *
	 *获取微信所有用户
	 * @account			微账号
	 * @password		微密码
	 * @datapath		cookie保存位置
	 * @debug			错误是否开启 true false
	 *
	 */
	public function weiUserlist(){
		$systems=R('Sharing/systems');
		//$weiuser=M('weiuser');
		//模似登陆微信
		$options = array(
		'account'=>$systems['sys_weiAccount'],
		'password'=>$systems['sys_weiPassword'],
		'datapath'=>'./temp/Data/cookie',
			'debug'=>true,
			'logcallback'=>'logdebug'	
		); 
		import("ORG.Weixin.Wechatext");
		$wechat = new Wechatext($options);
		// 获取所有用户		需添加分页跳转处理
		if ($wechat->checkValid()) {
			$userlist = reset($wechat->getUserlist());
			print_r($wechat->getInfo('2121015543'));
			exit;
			$weiuser->query('TRUNCATE ds_weiuser');	//清空表
			foreach($userlist as $id=>$ut){
				$array['fakeid']			=$ut['id'];
				$array['nick_name']			=$ut['nick_name'];
				$array['remark_name']		=$ut['remark_name'];
				$array['group_id']			=$ut['group_id'];
				$weiuser->add($array);
			}
			$this->success('更新成功','__URL__/index');
		} else {
			echo "login error";
		}
	}
	
	/**
	 *
	 *微信错误保存
	 * @text			内容
	 *
	 */
	public function logdebug($text){
		file_put_contents('./temp/Data/data/log.txt',$text."\n",FILE_APPEND);		
	}
}
?>