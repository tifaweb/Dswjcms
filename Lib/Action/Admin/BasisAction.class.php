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
class BasisAction extends AdminCommAction {
//--------线下银行-----------
    public function linebank(){
		$unite=M('unite');
		$list=$unite->field('name,value')->where('`state`=0 and `pid`=14')->order('`order` asc,`id` asc')->select();
		$audit=$this->offlineBank();
		$this->assign('list',$list);
		$this->assign('audit',$audit);
        $this->display();
    }
	
	//线下银行排序
	public function updtline(){
		$offline=D('Offline');
		$offline->create();
		$offline->where(array('id'=>$this->_post('id')))->save();
		$this->Record('线下银行排序修改成功');//后台操作
    }
	
	//线下银行更新
	public function editlin(){
		$unite=M('unite');
		$offline=M('offline');
		$list=$unite->field('name,value')->where('`state`=0 and `pid`=14')->order('`order` asc,`id` asc')->select();
		$edlist=reset($offline->where('`id`="'.$this->_get('id').'"')->select());
		$this->assign('list',$list);
		$this->assign('vo',$edlist);
        $this->display();
    }
	
	//线下银行删除
	public function delelin(){
		$offline=M('offline');
		$result = $offline->where(array('id'=>$this->_get('id')))->delete();
		if($result){
			$this->Record('线下银行删除成功');//后台操作
			 $this->success("删除成功","__APP__/TIFAWEB_DSWJCMS/Basis/linebank");
				
		}else{
			$this->Record('线下银行删除失败');//后台操作
			$this->error("删除失败");
		}			
    }
	
//--------线上银行-----------
    public function online(){
		$online=M('online');
		$list=$online->field('id,logo,name,introduce')->order('`order` asc,`id` asc')->select();
        $this->assign('list',$list);
		$this->display();
    }
	
	//线上银行编辑
	public function editonl(){
		$online=M('online');
		$list=$online->where('`id`="'.$this->_get('id').'"')->find();
        $this->assign('vo',$list);
		$this->display();
    }
	
	//线上银行删除
	public function deleonl(){
		$online=M('online');
		$result = $online->where(array('id'=>$this->_get('id')))->delete();
		if($result){
			$this->Record('线上银行删除成功');//后台操作
			 $this->success("删除成功","__APP__/TIFAWEB_DSWJCMS/Basis/online");
				
		}else{
			$this->Record('线上银行删除失败');//后台操作
			$this->error("删除失败");
		}			
    }

//--------积分等级-----------
    public function integrallevel(){
		$grade=M('membership_grade');
		$list=$grade->order('`id` asc')->select();
		$this->assign('list',$list);
        $this->display();
    }
	
	//积分等级更新
	public function editint(){
		$grade=M('membership_grade');
		$edlist=reset($grade->where('`id`="'.$this->_get('id').'"')->select());
		$this->assign('vo',$edlist);
        $this->display();
    }
	
	//积分等级删除
	public function deleint(){
		$grade=M('membership_grade');
		$edlist=reset($grade->where('`id`="'.$this->_get('id').'"')->select());
		unlink('./Public/uploadify/uploads/grade_img/'.$edlist['img']);	//删除图片
		$result = $grade->where(array('id'=>$this->_get('id')))->delete();
		if($result){
			$this->Record('积分等级删除成功');//后台操作
			 $this->success("删除成功","__APP__/TIFAWEB_DSWJCMS/Basis/integrallevel");
				
		}else{
			$this->Record('积分等级删除失败');//后台操作
			$this->error("删除失败");
		}			
    }
	
	
//--------数据库备份-----------
	public function backup(){
		import('ORG.Custom.backupsql');
		$db = new DBManage ( C('DB_HOST'),C('DB_USER'), C('DB_PWD'), C('DB_NAME'), 'utf8' );
		if($this->_post('pid')){
			$smtp=M('smtp');
			$stmpArr=$smtp->find();
			$backup=$db->backup();
			if($backup){
				if($this->_post('email')){
					$stmpArr['receipt_email']	=$this->_post('email');
					$stmpArr['title']			="数据库备份".time();
					$stmpArr['content']			='<div>
														备份时间:'.date('Y/m/d H:i:s').'
													</div>';
					$stmpArr['addattachment']	=$backup;
					$this->email_send($stmpArr);//发送邮件
					$this->Record('数据库备份成功');//后台操作
					//删除备份的数据表
					if(file_exists($backup)){	
						unlink($backup);	//删除它
					}
					$this->success("数据库备份成功","__APP__/TIFAWEB_DSWJCMS/Basis/backup");
					exit;
				}else{
					$this->error("请输入正确的邮箱地址");
				}
			}else{
				$this->Record('数据库备份失败');//后台操作
				$this->error("数据库备份失败");
			}	
		}
		$this->display();
    }
//--------导航页-----------
    public function shuffling(){
        $shuff = M('shuffling');
        $list = $shuff->select();
        $this->assign('list',$list);
        $this->display();
    }
	
