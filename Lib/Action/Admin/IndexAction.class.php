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
class IndexAction extends AdminCommAction {
//后台首页
	public function index(){
		$stat=$this->statistical();
		$this->assign('stat',$stat);
        $this->display();
	}
//--------系统设置-----------
     public function system(){
        $System = M('system');
        $list = $System->order('`id` asc')->select();
        $this->assign('list',$list);
        $this->display();
    }
	
	//系统参数添加
    public function addsy(){
            $system=D('System');
            if(!$system->create()){
                 $this->error($system->getError());
            }
            $last=$system->add();
            if($last){
				$this->Record('添加系统参数');//后台操作
				F('systems',NULL);
				$this->success('添加成功', '__APP__/TIFAWEB_DSWJCMS/Index/system');
            }else{
				$this->Record('系统参数添加失败');//后台操作
				$this->error('系统参数添加失败');
            }

    }
	
	//系统参数删除
    public function delesy(){
            $system=M('System');
            $id=$this->_get('id');
            $system->where('id="'.$id.'"')->delete();
			$this->Record('删除系统参数');//后台操作
			F('systems',NULL);
            $this->success('删除成功', '__APP__/TIFAWEB_DSWJCMS/Index/system');
    }

	//系统参数编辑页
    public function editsys(){
            $System = M('system');
            $id=$this->_get('id');
            $edlist = $System->where('id="'.$id.'"')->select();
    		$this->assign('edlist',$edlist);
            $this->display();
    }
	//系统参数值保存(单)
    public function savesys(){
            $system=D('System');
			if($create=$system->create()){	
				$system->where('id="'.$this->_post('id').'"')->save($create);
				F('systems',NULL);
				$this->Record('参数修改成功');//后台操作
				$this->success('参数修改成功', '__APP__/TIFAWEB_DSWJCMS/Index/system');
			}else{
				$this->error($system->getError());
			}            
    }
	
	//系统参数值保存(总)
    public function savesy(){
            $system=D('System');
            $value=$this->_post('value');
			if($system->create()){	
				foreach($this->_post('id') as $v=>$id){
						$data['id']			= $id;
						$data['value']		= $value[$v];
						$system->save($data);
				}
				F('systems',NULL);
				$this->Record('参数修改成功');//后台操作
				$this->success('参数修改成功', '__APP__/TIFAWEB_DSWJCMS/Index/system');
			}else{
				$this->error($system->getError());
			}            
    }
	//--------邮箱管理-----------
	public function email(){
        $email = M('smtp');
        $list = $email->select();
        $this->assign('vo',$list);
        $this->display();
    }
    //邮箱保存
    public function email_send(){
            $system=D('Smtp');
			if($system->create()){
				  $result = $system->save();
				 if($result){
					 $this->Record('SMTP修改成功');//后台操作
					 $this->success('修改成功', '__APP__/TIFAWEB_DSWJCMS/Index/email');
					
				 }else{
					 $this->Record('SMTP修改失败');//后台操作
					$this->error("修改失败");
				 }			 			
			}else{
				 $this->error($system->getError());
			}
    }
//--------管理操作记录-----------
     public function operation(){
		 if($this->_get('title')){
			$where.="`name` LIKE '%".$this->_get('title')."%'";
		}
		
		if($this->_get('starttime')>0){
			$starttime=strtotime($this->_get('starttime'));
			$starttime=" and `time`>='".$starttime."'";
		}
		if($this->_get('endtime')>0){
			$endtime=strtotime($this->_get('endtime'));
			$endtime=" and `time`<='".$endtime."'";
		}
		$where.=$starttime.$endtime;
		
		$where=trim($where,' and ');
        $Operation = M('operation');
        import('ORG.Util.Page');// 导入分页类
        $count      = $Operation->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $Operation->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('time DESC')->select();
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);
        $this->display();
    }

