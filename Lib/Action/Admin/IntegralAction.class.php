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
class IntegralAction extends AdminCommAction {
//--------积分商城----------
	//商品列表
	public function index(){
		$integral=M('integral');
		$list=$integral->field('id,title,kind,price,integral,category,state,sort')->order('`id` DESC')->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	//商品添加页
	public function addgoo(){
		$this->display();
	}

	//排序修改
    public function savegoo(){
		$integral=D('Integral');
		$id=$this->_post("id");
		$sort=$this->_post("sort");
		$state=$this->_post("state");
		if($integral->create()){
			  $result = $integral->where(array('id'=>$id))->save();		 			
		}else{
			 $this->error($integral->getError());
		}
    }
	
	//图片删除
	public function intExit(){
		$img='./Public/uploadify/uploads/goods/'.$this->_post("img");
		if(file_exists($img)){	//存在图片
			unlink($img);	//删除它
		}
    }
	
    //商品编辑页
    public function editgoo(){
            $Integral = M('Integral');
            $id=$this->_get("id");
            $edlist = $Integral->where('id='.$id)->select();
			$img=array_filter(explode(',',$edlist[0]['img']));
			$this->assign('img',$img);
            $this->assign('edlist',$edlist);
            $this->display();
    }

    //商品删除
    public function delego(){
			$id=$this->_get("id");
            $integral=M('integral');
			$inte=$integral->field('img')->where('id='.$id)->find();
			$img=array_filter(explode(',',$inte['img']));
            foreach($img as $i){	//先删除对应的图片
				unlink('./Public/uploadify/uploads/goods/'.$i);	//删除它
			}
            $result=$integral->where('id='.$id)->delete();	//再删除该条数据
			if($result){
				$this->success('删除成功', '__URL__');
			}else{
				$this->error("删除失败");
			}			
            
    }
	
	//兑换记录
	public function records(){
		$forrecord=D('Forrecord');
		$list=$forrecord->relation(true)->order('`time` DESC')->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	//发货页
	public function delivery(){
		$forrecord=D('Forrecord');
		$unite=M('unite');
		$unit=$unite->field('name,value')->where('`state`=0 and `pid`=16')->order('`order` asc,`id` asc')->select();
		$list=reset($forrecord->relation(true)->where('id='.$this->_get('id'))->select());
		$userinfo=R('dswjjd://Sharing/userinfo',array($list['uid'],'name,qq,fixed_line,cellphone'));
		unset($userinfo['username']);
		$list=array_merge($list,$userinfo);
		unset($userinfo);
		$this->assign('unit',$unit);
		$this->assign('list',$list);
		$this->display();
	}
	
	//快递跟踪显示
	public function deliveryAjax(){
		$express=R('dswjjd://Sharing/expressQuery',array($this->_post('name'),$this->_post('number')));	//快递跟踪记录
		if(!$express['data']){
			echo '
				<tr>
					<td>无数据</td>
				</tr>
				';
		}
		foreach($express['data'] as $exp){
		$content.='
		<tr>
			<td>'.$exp['time'].'</td>
			<td>'.$exp['context'].'</td>
	  	</tr>
		';
		}
		echo $content;
		echo '<tr class="red"><td>'.$express['end']['time'].'</td><td>'.$express['end']['context'].'</td></tr>';
	}
	
	//发货
	public function deliveryUpda(){
		$forrecord=D('Forrecord');
		if($create=$forrecord->create()){
			$create['audittime']=time();//审核时间
			$result = $forrecord->where(array('id'=>$this->_post('id')))->save($create);
			if($result){
				$this->success("发货成功","__URL__/records");
			
			}else{
				$this->error("发贷失败");
			}			 			
		}else{
			 $this->error($forrecord->getError());
		}
	}
	
	//撤销
	public function undo(){
		$forrecord=D('Forrecord');
		if($create=$forrecord->create()){
			$create['endtime']=time();//完成时间
			$result = $forrecord->where(array('id'=>$this->_post('id')))->save($create);
			if($result){
				if($this->_post('kind')==1){
					$ufees=M('ufees');
					$ufees->where('uid='.$this->_post('uid'))->setInc('`available`',$this->_post('integral')); //会员积分
				}else if($this->_post('kind')==2){
					$vip_points=M('vip_points');
					$vip_points->where('uid='.$this->_post('uid'))->setInc('`available`',$this->_post('integral'));	//VIP积分
				}else{
					$promote_integral=M('promote_integral');
					$promote_integral->where('uid='.$this->_post('uid'))->setInc('`available`',$this->_post('integral'));	//推广积分
				}
				$this->success("撤销成功","__URL__/records");
			
			}else{
				$this->error("撤销失败");
			}			 			
		}else{
			 $this->error($forrecord->getError());
		}
	}
	
	//导出EXCEL
	public function integExport(){
		$forrecord=D('Forrecord');
		$type=$this->_post('type')?"type=".$this->_post('type'):'';
		$list=$forrecord->relation(true)->where($type)->order('`time` asc')->select();
		$data['title']="积分兑换记录";
		$data['name']=array(
							array('n'=>'订单号','u'=>'indent'),
							array('n'=>'用户名','u'=>'username'),
							array('n'=>'兑换商品','u'=>'integral_title'),
							array('n'=>'兑换数量','u'=>'number'),
							array('n'=>'兑换积分','u'=>'integral'),
							array('n'=>'积分类型','u'=>'kind'),
							array('n'=>'兑换时间','u'=>'time'),
							array('n'=>'状态','u'=>'type'));
		foreach($list as $l){
			if($l['kind']==1){
				$kind="会员积分";
			}else if($l['kind']==2){
				$kind="VIP积分";
			}else{
				$kind="VIP推广积分";
			}
			if($l['type']==1){
				$type="待发货";
			}else if($l['kind']==2){
				$type="待收货";
			}else if($l['kind']==2){
				$type="已完成";
			}else{
				$type="失败";
			}
			$content[]=array(
							'indent'		=>" ".$l['indent'],
							'username'		=>$l['username'],
							'integral_title'=>$l['integral_title'],
							'number'		=>$l['number'],
							'integral'		=>$l['integral'],
							'kind'			=>$kind,
							'time'			=>date('Y-m-d H:i:s',$l['time']),
							'type'			=>$type,
							);
		}
		$data['content']=$content;
		$excel=$this->excelExport($data);
		
			$this->success("导出成功","__URL__/records");
		
	}
	
}
?>