    //添加轮播图片
    public function addsh(){
            $Shuff=D('Shuffling');
			if(!$Shuff->create()){
					$this->error($Shuff->getError());
			}
			$Shuff->time=time();
			$last=$Shuff->add();
			if($last){
					$this->Record('添加轮播图片成功');//后台操作
					$this->success('添加成功', '__APP__/TIFAWEB_DSWJCMS/Basis/shuffling');
			}else{
					$this->Record('添加轮播图片失败');//后台操作
					$this->error('商品添加失败');
			}
    }
	
    //排序修改
    public function savesh(){
            $Shuff=M('shuffling');
            $id=$this->_post('id');
			$order=$this->_post('order');
			$state=$this->_post('state');
			$data['id']			= $id;
			if(isset($order)){
			$data['order']		= $order;
			}else if(isset($state)){
			$data['state']		= $state;	
			}
			$Shuff->save($data);
    }
	
    //轮播图片编辑页
    public function editshu(){
            $Shuff = M('shuffling');
            $id=$this->_get('id');
            $edlist = $Shuff->where('id="'.$id.'"')->select();
            $this->assign('edlist',$edlist);
            $this->display();
    }

    //轮播图片编辑页保存
    public function editsh(){
            $Shuff=D('Shuffling');
			if(!$Shuff->create()){
					$this->error($Shuff->getError());
			}
            $Shuff->save();
			$this->Record('轮播图片修改成功');//后台操作
            $this->success('修改成功', '__APP__/TIFAWEB_DSWJCMS/Basis/shuffling');
    }

    //轮播图片删除
    public function delesh(){
            $Shuff=M('shuffling');
            $id=$this->_get('id');
            $Shuff->where('id="'.$id.'"')->delete();
			$this->Record('轮播图片删除成功');//后台操作
            $this->success('删除成功', '__APP__/TIFAWEB_DSWJCMS/Basis/shuffling');
    }
	
//--------友情链接-----------
    public function links(){
        $shuff = M('links');
        $list = $shuff->select();
        $this->assign('list',$list);
        $this->display();
    }
	
	//添加友情链接
    public function addli(){
            $Shuff=D('Links');
			if(!$Shuff->create()){
					$this->error($Shuff->getError());
			}
			$Shuff->time=time();
			$last=$Shuff->add();
			if($last){
				$this->Record('添加友情链接成功');//后台操作
					$this->success('添加成功', '__APP__/TIFAWEB_DSWJCMS/Basis/links');
			}else{
				$this->Record('添加友情链接失败');//后台操作
					$this->error('友情链接添加失败');
			}
    }
	
    //排序修改
    public function saveli(){
            $Shuff=M('links');
            $id=$this->_post('id');
			$order=$this->_post('order');
			$state=$this->_post('state');
			$data['id']			= $id;
			if(isset($order)){
			$data['order']		= $order;
			}else if(isset($state)){
			$data['state']		= $state;	
			}
			$Shuff->save($data);
    }
	