//--------会员操作记录-----------
     public function userrecord(){
		
        $Operation = D('User_log');
        import('ORG.Util.Page');// 导入分页类
        $count      = $Operation->count();// 查询满足要求的总记录数
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $Operation->relation(true)->limit($Page->firstRow.','.$Page->listRows)->order('time DESC')->select();
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);
        $this->display();
    }

//--------积分记录-----------
     public function integralrecord(){
        $Operation = D('Money_log');
        import('ORG.Util.Page');// 导入分页类
        $count      = $Operation->where('type>0')->count();// 查询满足要求的总记录数
        $Page       = new Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $Operation->relation(true)->where('type>0')->limit($Page->firstRow.','.$Page->listRows)->order('time DESC')->select();
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);
        $this->display();
    }
	
	//导出EXCEL(管理员操作记录)
	public function adminExport(){
		$operation = M('operation');
		$fid=$this->_post('fid');
		if($fid>0){
		$list=$operation->order('`time`  ASC ')->limit($fid.',5000')->select();
		}else{
		$list=$operation->order('`time`  ASC ')->limit(5000)->select();
		}
		$data['title']="管理员操作记录";
		$data['name']=array(
							array('n'=>'ID','u'=>'id'),
							array('n'=>'操作者','u'=>'name'),
							array('n'=>'操作页面','u'=>'page'),
							array('n'=>'操作类型','u'=>'type'),
							array('n'=>'操作IP','u'=>'ip'),
							array('n'=>'操作时间','u'=>'time')
							);
		foreach($list as $l){
			$content[]=array(
							'id'				=>' '.$l['id'],
							'name'				=>$l['name'],
							'page'				=>$l['page'],
							'type'				=>$l['type'],
							'ip'				=>' '.$l['ip'],
							'time'				=>date('Y-m-d H:i:s',$l['time'])
							);
		}
		$data['content']=$content;
		$excel=$this->excelExport($data);
		$this->Record('管理员操作记录导出成功');//后台操作
			$this->success("导出成功","__APP__/TIFAWEB_DSWJCMS/Index/operation.html");
		
	}
	
	//导出EXCEL(用户操作记录)
	public function userExport(){
		$operation = D('User_log');
		$fid=$this->_post('fid');
		if($fid>0){
		$list=$operation->relation(true)->order('`time`  ASC ')->limit($fid.',5000')->select();
		}else{
		$list=$operation->relation(true)->order('`time`  ASC ')->limit(5000)->select();
		}
		
		$data['title']="用户操作记录";
		$data['name']=array(
							array('n'=>'ID','u'=>'id'),
							array('n'=>'操作者','u'=>'username'),
							array('n'=>'操作说明','u'=>'actionname'),
							array('n'=>'操作时间','u'=>'time'),
							array('n'=>'操作IP','u'=>'ip')
							);
		foreach($list as $l){
			$content[]=array(
							'id'				=>' '.$l['id'],
							'username'			=>$l['username'],
							'actionname'		=>$l['actionname'],
							'time'				=>date('Y-m-d H:i:s',$l['time']),
							'ip'				=>' '.$l['ip']
							);
		}
		$data['content']=$content;
		$excel=$this->excelExport($data);
		$this->Record('用户操作记录导出成功');//后台操作
			$this->success("导出成功","__APP__/TIFAWEB_DSWJCMS/Index/userrecord.html");
		
	}
	
	//导出EXCEL(积分记录)
	public function integralExport(){
		$operation = D('Money_log');
		$fid=$this->_post('fid');
		if($fid>0){
		$list=$operation->relation(true)->where('type>0')->order('`time`  ASC ')->limit($fid.',5000')->select();
		}else{
		$list=$operation->relation(true)->where('type>0')->order('time`  ASC ')->limit(5000)->select();
		}
		$data['title']="积分记录";
		$data['name']=array(
							array('n'=>'ID','u'=>'id'),
							array('n'=>'操作者','u'=>'username'),
							array('n'=>'交易对方','u'=>'counterparty'),
							array('n'=>'操作类型','u'=>'type'),
							array('n'=>'操作说明','u'=>'actionname'),
							array('n'=>'总数','u'=>'total_money'),
							array('n'=>'可用','u'=>'available_funds'),
							array('n'=>'冻结','u'=>'freeze_funds'),
							array('n'=>'操作金额','u'=>'operation'),
							array('n'=>'操作时间','u'=>'time'),
							array('n'=>'操作IP','u'=>'ip')
							);
		foreach($list as $l){
			switch($l['type']){
				case 1:
				$type='会员积分';
				break;
				case 2:
				$type='VIP积分';
				break;
				case 3:
				$type='推广积分';
				break;
			}
			$content[]=array(
							'id'				=>' '.$l['id'],
							'username'			=>$l['username'],
							'counterparty'		=>$l['counterparty'],
							'type'				=>$type,
							'actionname'		=>$l['actionname'],
							'total_money'		=>$l['total_money'],
							'available_funds'	=>$l['available_funds'],
							'freeze_funds'		=>$l['freeze_funds'],
							'operation'			=>$l['operation'],
							'time'				=>date('Y-m-d H:i:s',$l['time']),
							'ip'				=>' '.$l['ip']
							);
		}
		$data['content']=$content;
		$excel=$this->excelExport($data);
		$this->Record('积分记录导出成功');//后台操作
			$this->success("导出成功","__APP__/TIFAWEB_DSWJCMS/Index/userrecord.html");
		
	}
	//--------界面风格-----------
    public function colour(){
		$directory = F('directory'); 	//模板
		$dirname = F('dirname');  // 默认模板
		if(!$directory){
			$directory=$this->templateData('./Tpl/Home/template/');
			F('directory',$directory);
			$directory=F('directory');	
		}
		if(!$dirname){
			$dir="Default";
			F('dirname',$dir);
			$dirname=F('dirname');
		}
		$this->assign('num',$directory['num']);
		unset($directory['num']);
		$this->assign('dirname',$dirname);
		$this->assign('list',$directory);
		$this->display();
	}
	
	//界面设为默认
	public function setDefault(){
		if($this->_post('dir')){
			F('dirname',NULL);
			F('dirname',$this->_post('dir'));
			$dirname=F('dirname');
			$directory = F('directory');
			$num=$directory['num'];
			unset($directory['num']);
			echo '
			<p>共<span class="red">'.$num.'</span>套模板</p>
			<ul class="thumbnails colour_switch">
			';
			foreach($directory as $dir){
				echo '
					<li class="span2">
						<a class="thumbnail" onclick="setDefault(\''.$dir[3].'\');">
						  <img src="/Tpl/Home/template/'.$dir[3].'/direct.png" style="width:152px;height:123px;">
						  <div class="title">
						  <h4>'.$dir[0].'</h4>
						  <p>'.$dir[1].'</p>
						  <p><span>作者:'.$dir[2].'</span></p>
						  </div>
						  <p><em>';
				echo $dir[3]==$dirname?"默认模板":"";
				echo "</em></p>
						</a>
					  </li>
				";
			}
			echo '
				</ul>
			</div>
			';
		}
	}
	
	//界面刷新
	public function colourRefresh(){
		if($this->_post('limit')==1){
			$dirname = F('dirname');
			F('directory',NULL);
			$directory=$this->templateData('./Tpl/Home/template/');
			F('directory',$directory);
			$directory = F('directory');
			$num=$directory['num'];
			unset($directory['num']);
			echo '
			<p>共<span class="red">'.$num.'</span>套模板</p>
			<ul class="thumbnails colour_switch">
			';
			foreach($directory as $dir){
				echo '
					<li class="span2">
						<a class="thumbnail" onclick="setDefault(\''.$dir[3].'\');">
						  <img src="/Tpl/Home/template/'.$dir[3].'/direct.png" style="width:152px;height:123px;">
						  <div class="title">
						  <h4>'.$dir[0].'</h4>
						  <p>'.$dir[1].'</p>
						  <p><span>作者:'.$dir[2].'</span></p>
						  </div>
						  <p><em>';
				echo $dir[3]==$dirname?"默认模板":"";
				echo "</em></p>
						</a>
					  </li>
				";
			}
			echo '
				</ul>
			</div>
			';
		}
	}	
	
	//环境切换
	public function contextSwitching(){
		$path='./index.php';
		if (file_exists($path)) {
			
			$fileget=file_get_contents($path);
			if($this->_post('id')==1){	//正式环境
				$replace="//define('APP_DEBUG";
				$update_str = preg_replace('/define\(\'APP_DEBUG/', $replace, $fileget); 
			}else{
				$replace="define('APP_DEBUG";
				$update_str = preg_replace('/\/\/define\(\'APP_DEBUG/', $replace, $fileget); 
			}
			file_put_contents($path, $update_str);
			$this->ajaxReturn(1,'切换成功',1);
		}else{
			$this->ajaxReturn(0,'无读写权限',0);
		}
		
	}
	//--------APP界面风格-----------
    public function wcolour(){
		$directory = F('wdirectory'); 	//模板
		$dirname = F('wdirname');  // 默认模板
		if(!$directory){
			$directory=$this->templateData('./Tpl/Win/template/');
			F('wdirectory',$directory);
			$directory=F('wdirectory');	
		}
		if(!$dirname){
			$dir="Default";
			F('wdirname',$dir);
			$dirname=F('wdirname');
		}
		$this->assign('num',$directory['num']);
		unset($directory['num']);
		$this->assign('dirname',$dirname);
		$this->assign('list',$directory);
		$this->display();
	}
	
	//APP界面设为默认
	public function wsetDefault(){
		if($this->_post('dir')){
			F('wdirname',NULL);
			F('wdirname',$this->_post('dir'));
			$dirname=F('wdirname');
			$directory = F('wdirectory');
			$num=$directory['num'];
			unset($directory['num']);
			echo '
			<p>共<span class="red">'.$num.'</span>套模板</p>
			<ul class="thumbnails colour_switch">
			';
			foreach($directory as $dir){
				echo '
					<li class="span2">
						<a class="thumbnail" onclick="setDefault(\''.$dir[3].'\');">
						  <img src="/Tpl/Win/template/'.$dir[3].'/direct.png" style="width:200px;height:250px;">
						  <div class="title">
						  <h4>'.$dir[0].'</h4>
						  <p>'.$dir[1].'</p>
						  <p><span>作者:'.$dir[2].'</span></p>
						  </div>
						  <p><em>';
				echo $dir[3]==$dirname?"默认模板":"";
				echo "</em></p>
						</a>
					  </li>
				";
			}
			echo '
				</ul>
			</div>
			';
		}
	}
	
	//APP界面刷新
	public function wcolourRefresh(){
		if($this->_post('limit')==1){
			$dirname = F('wdirname');
			F('wdirectory',NULL);
			$directory=$this->templateData('./Tpl/Win/template/');
			F('wdirectory',$directory);
			$directory = F('wdirectory');
			$num=$directory['num'];
			unset($directory['num']);
			echo '
			<p>共<span class="red">'.$num.'</span>套模板</p>
			<ul class="thumbnails colour_switch">
			';
			foreach($directory as $dir){
				echo '
					<li class="span2">
						<a class="thumbnail" onclick="setDefault(\''.$dir[3].'\');">
						  <img src="/Tpl/Win/template/'.$dir[3].'/direct.png" style="width:200px;height:250px;">
						  <div class="title">
						  <h4>'.$dir[0].'</h4>
						  <p>'.$dir[1].'</p>
						  <p><span>作者:'.$dir[2].'</span></p>
						  </div>
						  <p><em>';
				echo $dir[3]==$dirname?"默认模板":"";
				echo "</em></p>
						</a>
					  </li>
				";
			}
			echo '
				</ul>
			</div>
			';
		}
	}
}

?>