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
class IntegralconfAction extends AdminCommAction {
//--------积分配置----------
	//会员积分配置
	public function index(){
		$unite=M('integralconf');
		$id=$this->_get('id')?$this->_get('id'):0;
		switch($id){
			case 0:
			$pname='会员积分配置';
			break;
			case 1:
			$pname='VIP积分配置';
			break;
			case 2:
			$pname='推广积分配置';
			break;
		}
		$list=$unite->where('`pid`='.$id)->select();
		$this->assign('list',$list);
		$this->assign('pname',$pname);
		$endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__URL__/editajax", {id:id});
}
		';
		$this->assign('endjs',$endjs);
		$this->display();
	}
	
	//编辑显示AJAX
    public function editajax(){
		$unite=D('Integralconf');
		$id=$this->_post("id");
		$list=$unite->where('`id`='.$id)->find();
		echo '
			<table class="table">
        <tbody>
          <tr>
            <td>变量名：</td>
            <td><input name="name" type="text" class="span6" placeholder="请输入变量名..." value="'.$list['name'].'"></td>
          </tr>
          <tr>
            <td>积分：</td>
            <td><input name="value" type="text" class="span6" placeholder="请输入积分..." value="'.$list['value'].'"></td>
          </tr>
		  <tr>
            <td>说明：</td><td> <textarea name="state" rows="3" placeholder="说明不要超过100个字...">'.$list['state'].'</textarea></td>
          </tr>
		  <input name="sid" type="hidden" value="'.$id.'" />
        </tbody>      
    </table>
		';
    }
	
	//删除
    public function exitgan(){
		$unite=D('Integralconf');
		$result = $unite->where(array('id'=>$this->_get('id')))->delete();
		if($result){
			$this->Record('会员积分删除成功');//后台操作
			 $this->success("删除成功");
				
		}else{
			$this->Record('会员积分删除失败');//后台操作
			$this->error("删除失败");
		}		
	}
	
}
?>