    //友情链接编辑页
    public function editlink(){
            $Shuff = M('links');
            $id=$this->_get('id');
            $edlist = $Shuff->where('id="'.$id.'"')->select();
            $this->assign('edlist',$edlist);
            $this->display();
    }

    //友情链接编辑页保存
    public function editli(){
            $Shuff=D('links');
			if(!$Shuff->create()){
					$this->error($Shuff->getError());
			}
            $Shuff->save();
			$this->Record('友情链接修改成功');//后台操作
            $this->success('修改成功', '__APP__/TIFAWEB_DSWJCMS/Basis/links');
    }

    //友情链接删除
    public function deleli(){
            $Shuff=M('links');
            $id=$this->_get('id');
            $Shuff->where('id="'.$id.'"')->delete();
			$this->Record('友情链接删除成功');//后台操作
            $this->success('删除成功', '__APP__/TIFAWEB_DSWJCMS/Basis/links');
    }
	
	//--------数据库优化-----------
	public function optimization(){
		if($this->_post('oid')){
			$models = new Model();
			$models->query('OPTIMIZE TABLE 
			`ds_admin` ,
			`ds_article` ,
			`ds_article_add` ,
			`ds_auth_group` ,
			`ds_auth_group_access` ,
			`ds_auth_rule` ,
			`ds_borrowing` ,
			`ds_borrow_log` ,
			`ds_certification` ,
			`ds_city` ,
			`ds_collection` ,
			`ds_coverdue` ,
			`ds_instation` ,
			`ds_links` ,
			`ds_membership_grade` ,
			`ds_money` ,
			`ds_money_log` ,
			`ds_offline` ,
			`ds_online` ,
			`ds_operation` ,
			`ds_overdue` ,
			`ds_recharge` ,
			`ds_refund` ,
			`ds_shuffling` ,
			`ds_site` ,
			`ds_site_add` ,
			`ds_smtp` ,
			`ds_system` ,
			`ds_unite` ,
			`ds_user` ,
			`ds_userinfo` ,
			`ds_user_log` ,
			`ds_withdrawal`
			');
			exit;
		}	 
		$this->display();
	}
	
	//--------担保公司-----------
    public function gcompany(){
		$Shuff=D('Guaranteecomp');
		$guaran=$Shuff->select();
		$this->assign('list',$guaran);
        $this->display();
    }
	
	//担保公司详细信息
    public function editgcompany(){
		if($this->_get('id')>0){
			$Shuff=D('Guaranteecomp');
			$guaran=$Shuff->where('id="'.$this->_get('id').'"')->find();
			$guaran['img']=explode(",",$guaran['img']);
			$this->assign('list',$guaran);
		}
        $this->display();
    }
	
	//添加担保公司
    public function addgcompany(){
		$Shuff=D('Guaranteecomp');
		if(!$create=$Shuff->create()){
				$this->error($Shuff->getError());
		}
		$create['time']=time();
		$create['img']=implode(",",$create['img']);
		$last=$Shuff->add($create);
		if($last){
			$this->Record('添加担保公司成功');//后台操作
				$this->success('添加成功', '__APP__/TIFAWEB_DSWJCMS/Basis/gcompany');
		}else{
			$this->Record('添加担保公司成功失败');//后台操作
				$this->error('担保公司添加失败');
		}
    }
	
	//添加担保公司
    public function edgcompany(){
		$Shuff=D('Guaranteecomp');
		if($this->_post('id')<1){
			$this->error('参数有误');
		}
		if(!$create=$Shuff->create()){
				$this->error($Shuff->getError());
		}
		$create['img']=implode(",",$create['img']);
		$last=$Shuff->where('id="'.$this->_post('id').'"')->save($create);
		if($last){
			$this->Record('更新担保公司成功');//后台操作
				$this->success('更新成功', '__APP__/TIFAWEB_DSWJCMS/Basis/gcompany');
		}else{
			$this->Record('更新担保公司失败');//后台操作
				$this->error('担保公司更新失败');
		}
    }
}
?>