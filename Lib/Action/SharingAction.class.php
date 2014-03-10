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
class SharingAction extends Action{
	/**
	 * @前台验证
     * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	public function homeVerify(){
		if($this->_session('user_uid')){
			$user=M('user');
			$users=$user->field('username,password')->where('id='.$this->_session('user_uid'))->find();
			if($this->_session('user_verify') !== MD5($users['username'].DS_ENTERPRISE.$users['password'].DS_EN_ENTERPRISE)){
				session('user_uid',null);
				session('user_name',null);
				session('user_verify',null);
				$this->error("请先重新登陆",'__ROOT__/Logo/login.html');
			}
		}else{
			$this->error("请先登陆",'__ROOT__/Logo/login.html');
		}
	 }

	/**
     * @后台操作记录
     * @type    记录类型
     * @id      是否开启
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
     */
    public function Record($type,$id=0){
        if($id==0){
            $Operation = M('operation');
            $array['name']= $_SESSION['admin_name'];
            $array['page']= $_SERVER['PHP_SELF'];
            $array['type']= $type;
            $array['ip']= get_client_ip();
            $array['time']= time();
            $Operation->add($array);
        }
    }
	
	/**
	 * @取前几条数据
	 * @m		传入的model
	 * @w		查询条件
	 * @o		排序
	 * @l		条数
	 * @r		是否关联查询
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	public function top($m,$w,$o,$l,$r=1) {
		$model=D($m);
		if($r==1){
			return $model->relation(true)->where($w)->order($o)->limit($l)->select();
		}else{
			return $model->where($w)->order($o)->limit($l)->select();
		}
		
	}

    /**
	 *
	 * @邮件发送
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	public function email_send($arr){
		import('ORG.Custom.PhpMailer');
		$mail = new PHPMailer(); 
		$smtp			=	$arr['smtp'];
		$validation		=	$arr['validation'];
		$send_email		=	$arr['send_email'];
		$password		=	$arr['password'];
		$addresser		=	$arr['addresser'];
		$receiver_email_array  =	array_filter(explode(',',$arr['receiver_email_array']));
		$receipt_email	=  	$arr['receipt_email'];
		$title			=	$arr['title'];
		$content		=	$arr['content'];
		$addattachment	=	$arr['addattachment'];
		$ishtml			=	$arr['ishtml'];
		$mail->IsSMTP(); // 使用SMTP方式发送
		$mail->CharSet='UTF-8';// 设置邮件的字符编码
		$mail->Host = "$smtp"; // 您的企业邮局域名
		$mail->SMTPAuth = $validation==1?true:false; // 启用SMTP验证功能
		$mail->Username = "$send_email"; // 邮局用户名(请填写完整的email地址)
		$mail->Password = "$password"; // 邮局密码
		$mail->From = "$send_email"; //邮件发送者email地址
		$mail->FromName = "$addresser";	//发件人
		if($receiver_email_array){	//群发
			foreach($receiver_email_array as $rea){
				$mail->AddAddress("$rea");
			}
		}else{
			$mail->AddAddress("$receipt_email");//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
		}
		//$mail->AddReplyTo("", "");	//添加回复
		if($addattachment){
			$mail->AddAttachment("$addattachment"); // 添加附件
		}
		$mail->IsHTML($ishtml==1?true:false); // set email format to HTML //是否使用HTML格式
		$mail->Subject = "$title"; //邮件标题
		$mail->Body = "$content"; //邮件内容
		$mail->AltBody = "点石为金借贷"; //附加信息，可以省略
		if(!$mail->Send())
		{
			echo '邮件发送失败. <p>错误原因: '. $mail->ErrorInfo;
			exit;
		}
		return true;
    }
		
   /**
	*
	* @系统配置
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function systems(){
		$sys = F('systems');  // 获取缓存
		if(!$sys){
			$system	=	M('system');
			$system=$system->select();
			foreach($system as $s){
				$sys[$s['name']]=$s['value'];
			}
			F('systems',$sys);	//设置缓存
		}
		return $sys;
	}
	
	/**
	*
	* @会员操作记录
	* @arr		记录说明
	* @uid		用户ID
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
    public function userLog($arr,$uid){
			$models = new Model();
            $array['uid']		= $uid?$uid:$this->_session('user_uid');
			$array['actionname']= $arr;
			$array['page']		= $_SERVER['PHP_SELF'];
            $array['ip']		= get_client_ip();
            $array['time']		= time();
			return $models->table('ds_user_log')->add($array);
    }

	/**
	*
	* @excel列转换
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function letter() {
		return $array=array(0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z');
	}
	
	/**
	*
	* @excel导出
	* @作者			天发网络科技
	* @版权			http://www.tifaweb.com
	* @$array		数据数组
	* @-moder			所采用的模板 默认为template
	* @-title			标题
	* @-name			小标题（数组）
	* @--n					字段名
	* @--u					字段英文名
	* @--t					字段类型
	* @-content			数据(数组)
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function excelExport($array) {
		Vendor ( 'Excel.PHPExcel' );
		$letter=$this->letter();//引入列换算
		$mode=$array['moder']?$array['moder']:'t1.xls';	//获取模板
		$mode='Public/excel/'.$mode;
		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ($mode);
		//获取当前活动的表
		$objActSheet = $objPHPExcel->getActiveSheet ();
		$objActSheet->setTitle ($array['title']);
		$baseRow = 2; //数据从N-1行开始往下输出  这里是避免头信息被覆盖
		//我现在就开始输出列头了
		foreach($array['name'] as $id=>$name){
			$objActSheet->setCellValue ($letter[$id].'1',$name['n']);
			foreach ( $array['content'] as $r => $dataRow ) {
				$row = $baseRow + $r;
				//将数据填充到相对应的位置
				$objPHPExcel->getActiveSheet ()->setCellValue ( $letter[$id] . $row,$dataRow [$name['u']]);
			}
		}
		//导出
		$filename = time ();
		
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
		header ( 'Cache-Control: max-age=0' );
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );
		return true;
	}
	
	/**
	 * @后台总数据统计
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	public function statistical(){
		$user=M('user');
		//总
		$array['metotal']=$user->count();	//会员总数
		//今天
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$where='time >='.$beginToday.' and time <='.$endToday;
		$array['menow']=$user->where($where)->count();	//会员数
		//本周
		$beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
		$endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));
		$where='time >='.$beginLastweek.' and time <='.$endLastweek;
		$array['meweeks']=$user->where($where)->count();	//会员数
		//本月
		$beginThismonth=mktime(0,0,0,date('m'),1,date('Y')); 
		$endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
		$where='time >='.$beginThismonth.' and time <='.$endThismonth;
		$array['memonth']=$user->where($where)->count();	//会员数	
		return $array;
	}
	
	/**
	 * @查看头像是否存在
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	public function headPortrait($img){
		if(file_exists($img)){	//存在图片
			return 1;
		}
	}
	
	/**
	 * @获取某个类目下的文章
	 * @id			//栏目ID
	 * @limt		//显示条数
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	public function someArticle($id,$limt){
		$mod = D("Article");
		$list = $mod->field('id,title,addtime')->where("published=1 and catid=".$id)->limit($limt)->order('`order` desc,`addtime` desc')->select();
		return $list;
	}
	
	/**
	*
	* @数据库自动备份
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function automaticBackup(){
		$system=$this->systems();
		import('ORG.Custom.backupsql');
		$db = new DBManage ( C('DB_HOST'),C('DB_USER'), C('DB_PWD'), C('DB_NAME'), 'utf8' );
		$smtp=M('smtp');
		$stmpArr=$smtp->find();
		$backup=$db->backup();
		if($backup){
			$stmpArr['receipt_email']	=$system['sys_autoemail'];
			$stmpArr['title']			="数据库备份".time();
			$stmpArr['content']			='<div>
												备份时间:'.date('Y/m/d H:i:s').'
											</div>';
			$stmpArr['addattachment']	=$backup;
			$this->email_send($stmpArr);//发送邮件
			//删除备份的数据表
			if(file_exists($backup)){	
				unlink($backup);	//删除它
			}
		}
	}
	/**
	*
	* @显示指定目录文件
	* @dirname	要遍历的目录名字	
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function directory($dirname){
	   $num=0;    //用来记录目录下的文件个数
	   $dir_handle=opendir($dirname);
	   while($file=readdir($dir_handle))
	   {
		 if($file!="."&&$file!="..")
		 {
			$dirFile=$dirname."/".$file;
			$num++;
			$array[]=$file;
		 }
	   }
	   closedir($dir_handle);
	   $array['num']=$num;
	   return $array;
	}
	
	
	/**
	*
	* @模板数据获取
	* @dirname	要遍历的目录名字
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function templateData($dirname){
		$template=$this->directory($dirname);
		$array['num']=$template['num'];
		unset($template['num']);
		foreach($template as $id=>$te){
			$fp = file_get_contents($dirname."/".$te."/state.tf",'r'); 
			$array[$id] = explode("\r\n",$fp);
			$array[$id][3]=$te;
			fclose($fp); //关闭文件 
		}
		return $array;
	}
	
	/**
	*
	* @删除指定文件
	* @path		路径
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function pathExit(){
		$path=$this->_post('img');
		if(file_exists($path)){	//存在图片
			unlink($path);	//删除它
		}
    }
}
?>