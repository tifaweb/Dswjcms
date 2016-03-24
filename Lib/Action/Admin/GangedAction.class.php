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
class GangedAction extends AdminCommAction {
//--------联动显示-----------
    public function index(){
		$unite=M('unite');
		$id=$this->_get('id')?$this->_get('id'):1;
		switch($id){
			case 1:
			$pname='借款用途';
			break;
			case 2:
			$pname='借款期限（月）';
			break;
			case 3:
			$pname='借款期限（天）';
			break;
			case 4:
			$pname='还款方式';
			break;
			case 5:
			$pname='有效时间';
			break;
			case 6:
			$pname='最低投标金额';
			break;
			case 7:
			$pname='最高投标金额';
			break;
			case 8:
			$pname='学历';
			break;
			case 9:
			$pname='月收入';
			break;
			case 10:
			$pname='住房条件';
			break;
			case 11:
			$pname='购车情况';
			break;
			case 12:
			$pname='当前所在行业';
			break;
			case 13:
			$pname='56个民族';
			break;
			case 14:
			$pname='开户银行';
			break;
			case 15:
			$pname='充值类型';
			break;
			case 17:
			$pname='后台栏目分组';
			break;
		}
		$list=$unite->where('`pid`="'.$id.'"')->select();
		
		$this->assign('list',$list);
		$this->assign('pname',$pname);
		$endjs='
//编辑
function edit(id){
	var loading=\'<div class="invest_loading"><div><img src="__PUBLIC__/bootstrap/img/ajax-loaders/ajax-loader-1.gif"/></div><div>加载中...</div> </div>\';
	$(".integral_subject").html(loading);
		$("#edits").load("__APP__/TIFAWEB_DSWJCMS/Ganged/editajax", {id:id});
}
		';
		$this->assign('endjs',$endjs);
        $this->display();
    }
	
	//排序修改
    public function savegan(){
		$integral=D('Unite');
		$id=$this->_post("id");
		if($integral->create()){
			  $result = $integral->where(array('id'=>$id))->save();		 			
		}else{
			 $this->error($integral->getError());
		}
    }
	
	//编辑显示AJAX
    public function editajax(){
		$unite=D('Unite');
		$id=$this->_post("id");
		$list=$unite->where('`id`="'.$id.'"')->find();
		echo '
			<table class="table">
        <tbody>
          <tr>
            <td>联动名：</td>
            <td><input name="name" type="text" class="span6" placeholder="请输入联动名..." value="'.$list['name'].'"></td>
          </tr>
          <tr>
            <td>联动值：</td>
            <td><input name="value" type="text" class="span6" placeholder="请输入联动值..." value="'.$list['value'].'"></td>
          </tr>
          <tr>
            <td>状态：</td>
            <td class="form-inline">';
			if($list['state']==0){
				echo '
				<label class="radio"><input type="radio" name="state" value="0" checked/> 显示</label>
                <label class="radio"><input type="radio" name="state" value="1" /> 隐藏</label>';
			}else{
				echo '
				<label class="radio"><input type="radio" name="state" value="0" /> 显示</label>
                <label class="radio"><input type="radio" name="state" value="1" checked/> 隐藏</label>';
			}
            echo '</td>
          </tr>
		  <input name="sid" type="hidden" value="'.$id.'" />
        </tbody>      
    </table>
		';
    }
	
	//删除联动
    public function exitgan(){
		$unite=D('Unite');
		$result = $unite->where(array('id'=>$this->_get('id')))->delete();
		if($result){
			$this->Record('删除联动成功');//后台操作
			 $this->success("删除成功");
				
		}else{
			$this->Record('删除联动失败');//后台操作
			$this->error("删除失败");
		}		
	}
}
?>