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
	 function homeVerify(){
		if(!$this->_session('user_uid')){
			$this->error("请先登陆",'__ROOT__/Logo/login.html');
		}
	 }
	 
	/**
	  * @返回值/错误信息
	  * @in		数组
	  *
	  */
	 function remote($in){
		if($in['value'] == 'NO'){
			$this->error($in['error'],$in['url']);
		}else if($in['value'] == 'accredit'){
			$this->error($in['error'],$in['url']);	
		}else{
			return $in['value'];
		}	
	 }
	 
	/**
	 * @根据id生成唯一订单号
	 * @当前时间戳+随机
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	public function orderNumber() {
		$curlPost = "dswjw=".$_SERVER['SERVER_NAME']."&dswjn=".DS_NUMbER;
		$url='http://www.tifaweb.com/Api/Core/orderNumberApi';  
		$in=$this->Curl($curlPost,$url);
		$remote=$this->remote($in);
		if(!$remote){
			$url='http://www.dswjcms.com/Api/Core/orderNumberApi';  
			$in=$this->Curl($curlPost,$url);
			$remote=$this->remote($in);
		}
		return $remote;
	}


	/**
	 * @(有待完善，当失败、签收状态对比、颜色变化等)
	 * @快递派送流程
	 * @name		快递公司(不支持中文)
	 * @number		快递单号
	 * @作者			shop猫
	 * @版权			宁波天发网络
	 * @官网			http://www.tifaweb.com http://www.dswjcms.com
	 * @快递公司查询：http://code.google.com/p/kuaidi-api/wiki/Open_API_API_URL
	 *
	 */
	function expressQuery($name,$number){
		$jsons=file_get_contents("http://www.kuaidi100.com/query?type=".$name."&postid=".$number."&id=1");
		$kuaidi=json_decode($jsons,true);	//json转数组
		sort($kuaidi['data']);	//排序
		$array['data']=$kuaidi['data'];
		$array['end']=end($kuaidi['data']);//取最新一条
		return $array;
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
	 *
	 * @城市
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */	
	public function city(){
		$citys = F('city');  // 获取缓存
		if(!$citys){
			$city	=	M('city');
			$city=$city->select();
			foreach($city as $cy){
				$citys[$cy['var']]=$cy['city'];
			}
			F('city',$citys);	//设置缓存
		}
		return $citys;
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
	* @积分配置
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function integralConf(){
		$system=M('integralconf');
		$system=$system->select();
		foreach($system as $s){
			$sys[$s['name']]=array($s['value'],$s['state']);
		}
		return $sys;
	}

    /**
	 * @积分添加
	 * @array	参数
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 *--------------使用说明-----------------
	 $add['member']=array(
						'uid'=>1,	//用户名
						'name'=>'mem_register',	//积分配置表中的积分名
					);
	 $this->integralAdd($add);//积分添加
	 *--------------------------------------
	 */
	public function integralAdd($array) {
		$Model = new Model();
		$inf=$this->integralConf();
		if(isset($array['member'])){	//会员积分
		
			if(isset($inf[$array['member']['name']])){	//判断用户提交上来的积分名是否存在积分配置表中
				$upda=$Model->execute("update ds_ufees set `total`=`total`+".$inf[$array['member']['name']][0].",`available` = `available`+".$inf[$array['member']['name']][0]." where uid='".$array['member']['uid']."'");//会员积分更新
				$ufee=$Model->table('ds_ufees')->where('uid='.$array['member']['uid'])->find();
				if($upda){
					
					$arr[0]=1;
					$arr[1]=$inf[$array['member']['name']][1];
					$arr[2]=$inf[$array['member']['name']][0];
					$arr[3]='平台';
					$arr[4]=$ufee['total'];
					$arr[5]=$ufee['available'];
					$arr[6]=$ufee['freeze'];
					$moneyLog=R('Comm/moneyLog',array($arr));
				}
			}
		}
		if(isset($array['vip'])){	//VIP
			if(isset($inf[$array['vip']['name']])){	//判断用户提交上来的积分名是否存在积分配置表中
				$upda=$Model->execute("update ds_vip_points set `total`=`total`+".$inf[$array['vip']['name']][0].",`available` = `available`+".$inf[$array['vip']['name']][0]." where uid='".$array['vip']['uid']."'");//VIP积分更新
				$ufee=$Model->table('ds_vip_points')->where('uid='.$array['vip']['uid'])->find();
				if($upda){
					$arr[0]=2;
					$arr[1]=$inf[$array['vip']['name']][1];
					$arr[2]=$inf[$array['vip']['name']][0];
					$arr[3]='平台';
					$arr[4]=$ufee['total'];
					$arr[5]=$ufee['available'];
					$arr[6]=$ufee['freeze'];
					$moneyLog=R('Comm/moneyLog',array($arr));
				}
			}
		}
		if(isset($array['promote'])){	//推广积分
			if(isset($inf[$array['promote']['name']])){	//判断用户提交上来的积分名是否存在积分配置表中
				$upda=$Model->execute("update ds_promote_integral set `total`=`total`+".$inf[$array['promote']['name']][0].",`available` = `available`+".$inf[$array['promote']['name']][0]." where uid='".$array['promote']['uid']."'");//会员积分更新
				$ufee=$Model->table('ds_promote_integral')->where('uid='.$array['promote']['uid'])->find();
				if($upda){
					$arr[0]=3;
					$arr[1]=$inf[$array['promote']['name']][1];
					$arr[2]=$inf[$array['promote']['name']][0];
					$arr[3]='平台';
					$arr[4]=$ufee['total'];
					$arr[5]=$ufee['available'];
					$arr[6]=$ufee['freeze'];
					$moneyLog=R('Comm/moneyLog',array($arr));
				}
			}
		}
		return 1;
	}
	
    /**
	 *
	 * @邮件发送
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	public function email_send($arr){
		$this->copyright();
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
		$this->copyright();
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
	* @用户详情
	* @uid		传入的用户ID
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function user_details($uid=0){
		$user	=	D('User');
		$unite	=	M('unite');
		$citys=$this->city();
		$unite=$unite->field('pid,name,value')->where('`state`=0')->order('`order` asc,`id` asc')->select();
		if($uid){
			$id=$uid;
		}else{
			$id=$this->_session('user_uid');
		}
		$list = $user->relation(true)->where('id='.$id)->select();
		$integral=$list[0]['member_total_score'];
		$membership_grade=M('membership_grade');
		$grade_list = $membership_grade->where($integral.'>=min and '.$integral.'<=max')->field('name,img')->select();
		foreach($unite as $ue){
			switch($ue['pid']){
				case 8:
				if($ue['value']==$list[0]['userinfo']['education']){
					$education=$ue['name'];
				}
				break;
				case 9:
				if($ue['value']==$list[0]['userinfo']['monthly_income']){
					$monthly_income=$ue['name'];
				}
				break;
				case 10:
				if($ue['value']==$list[0]['userinfo']['housing']){
					$housing=$ue['name'];
				}
				break;
				case 11:
				if($ue['value']==$list[0]['userinfo']['buy_cars']){
					$buy_cars=$ue['name'];
				}
				break;
				case 12:
				if($ue['value']==$list[0]['userinfo']['industry']){
					$industry=$ue['name'];
				}
				break;
				case 13:
				if($ue['value']==$list[0]['userinfo']['national']){
					$national=$ue['name'];
				}
				break;
			}
		}
		$native_place=explode(" ",$list[0]['userinfo']['native_place']);
		$location=explode(" ",$list[0]['userinfo']['location']);
		$native_place=$citys[$native_place[0]]." ".$citys[$native_place[1]]." ".$citys[$native_place[2]];
		$location=$citys[$location[0]]." ".$citys[$location[1]]." ".$citys[$location[2]];
		if($list[0]['userinfo']['marriage']==1){
			$marriage="未婚";
		}else if($list[0]['userinfo']['marriage']==2){
			$marriage="已婚";
		}else{
			$marriage="保密";
		}
		$list[0]['membership_grade_name']=$grade_list[0]['name'];
		$list[0]['membership_grade_img']=$grade_list[0]['img'];
		$list[0]['name']=$list[0]['userinfo']['name'];
		$list[0]['gender']=$list[0]['userinfo']['gender']?"女":"男";
		$list[0]['national']=$national;
		$list[0]['born']=$list[0]['userinfo']['born'];
		$list[0]['idcard']=$list[0]['userinfo']['idcard'];
		$list[0]['idcard_img']=array_splice(explode(",",$list[0]['userinfo']['idcard_img']),1);;
		$list[0]['native_place']=$native_place;
		$list[0]['location']=$location;
		$list[0]['marriage']=$marriage;
		$list[0]['education']=$education;
		$list[0]['monthly_income']=$monthly_income;
		$list[0]['housing']=$housing;
		$list[0]['buy_cars']=$buy_cars;
		$list[0]['industry']=$industry;
		$list[0]['qq']=$list[0]['userinfo']['qq'];
		$list[0]['company']=$list[0]['userinfo']['company'];
		$list[0]['assure']=$list[0]['userinfo']['assure'];
		$list[0]['assurestate']=$list[0]['userinfo']['assurestate'];
		$list[0]['fixed_line']=$list[0]['userinfo']['fixed_line'];
		$list[0]['cellphone']=$list[0]['userinfo']['cellphone'];
		$list[0]['wechat']=$list[0]['userinfo']['wechat'];
		$list[0]['certification']=$list[0]['userinfo']['certification'];
		$list[0]['email_audit']=$list[0]['userinfo']['email_audit'];
		$list[0]['cellphone_audit']=$list[0]['userinfo']['cellphone_audit'];
		$list[0]['video_audit']=$list[0]['userinfo']['video_audit'];
		$list[0]['site_audit']=$list[0]['userinfo']['site_audit'];
		$list[0]['wechat_audit']=$list[0]['userinfo']['wechat_audit'];
		unset($list[0]['userinfo']);
		return $list;
    }
	
   /**
	* @认证资料
	* @id	0全部1实名2视频3现场4手机
	* @q	不为0时显示认证信息
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function audit($id=0,$q=0){
		if($id==1){
			$where="`certification`=1";
		}else if($id==2){
			$where="`video_audit`=1";
		}else if($id==3){
			$where="`site_audit`=1";
		}else if($id==4){
			$where="`cellphone_audit`=1";
		}else{
			$where='';
		}
		if($q){
			$field=",certification,email_audit,cellphone_audit,video_audit,site_audit,wechat_audit";
		}else{
			$field='';
		}
		$unite	=	M('unite');
		$userinfo	=	D('Userinfo');
		$citys=$this->city();
		$unite=$unite->field('pid,name,value')->where('`state`=0 and `pid`=13')->order('`order` asc,`id` asc')->select();
		foreach($unite as $ue){
			$unites[$ue['value']]=$ue['name'];
		}
		$userinfo=$userinfo->field('id,uid,name,gender,national,born,idcard,idcard_img,cellphone,native_place'.$field)->relation(true)->where($where)->order('`id` DESC')->select();
		
		foreach($userinfo as $id=>$ufo){
			$idcard_img=array_splice(explode(",",$ufo['idcard_img']),1);
			$native_place=explode(" ",$ufo['native_place']);
			$native_place=$citys[$native_place[0]]." ".$citys[$native_place[1]]." ".$citys[$native_place[2]];
			$userinfo[$id]['native_place']=$native_place;
			$userinfo[$id]['idcard_img']=$idcard_img;
			$userinfo[$id]['national']=$unites[$ufo['national']];
			$userinfo[$id]['gender']=$ufo['gender']?"女":"男";
			$userinfo[$id]['cellphone']=$ufo['cellphone'];
			unset($userinfo[$id]['join_date']);
		}	
		return $userinfo;
    }
   
   /**
	* @用户信息表
	* @uid		用户id
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function userinfo($uid=0,$conditions=0){
		$userinfo=D('Userinfo');
		if($uid){
			if($conditions){
				$userinfos = reset($userinfo->relation(true)->field($conditions)->where("`uid`=".$uid)->select());
			}else{
				$userinfos = reset($userinfo->relation(true)->where("`uid`=".$uid)->select());
			}
		}
		
		return $userinfos;
	}
	
   /**
	*
	* @线下银行
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function offlineBank(){
		$unite=M('unite');
		$offline=M('offline');
		$list=$unite->field('name,value')->where('`state`=0 and `pid`=14')->order('`order` asc,`id` asc')->select();
		$audit=$offline->order('`id` DESC')->select();
		foreach($list as $lt){
			$userinfos[$lt['value']]=$lt['name'];
		}
		foreach($audit as $id=>$au){
			$audit[$id]['type_name']=$userinfos[$au['type']];
		}
		return $audit;
	}	
	
   /**
	* @提现手续费
	* @m	提现金额
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function withdrawalPoundage($m=0){
		$systems=$this->systems();
		if($m>0){
			if($m<=$systems['sys_wFPoundage']){	//小于免费提现额度
				$wfp=0;
			}else{	//提现手续费=（提现金额-免费额度）*提现手续费率
				$wfp=round(($m-$systems['sys_wFPoundage'])*$systems['sys_withdrawalPoundage'],2);
			}
		}
		return $wfp;
	}
	
   /**	
	* @提现用户详细
	* @id		查询id
	* @uid		用户id
	* @where	条件
	*
	*/
	public function showUser($id=0,$uid=0,$where){
		$withdrawal=D('Withdrawal');
		$unite=M('unite');
		
		$list=$unite->field('name,value')->where('`state`=0 and `pid`=14')->order('`order` asc,`id` asc')->select();
		foreach($list as $lt){
			$unites[$lt['value']]=$lt['name'];
		}
		unset($list);
		if($id){	//单记录
			$withdrawals=reset($withdrawal->where('id='.$id)->order('`time` DESC,`id` DESC')->select());
			$userinfo=$this->userinfo($withdrawals['uid'],'uid,name,bank,bank_name,bank_account');
			$withdrawals['name']				=	$userinfo['name'];
			$withdrawals['username']			=	$userinfo['username'];
			$withdrawals['bank']				=	$unites[$userinfo['bank']];
			$withdrawals['bank_name']			=	$userinfo['bank_name'];
			$withdrawals['bank_account']		=	$userinfo['bank_account'];
			$withdrawals['withdrawal_poundage']	=	$this->withdrawalPoundage($withdrawals['money']);
			$withdrawals['account']				=	$withdrawals['money']-$this->withdrawalPoundage($ws['money']);
		}else{
			if($uid>0){	//单个用户
				$withdrawals=$withdrawal->where('uid='.$uid)->order('`time` DESC,`id` DESC')->select();
				foreach($withdrawals as $id=>$ws){
					$userinfo=$this->userinfo($ws['uid'],'uid,name,bank,bank_name,bank_account');
					$withdrawals[$id]['name']					=	$userinfo['name'];
					$withdrawals[$id]['username']				=	$userinfo['username'];
					$withdrawals[$id]['bank']					=	$unites[$userinfo['bank']];
					$withdrawals[$id]['bank_name']				=	$userinfo['bank_name'];
					$withdrawals[$id]['bank_account']			=	$userinfo['bank_account'];
					$withdrawals[$id]['withdrawal_poundage']	=	$this->withdrawalPoundage($ws['money']);
					$withdrawals[$id]['account']				=	$ws['money']-$this->withdrawalPoundage($ws['money']);
				}
			}else{	//所有用户信息
				$withdrawals=$withdrawal->where($where)->order('`time` DESC,`id` DESC')->select();
				foreach($withdrawals as $id=>$ws){
					$userinfo=$this->userinfo($ws['uid'],'uid,name,bank,bank_name,bank_account');
					$withdrawals[$id]['name']					=	$userinfo['name'];
					$withdrawals[$id]['username']				=	$userinfo['username'];
					$withdrawals[$id]['bank']					=	$unites[$userinfo['bank']];
					$withdrawals[$id]['bank_name']				=	$userinfo['bank_name'];
					$withdrawals[$id]['bank_account']			=	$userinfo['bank_account'];
					$withdrawals[$id]['withdrawal_poundage']	=	$this->withdrawalPoundage($ws['money']);
					$withdrawals[$id]['account']				=	$ws['money']-$this->withdrawalPoundage($ws['money']);
				}
			}
		}
		return $withdrawals;
    }
	
   /**
	* @充值用户详细
	* @id		查询id
	* @uid		用户id
	* @where	条件
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com	
	*
	*/
	public function rechargeUser($id=0,$uid=0,$where){
		$recharge=D('Recharge');
		$unite=M('unite');
		$list=$unite->field('pid,name,value')->where('(`pid` = 14 or `pid` = 15 ) and `state`=0')->order('`order` asc,`id` asc')->select();
		foreach($list as $lt){
			if($lt['pid']==15){
				$online[$lt['value']]=$lt['name'];	//网上
			}else{
				$unites[$lt['value']]=$lt['name'];	//线下
			}
		}
		unset($list);
		$offline=$this->offlineBank();
		foreach($offline as $of){
			$offlin[$of['id']]=$of;
		}
		if($id){	//单记录
			$recharges=reset($recharge->relation(true)->where('id='.$id)->order('`time` DESC,`id` DESC')->select());
			$recharges['genre_name']			=	$online[$recharges['genre']];
			$recharges['oid_array']				=	$offlin[$recharges['oid']];
		}else{
			if($uid>0){	//单个用户
				$recharges=$recharge->relation(true)->where('uid='.$uid)->order('`time` DESC,`id` DESC')->select();
				foreach($recharges as $id=>$ws){
					$recharges[$id]['genre_name']			=	$online[$ws['genre']];
				}
			}else{	//所有用户信息
				$recharges=$recharge->relation(true)->where($where)->order('`time` DESC,`id` DESC')->select();
				foreach($recharges as $id=>$ws){
					$recharges[$id]['genre_name']			=	$online[$ws['genre']];
					$recharges[$id]['oid_name']				=	$offlin[$ws['oid']]['bank'];
				}
			}
		}
		return $recharges;
    }
   /**
    * @充值手续费
	* @m	充值金额
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function topUpFees($m=0){
		$systems=$this->systems();
		if($m>0){
			if($systems['sys_topUFC']==0){	//大于免费额度收取手续费
				if($m<=$systems['sys_rechargeFA']){	//小于免费提现额度
					$wfp=0;
				}else{	//充值手续费=（充值金额-免费额度）*充值手续费
					$wfp=round(($m-$systems['sys_rechargeFA'])*$systems['sys_topUpFees'],2);
				}
			}else if($systems['sys_topUFC']==1){	//小于免费额度收取手续费
				if($m<=$systems['sys_rechargeFA']){	//小于免费提现额度
					//充值手续费=（充值金额-免费额度）*充值手续费率
					$wfp=round(($m-$systems['sys_rechargeFA'])*$systems['sys_topUpFees'],2);;
				}else{
					$wfp=0;
				}
			}
		}
		return $wfp;
	}

   /**
	*
	* @联动（发标所需联动）
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function borrowLinkage(){
		$unite=M('unite');
		$list=$unite->where('`state`=0 and `pid`<8')->order('`order` asc,`id` asc')->select();
		foreach($list as $lt){
			switch($lt['pid']){
				case 1:
				$linkage['use'][]=$lt;
				break;
				case 2:
				$linkage['deadline_month'][]=$lt;
				break;
				case 3:
				$linkage['deadline_day'][]=$lt;
				break;
				case 4:
				$linkage['way'][]=$lt;
				break;
				case 5:
				$linkage['valid'][]=$lt;
				break;
				case 6:
				$linkage['min'][]=$lt;
				break;
				case 7:
				$linkage['max'][]=$lt;
				break;
			}
		}
		return $linkage;
	}
	/**
	 * @借款单条
	 * @id		传入的的借款ID
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 *
	 */
	public function borrow_information($id){
		$borrow=$this->borrow_unicom($id);
		$user=$this->user_details($borrow[0]['uid']);
		$bid_records=$this->bidRecords('4',$id);
		$bid_records_count=count($bid_records);	//投标记录
		$assure_records=$this->bidRecords('14',$id);
		$assure_records_count=count($assure_records);	//担保记录
		$flow_records=$this->bidRecords('16',$id);//流转记录
		foreach($user as $ur){
			$borrow[0]['email']=$ur['email'];
			$borrow[0]['total_money']=$ur['total_money'];
			$borrow[0]['available_funds']=$ur['available_funds'];
			$borrow[0]['freeze_funds']=$ur['freeze_funds'];
			$borrow[0]['promote_tota']=$ur['promote_tota'];
			$borrow[0]['promote_available_integral']=$ur['promote_available_integral'];
			$borrow[0]['promote_freezing_points']=$ur['promote_freezing_points'];
			$borrow[0]['vip_total_score']=$ur['vip_total_score'];
			$borrow[0]['vip_available_integral']=$ur['vip_available_integral'];
			$borrow[0]['vip_freezing_points']=$ur['vip_freezing_points'];
			$borrow[0]['vip_audit']=$ur['vip_audit'];
			$borrow[0]['vip_checktime']=$ur['vip_checktime'];
			$borrow[0]['vip_deadline']=$ur['vip_deadline'];
			$borrow[0]['vip_unit']=$ur['vip_unit'];
			$borrow[0]['vip_opening_time']=$ur['vip_opening_time'];
			$borrow[0]['vip_expiration_time']=$ur['vip_expiration_time'];
			$borrow[0]['member_total_score']=$ur['member_total_score'];
			$borrow[0]['membership_grade_name']=$ur['membership_grade_name'];
			$borrow[0]['membership_grade_img']=$ur['membership_grade_img'];
			$borrow[0]['name']=$ur['name'];
			$borrow[0]['gender']=$ur['gender'];
			$borrow[0]['qq']=$ur['qq'];
			$borrow[0]['fixed_line']=$ur['fixed_line'];
			$borrow[0]['cellphone']=$ur['cellphone'];
			$borrow[0]['national']=$ur['national'];
			$borrow[0]['born']=$ur['born'];
			$borrow[0]['idcard']=$ur['idcard'];
			$borrow[0]['idcard_img']=$ur['idcard_img'];
			$borrow[0]['native_place']=$ur['native_place'];
			$borrow[0]['location']=$ur['location'];
			$borrow[0]['marriage']=$ur['marriage']?$ur['marriage']:"保密";
			$borrow[0]['education']=$ur['education']?$ur['education']:"保密";
			$borrow[0]['monthly_income']=$ur['monthly_income']?$ur['monthly_income']:"保密";
			$borrow[0]['housing']=$ur['housing']?$ur['housing']:"保密";
			$borrow[0]['buy_cars']=$ur['buy_cars']?$ur['buy_cars']:"保密";
			$borrow[0]['industry']=$ur['industry']?$ur['industry']:"保密";
			$borrow[0]['company']=$ur['company'];
			$borrow[0]['assures']=$ur['assure'];
			$borrow[0]['certification']=$ur['certification'];
			$borrow[0]['email_audit']=$ur['email_audit'];
			$borrow[0]['cellphone_audit']=$ur['cellphone_audit'];
			$borrow[0]['video_audit']=$ur['video_audit'];
			$borrow[0]['site_audit']=$ur['site_audit'];
			$borrow[0]['wechat_audit']=$ur['wechat_audit'];
			$borrow[0]['bid_records_count']=$bid_records_count;
			$borrow[0]['assure_records_count']=$assure_records_count;
			$borrow[0]['bid_records']=$bid_records;
			$borrow[0]['assure_records']=$assure_records;
			$borrow[0]['flow_records']=$flow_records;
			

			$borrow[0]['due_in']=$ur['due_in'];
			$borrow[0]['stay_still']=$ur['stay_still'];
		}
		$borrow[0]['ratio']=sprintf("%01.2f",($borrow[0]['money']-$borrow[0]['surplus'])/$borrow[0]['money']*100);	//进度
		$borrow[0]['ratios']=sprintf("%01.2f",($borrow[0]['money']-$borrow[0]['assure'])/$borrow[0]['money']*100);	//担保进度
		$flow_total=floor($borrow[0]['money']/$borrow[0]['min']);	//流转总份数
		$borrow[0]['subscribe']=$flow_total-$borrow[0]['flows'];	//可认购数
		$borrow[0]['flow_ratio']=sprintf("%01.2f",$borrow[0]['flows']/$flow_total*100);	//流转标进度
		unset($bid_records);
		unset($assure_records);
		unset($user);
		return $borrow;
	}

   /**
	* @借款信息
	* @id		单条借款传入ID
	* @where	条件
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function borrow_unicom($id=0,$where){
		$unite=M('unite');
		$list=$unite->where('`state`=0 and `pid`<8')->order('`order` asc,`id` asc')->select();
		foreach($list as $lt){
			switch($lt['pid']){
				case 1:
				$use[$lt['value']]=$lt['name'];
				break;
				case 2:
				$deadline_month[$lt['value']]=$lt['name'];
				break;
				case 3:
				$deadline_day[$lt['value']]=$lt['name'];
				break;
				case 4:
				$way[$lt['value']]=$lt['name'];
				break;
				case 5:
				$valid[$lt['value']]=$lt['name'];
				break;
				case 6:
				$min[$lt['value']]=$lt['name'];
				break;
				case 7:
				$max[$lt['value']]=$lt['name'];
				break;
			}
		}
		$Borrowing = D('Borrowing');
		if($id>0){
			$borrow = $Borrowing->relation(true)->where('id='.$id)->order('`id` DESC')->select();
		}else{
			if(isset($where)){
				$borrow = $Borrowing->relation(true)->where($where)->order('`id` DESC')->select();
			}else{
				$borrow = $Borrowing->relation(true)->order('`id` DESC')->select();
			}
		}
		foreach($borrow as $id=>$lt){
			$borrow[$id]['use']=$use[$lt['use']];
			$borrow[$id]['deadlines']=$borrow[$id]['deadline'];
			if($lt['candra']==0){
				$borrow[$id]['deadlines']=$deadline_month[$lt['deadline']];
			}else{
				$borrow[$id]['deadlines']=$deadline_day[$lt['deadline']];
			}
			if($lt['candra']==0){
				$borrow[$id]['flow_deadlines']=$deadline_month[$lt['flow_deadline']];
			}else{
				$borrow[$id]['flow_deadlines']=$deadline_day[$lt['flow_deadline']];
			}
			if($lt['candra']==0){
				$borrow[$id]['min_limits']=$deadline_month[$lt['min_limit']];
			}else{
				$borrow[$id]['min_limits']=$deadline_day[$lt['min_limit']];
			}
			if($lt['reward_type']==0){
				$borrow[$id]['reward']='无';
			}else if($lt['reward_type']==1){
				$borrow[$id]['rewards']=$lt['reward']."元";
			}else{
				$borrow[$id]['rewards']=$lt['reward']."%";
			}
			$img=array_splice(explode(",",$lt['data']),1);
			$borrow[$id]['way']=$way[$lt['way']];
			$borrow[$id]['valid']=$valid[$lt['valid']];
			$borrow[$id]['min_name']=$min[$lt['min']];
			$borrow[$id]['max_name']=$max[$lt['max']]?$max[$lt['max']]:"无限制";
			$borrow[$id]['img']=$img[0];
			$borrow[$id]['already']=($lt['money']-$lt['surplus'])?($lt['money']-$lt['surplus']):'0.00';
			$borrow[$id]['alreadys']=($lt['money']-$lt['assure'])?($lt['money']-$lt['assure']):'0.00';
			$borrow[$id]['ratio']=sprintf("%01.2f",($lt['money']-$lt['surplus'])/$lt['money']*100);
			$borrow[$id]['ratios']=sprintf("%01.2f",($lt['money']-$lt['assure'])/$lt['money']*100);
			switch($lt['type']){
					case 0:
					$borrow[$id]['type_name']="秒还标";
					break;
					case 1:
					$borrow[$id]['type_name']="抵押标";
					break;
					case 2:
					$borrow[$id]['type_name']="质押标";
					break;
					case 3:
					$borrow[$id]['type_name']="净值标";
					break;
					case 4:
					$borrow[$id]['type_name']="信用标";
					break;
					case 5:
					$borrow[$id]['type_name']="担保标";
					break;
					case 6:
					$borrow[$id]['type_name']="团购标";
					break;
					case 7:
					$borrow[$id]['type_name']="流转标";
					break;
				}
				switch($lt['state']){
					case 0:
					$borrow[$id]['state_name']="待审核";
					break;
					case 1:
					$borrow[$id]['state_name']="审核通过";
					$borrow[$id]['state_names']="立即投标";
					break;
					case 2:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="审核失败";
					break;
					case 3:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="用户取消";
					break;
					case 4:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="流标";
					break;
					case 5:
					$borrow[$id]['state_name']="满标待审核";
					$borrow[$id]['state_names']="等待复审";
					break;
					case 6:
					$borrow[$id]['state_name']="满标审核失败";
					$borrow[$id]['state_names']="复审失败";
					break;
					case 7:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="还款中";
					break;
					case 8:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="逾期中";
					break;
					case 9:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="已完成";
					break;
					case 10:
					$borrow[$id]['state_name']="等待担保";
					$borrow[$id]['state_names']="立即担保";
					break;
					case 11:
					$borrow[$id]['state_name']="等待回购";
					$borrow[$id]['state_names']="回购中";
					break;
				}
		}
		return $borrow;
	}
	
   /**
	* @单用户借款信息
	* @uid		传入用户ID
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function borrowUidUnicom($uid){
		$unite=M('unite');
		$list=$unite->where('`state`=0 and `pid`<8')->order('`order` asc,`id` asc')->select();
		foreach($list as $lt){
			switch($lt['pid']){
				case 1:
				$use[$lt['value']]=$lt['name'];
				break;
				case 2:
				$deadline_month[$lt['value']]=$lt['name'];
				break;
				case 3:
				$deadline_day[$lt['value']]=$lt['name'];
				break;
				case 4:
				$way[$lt['value']]=$lt['name'];
				break;
				case 5:
				$valid[$lt['value']]=$lt['name'];
				break;
				case 6:
				$min[$lt['value']]=$lt['name'];
				break;
				case 7:
				$max[$lt['value']]=$lt['name'];
				break;
			}
		}
		$Borrowing = D('Borrowing');
		if($uid>0){
			$borrow = $Borrowing->relation(true)->where('uid='.$uid)->select();
		}else{
			if(isset($state)){
				$borrow = $Borrowing->relation(true)->where('state='.$state)->select();
			}else{
				$borrow = $Borrowing->relation(true)->select();
			}
		}
		foreach($borrow as $id=>$lt){
			$borrow[$id]['use']=$use[$lt['use']];
			$borrow[$id]['deadlines']=$borrow[$id]['deadline'];
			if($lt['candra']==0){
				$borrow[$id]['deadline']=$deadline_month[$lt['deadline']];
			}else{
				$borrow[$id]['deadline']=$deadline_day[$lt['deadline']];
			}
			
			if($lt['reward_type']==0){
				$borrow[$id]['reward']=无;
			}else if($lt['reward_type']==1){
				$borrow[$id]['reward']=$lt['reward']."元";
			}else{
				$borrow[$id]['reward']=$lt['reward']."%";
			}
			$img=array_splice(explode(",",$lt['data']),1);
			$borrow[$id]['way']=$way[$lt['way']];
			$borrow[$id]['valid']=$valid[$lt['valid']];
			$borrow[$id]['min_name']=$min[$lt['min']];
			$borrow[$id]['max_name']=$max[$lt['max']]?$max[$lt['max']]:"无限制";
			$borrow[$id]['img']=$img[0];
			$borrow[$id]['already']=($lt['money']-$lt['surplus'])?($lt['money']-$lt['surplus']):'0.00';
			$borrow[$id]['alreadys']=($lt['money']-$lt['assure'])?($lt['money']-$lt['assure']):'0.00';
			$borrow[$id]['ratio']=sprintf("%01.2f",($borrow[$id]['money']-$borrow[$id]['surplus'])/$borrow[$id]['money']*100);	//标进度
			$borrow[$id]['ratios']=sprintf("%01.2f",($borrow[$id]['money']-$borrow[$id]['assure'])/$borrow[$id]['money']*100);	//担保进度
			$flow_total=floor($borrow[$id]['money']/$borrow[$id]['min']);	//流转总份数
			$borrow[$id]['subscribe']=$flow_total-$borrow[$id]['flows'];	//可认购数
			$borrow[$id]['flow_ratio']=sprintf("%01.2f",$borrow[$id]['flows']/$flow_total*100);	//流转标进度
			switch($lt['type']){
					case 0:
					$borrow[$id]['type_name']="秒还标";
					break;
					case 1:
					$borrow[$id]['type_name']="抵押标";
					break;
					case 2:
					$borrow[$id]['type_name']="质押标";
					break;
					case 3:
					$borrow[$id]['type_name']="净值标";
					break;
					case 4:
					$borrow[$id]['type_name']="信用标";
					break;
					case 5:
					$borrow[$id]['type_name']="担保标";
					break;
					case 6:
					$borrow[$id]['type_name']="团购标";
					break;
					case 7:
					$borrow[$id]['type_name']="流转标";
					break;
				}
				switch($lt['state']){
					case 0:
					$borrow[$id]['state_name']="待审核";
					break;
					case 1:
					$borrow[$id]['state_name']="审核通过";
					$borrow[$id]['state_names']="立即投标";
					break;
					case 2:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="审核失败";
					break;
					case 3:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="用户取消";
					break;
					case 4:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="流标";
					break;
					case 5:
					$borrow[$id]['state_name']="满标待审核";
					$borrow[$id]['state_names']="等待复审";
					break;
					case 6:
					$borrow[$id]['state_name']="满标审核失败";
					$borrow[$id]['state_names']="复审失败";
					break;
					case 7:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="还款中";
					break;
					case 8:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="逾期中";
					break;
					case 9:
					$borrow[$id]['state_names']=$borrow[$id]['state_name']="已完成";
					break;
					case 10:
					$borrow[$id]['state_name']="等待担保";
					$borrow[$id]['state_names']="立即担保";
					break;
					case 11:
					$borrow[$id]['state_name']="等待回购";
					$borrow[$id]['state_names']="回购中";
					break;
				}
		}
		return $borrow;
	}
	/**
	 * @借款信息(详细)	
	 * @where	条件
	 * @limit	LIMIT 
	 * @order	排序
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 *
	 */
	public function borrow_unicoms($where,$limit,$order){
		if(!isset($where)){
			$where="`state`>0";
		}
		$unite=M('unite');
		$list=$unite->where('`state`=0 and `pid`<8')->order('`order` asc,`id` asc')->select();
		foreach($list as $lt){
			switch($lt['pid']){
				case 1:
				$use[$lt['value']]=$lt['name'];
				break;
				case 2:
				$deadline_month[$lt['value']]=$lt['name'];
				break;
				case 3:
				$deadline_day[$lt['value']]=$lt['name'];
				break;
				case 4:
				$way[$lt['value']]=$lt['name'];
				break;
				case 5:
				$valid[$lt['value']]=$lt['name'];
				break;
				case 6:
				$min[$lt['value']]=$lt['name'];
				break;
				case 7:
				$max[$lt['value']]=$lt['name'];
				break;
			}
		}
		$Borrowing = D('Borrowing');
		$borrow = $Borrowing->relation(true)->where($where)->order($order)->limit($limit)->select();
		if($borrow){
		foreach($borrow as $id=>$lt){
			$bid_records=$this->bidRecords('4',$lt['id']);
			$bid_records_count=count($bid_records);	//投标记录
			$assure_records=$this->bidRecords('14',$lt['id']);
			$assure_records_count=count($assure_records);	//担保记录
			unset($bid_records);
			unset($assure_records);
			$borrow[$id]['use']=$use[$lt['use']];
			if($lt['candra']==0){
				$borrow[$id]['deadlines']=$deadline_month[$lt['deadline']];
			}else{
				$borrow[$id]['deadlines']=$deadline_day[$lt['deadline']];
			}
			if($lt['candra']==0){
				$borrow[$id]['flow_deadlines']=$deadline_month[$lt['flow_deadline']];
			}else{
				$borrow[$id]['flow_deadlines']=$deadline_day[$lt['flow_deadline']];
			}
			if($lt['candra']==0){
				$borrow[$id]['min_limits']=$deadline_month[$lt['min_limit']];
			}else{
				$borrow[$id]['min_limits']=$deadline_day[$lt['min_limit']];
			}
			if($lt['reward_type']==0){
				$borrow[$id]['reward']=无;
			}else if($lt['reward_type']==1){
				$borrow[$id]['reward']=$lt['reward']."元";
			}else{
				$borrow[$id]['reward']=$lt['reward']."%";
			}
			$img=array_splice(explode(",",$lt['data']),1);
			$borrow[$id]['way']=$way[$lt['way']];
			$borrow[$id]['valid']=$valid[$lt['valid']];
			$borrow[$id]['min']=$min[$lt['min']];
			$borrow[$id]['max']=$max[$lt['max']]?$max[$lt['max']]:"无限制";
			$borrow[$id]['img']=$img[0];
			$borrow[$id]['bid_records_count']=$bid_records_count;
			$borrow[$id]['assure_records_count']=$assure_records_count;
			$borrow[$id]['ratio']=sprintf("%01.2f",($borrow[$id]['money']-$borrow[$id]['surplus'])/$borrow[$id]['money']*100);	//进度
			$borrow[$id]['ratios']=sprintf("%01.2f",($borrow[$id]['money']-$borrow[$id]['assure'])/$borrow[$id]['money']*100);	//担保进度
			$flow_total=floor($borrow[$id]['money']/$borrow[$id]['min']);	//流转总份数
			$borrow[$id]['subscribe']=$flow_total-$borrow[$id]['flows'];	//可认购数
			$borrow[$id]['flow_ratio']=sprintf("%01.2f",$borrow[$id]['flows']/$flow_total*100);	//流转标进度
			switch($lt[type]){
					case 0:
					$borrow[$id]['type_name']="秒还标";
					break;
					case 1:
					$borrow[$id]['type_name']="抵押标";
					break;
					case 2:
					$borrow[$id]['type_name']="质押标";
					break;
					case 3:
					$borrow[$id]['type_name']="净值标";
					break;
					case 4:
					$borrow[$id]['type_name']="信用标";
					break;
					case 5:
					$borrow[$id]['type_name']="担保标";
					break;
					case 6:
					$borrow[$id]['type_name']="团购标";
					break;
					case 7:
					$borrow[$id]['type_name']="流转标";
					break;
			}
			switch($lt[state]){
					case 1:
					$borrow[$id]['state_name']="立即投标";
					break;
					case 2:
					$borrow[$id]['state_name']="审核失败";
					break;
					case 3:
					$borrow[$id]['state_name']="用户取消";
					break;
					case 4:
					$borrow[$id]['state_name']="流标";
					break;
					case 5:
					$borrow[$id]['state_name']="等待复审";
					break;
					case 6:
					$borrow[$id]['state_name']="复审失败";
					break;
					case 7:
					$borrow[$id]['state_name']="还款中";
					break;
					case 8:
					$borrow[$id]['state_name']="逾期中";
					break;
					case 9:
					$borrow[$id]['state_name']="已完成";
					break;
					case 10:
					$borrow[$id]['state_name']="立即担保";
					break;
					case 11:
					$borrow[$id]['state_name']="回购中";
					break;
			}
			unset($borrow[$id]['privacy']);
			unset($borrow[$id]['password']);
			unset($borrow[$id]['data']);
			unset($borrow[$id]['content']);
			unset($borrow[$id]['review_note']);
			unset($borrow[$id]['time']);
			unset($borrow[$id]['join_date']);
			unset($borrow[$id]['member_total_score']);
		}
		}
		$user=D("User");
		$user = $user->relation(true)->select();
		$membership_grade=M('membership_grade');
		$grade_list = $membership_grade->field('name,img,max,min')->order('`id` asc')->select();
		$citys=$this->city();
		foreach($user as $id=>$ur){
			$users[$id]['id']=$ur['id'];
			$users[$id]['username']=$ur['username'];
			$users[$id]['member_total_score']=$ur['member_total_score'];
			$location=explode(" ",$ur['userinfo']['location']);
			
			if($location[0] && $location[1] && $location[2]){
			$location=$citys[$location[0]]." ".$citys[$location[1]]." ".$citys[$location[2]];
			}
			$users[$id]['location']=$location;
		}
		unset($user);	
		foreach($users as $id=>$us){
			if($grade_list){
			foreach($grade_list as $gt){
				if($us['member_total_score']>=$gt["min"] and $us['member_total_score']<=$gt["max"]){
					$users[$id]['member_total_name']=$gt['name'];
					$users[$id]['member_total_img']=$gt['img'];
					break 1;
				}
			}
			}
		}
		if($borrow){
		foreach($borrow as $id=>$bw){
			foreach($users as $us){
				if($bw['uid']==$us['id']){
					$borrow[$id]['member_total_name']=$us['member_total_name'];
					$borrow[$id]['member_total_img']=$us['member_total_img'];
					$borrow[$id]['location']=$us['location'];
					break 1;
				}
			}
		}
		}
		return $borrow;
	}
	
   /**
	*
	* @投标记录
	* @type		记录状态
	* @bid		标ID
	* @uid		用户ID
	* @details	显示各记录标的详情
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function bidRecords($type,$bid=0,$uid=0,$details=0){	
        $user_log=M('borrow_log');
		if($type){
			if($type && isset($bid)){
				$user_log=$user_log->where('`type`='.$type)->select();
				if($user_log){
					foreach($user_log as $id=>$ulog){
						$user_log[$id]['actionname']=json_decode($ulog['actionname'], true);
						if($bid>0){
							if($user_log[$id]['actionname']['bid']==$bid){	//显示对应ID的
								if($uid>0){	//指定标和用户
									if($user_log[$id]['actionname']['uid']==$uid){
										$u_log[]=$user_log[$id];
									}
								}else{
									$u_log[]=$user_log[$id];
								}
							}
								
						}else{
							if($user_log[$id]['actionname']['uid']==$uid){	//显示对应用户
								$user_log[$id]['details']=reset($this->borrow_unicom($user_log[$id]['actionname']['bid']));			//标详情
								if($details>0){
									
									switch($type){
										case 3:	//投标中
										if($user_log[$id]['details']['state']==1){
											$u_log[]=$user_log[$id];
										}
										break;
										case 7:	//还款中
										if($user_log[$id]['details']['state']==7){
											$u_log[]=$user_log[$id];
										}
										break;
										case 9:	//已完成
										if($user_log[$id]['details']['state']==9){
											$u_log[]=$user_log[$id];
										}
										break;
										case 11:	//逾期
										if($user_log[$id]['details']['state']==8){
											$u_log[]=$user_log[$id];
										}
										break;
										case 13:	//担保
										if($user_log[$id]['details']['type']==5){
											$u_log[]=$user_log[$id];
										}
										break;
										case 15:	//投资流转
										if($user_log[$id]['details']['type']==7){
											$u_log[]=$user_log[$id];
										}
										break;
										case 16:	//借款流转
										if($user_log[$id]['details']['type']==7){
											$u_log[]=$user_log[$id];
										}
										break;
									}
									
								}else{
									$u_log[]=$user_log[$id];
								}
								
							}
						}
					}
				}
				return $u_log;
			}else{
				return $user_log=$user_log->where('`type`='.$type)->select();
			}
			
		}else{
			return $user_log=$user_log->select();
		}
		
    }
	
   /**
	*
	* @投标处理-借款人信息
	* @field	标ID
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function borr($id){
		if(!$id){
			return false;
		}
		$borrow=$this->borrow_information($id);
		$borr=array(
					'id'			=>$borrow[0]['id'],
					'surplus'		=>$borrow[0]['surplus'],
					'assure'		=>$borrow[0]['assure'],
					'uid'			=>$borrow[0]['uid'],
					'type'			=>$borrow[0]['type'],
					'title'			=>$borrow[0]['title'],
					'password'		=>$borrow[0]['password'],
					'min'			=>$borrow[0]['min'],
					'max'			=>$borrow[0]['max'],
					'total_money'	=>$borrow[0]['total_money'],
					'available_funds'=>$borrow[0]['available_funds'],
					'freeze_funds'	=>$borrow[0]['freeze_funds'],
					'vip_audit'		=>$borrow[0]['vip_audit'],
					'candra'		=>$borrow[0]['candra'],
					'deadline'		=>$borrow[0]['deadlines'],
					'deadlinea'		=>$borrow[0]['deadline'],
					'reward_type'	=>$borrow[0]['reward_type'],
					'reward'		=>$borrow[0]['reward'],
					'money'			=>$borrow[0]['money'],
					'assures'		=>$borrow[0]['assures'],
					'rates'			=>$borrow[0]['rates'],
					'username'		=>$borrow[0]['username'],
					);
		unset($borrow);
		return $borr;
	}
	
   /**
	*
	* @资金表
	* @field		需要的字段
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function moneys($field){
		$money=M('money');
		$money=$money->field($field)->select();

		if(!$money){
			$this->error("请提交正确的field，如没有可为空！");
		}
		foreach($money as $my){
			$moneys[$my['uid']]=$my;
		}
		return $moneys;
	}
	
   /**
	*
	* @资金单条记录
	* @uid		用户id
	* @field	需要的字段
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function moneySingle($uid,$field){
		$money=$this->moneys($field);
		return $money[$uid];
	}
	
	/**
	 *
	 * @资金记录
	 * @uid			用户ID
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 *
	 */
	public function moneyRecord($uid){
		$uids=$uid?' and uid='.$uid:'';
		$money_log=D('Money_log');
		$list=$money_log->relation(true)->where('type=0'.$uids)->order('time DESC,id DESC ')->select();	//资金使用记录
		return $list;
		
	}
	
   /**
	*
	* @奖励计算
	* @arr		所需用于处理奖励参数
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function rewardCalculation($arr){
		if($arr['reward_type']==1){
			$reward=$arr['reward']/$arr['money']*$arr['price'];
		}elseif($arr['reward_type']==2){
			$reward=($arr['money']*$arr['reward']*0.01)/$arr['money']*$arr['price'];
		}
		return round($reward,2);
	}
	
   /**
	*
	* @借款管理费计算公式
	* @uid		用户ID
	* @price	借款本金
	* @deadline	期限
	* @公式		借款总金额*借款手续费
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function interest($uid,$price,$deadline=''){
		$system=$this->systems();
		$vip=M('vip_points');
		$audit=$vip->where('uid='.$uid)->find();
		if($audit['audit']==2){//利息管理费
			$interest_manage=$system['sys_vipInterestMF'];	
		}else{
			$interest_manage=$system['sys_InterestMF'];
		}
		if($deadline>0){	//如果是流转标
			return round($price*$interest_manage,2)*$deadline;
		}else{
			return round($price*$interest_manage,2);
		}
		
	}
	
   /**
	*
	* @逾期借款管理费计算公式
	* @price	借款本金
	* @days		天数
	* @公式		借款总金额*逾期手续费
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function overdueInterest($price,$days){
		$system=$this->systems();
		if($days<=30){	//小于30天
			$interest_manage=$system['sys_overdue'];	
			$p=round($price*$interest_manage*$days,2);
		}else{
			$interest_overdue=$system['sys_overdue'];	//30天前的
			$interest_manage=$system['sys_overdues'];//30天后的
			$p=round($price*$interest_overdue*30+$price*($days-30)*$interest_manage,2);
		}
		return $p;
	}
	
   /**
	*
	* @逾期借款罚息计算公式
	* @price	借款本金
	* @days		天数
	* @公式		借款总金额*逾期罚息费
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function penaltyInterest($price,$days){
		$system=$this->systems();
		if($days<=30){	//小于30天
			$interest_manage=$system['sys_penaltyint'];	
			$p=round($price*$interest_manage*$days,2);
		}else{
			$interest_penaltyint=$system['sys_penaltyint'];	//30天前的
			$interest_manage=$system['sys_penaltyints'];//30天后的
			$p=round($price*$interest_penaltyint*30+$price*($days-30)*$interest_manage,2);
		}
		return $p;
	}
	
	/**
	 * @还款计划
	 * @id		标ID
	 * @--------------
	 * @nper	期数
	 * @uid		会员
	 * @bid		标ID
	 * @money	资金
	 * @time	时间
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	 function refunds($id=2){
		$borrowing=M('borrowing');
		$refund=M('refund');
		$borrow=$borrowing->where('id='.$id)->find();
		$counters=$this->counters($borrow['money'],$borrow['rates'],$borrow['deadline'],$borrow['candra'],$borrow['way']);	//普通标还款计划
		if($borrow['candra']==0){	//如果是月标循环还款计划
			for($i=1;$i<=$borrow['deadline'];$i++){	//换算出每月还款时间
				//$time[]=$this->week(strtotime("+$i month"));
				$time[]=$this->endMonth($i);
			}
			unset($counters['total']);
			unset($counters['interest']);
			foreach($counters as $id=>$coun){
				$refun['nper']=$id+1;
				$refun['uid']=$borrow['uid'];
				$refun['bid']=$borrow['id'];
				$refun['money']=$coun['refund'];
				$refun['interest']=$coun['interest'];
				$refun['time']=$time[$id];
				$refund->add($refun);
			}	
		}else{	//如果是天标，直接显示最终还款计划
				$refun['nper']=1;
				$refun['uid']=$borrow['uid'];
				$refun['bid']=$borrow['id'];
				$refun['money']=$counters['total'];
				$refun['interest']=$counters['interest'];
				$refun['time']=$this->week(strtotime("+$borrow[deadline] day"));
				$refund->add($refun);
		}
	 }
	
	/**
	 * @收款计划
	 * @id		标ID
	 * @--------------
	 * @nper	期数
	 * @uid		会员
	 * @bid		标ID
	 * @money	资金
	 * @time	时间
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	 function collection($id,$uid){
		$borrowing=M('borrowing');
		$refund=M('collection');
		$borrow=$borrowing->where('id='.$id)->find();
		$bid_record=$this->specifyUser('7',$id,$uid);
		foreach($bid_record as $record){
			$counters=$this->counters($record['total'],$borrow['rates'],$borrow['deadline'],$borrow['candra'],$borrow['way']);	//普通标还款计划
			if($borrow['candra']==0){	//如果是月标循环还款计划
				for($i=1;$i<=$borrow['deadline'];$i++){	//换算出每月还款时间
					//$time[]=$this->week(strtotime("+$i month"));
					$time[]=$this->endMonth($i);
				}
				unset($counters['total']);
				unset($counters['interest']);
				foreach($counters as $id=>$coun){
					$refun[$id]['nper']=$id+1;
					$refun[$id]['uid']=$uid;
					$refun[$id]['bid']=$borrow['id'];
					$refun[$id]['money']=$coun['refund'];
					$refun[$id]['interest']=$coun['interest'];
					$refun[$id]['time']=$time[$id];
					
				}	
				$refund->addAll($refun);
			}else{	//如果是天标，直接显示最终还款计划
					$refun['nper']=1;
					$refun['uid']=$uid;
					$refun['bid']=$borrow['id'];
					$refun['money']=$counters['total'];
					$refun['interest']=$counters['interest'];
					$refun['time']=$this->week(strtotime("+$borrow[deadline] day"));
					$refund->add($refun);
			}
		}
	 }
	 
	/**
	 * @还款计划(流转)
	 * @id		标ID
	 * @fid		收款表期数
	 * @--------------
	 * @nper		期数
	 * @uid		会员
	 * @bid		标ID
	 * @money	资金
	 * @time		时间
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	 function frefunds($id,$fid){
		$models = new Model();
		$borrowing=M('borrowing');
		$user_log=M('borrow_log');
		$refund=M('refund');
		$borrow=$borrowing->where('id='.$id)->find();
		$bid_record=$user_log->where('type=15')->order('`time` DESC')->find();
		$bid_record['actionname']=json_decode($bid_record['actionname'], true);
		$counters=$this->counters($bid_record['actionname']['operation'],$borrow['rates'],$bid_record['actionname']['deadline'],$borrow['candra'],$borrow['way']);	//流转标还款计划
		$collection=$refund->field('nper')->where('bid='.$id.' and uid='.$borrow['uid'])->order('`id` DESC')->find();
		if($borrow['candra']==0){	//如果是月标循环还款计划
			//换算出每月还款时间
			$bid_time=$bid_record['actionname']['deadline'];
			//$time=$this->week(strtotime("+$bid_time month"));
			$time=$this->endMonth($bid_time);
			$refun['nper']=$collection['nper']+1;
			$refun['uid']=$borrow['uid'];
			$refun['bid']=$id;
			$refun['fid']=$fid;
			$refun['money']=$counters['total'];
			$refun['interest']=$counters['interest'];
			$refun['time']=$time;
			return $models->table('ds_refund')->add($refun);
		}else{	//如果是天标，直接显示最终还款计划
			$bid_time=$bid_record['actionname']['deadline'];
			$refun['nper']=$collection['nper']+1;
			$refun['uid']=$borrow['uid'];
			$refun['bid']=$id;
			$refun['fid']=$fid;
			$refun['money']=$counters['total'];
			$refun['interest']=$counters['interest'];
			$refun['time']=$this->week(strtotime("+$bid_time day"));
			return $models->table('ds_refund')->add($refun);
		}
	 }
	
	/*
	 * @收款计划(流转)
	 * @id		标ID
	 * @uid		投标人ID
	 * @--------------
	 * @nper	期数
	 * @uid		会员
	 * @bid		标ID
	 * @money	资金
	 * @time	时间
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	 function fcollection($id=1,$uid=2){
		$models = new Model();
		$borrowing=M('borrowing');
		$user_log=M('borrow_log');
		$refund=M('collection');
		$borrow=$borrowing->where('id='.$id)->find();
		$bid_record=$user_log->where('type=15')->order('`time` DESC')->find();
		$bid_record['actionname']=json_decode($bid_record['actionname'], true);
		$counters=$this->counters($bid_record['actionname']['operation'],$borrow['rates'],$bid_record['actionname']['deadline'],$borrow['candra'],$borrow['way']);	//流转标还款计划
		$collection=$refund->field('nper')->where('bid='.$id.' and uid='.$uid)->order('`id` DESC')->find();
		if($borrow['candra']==0){	//如果是月标循环还款计划
			//换算出每月还款时间
			$bid_time=$bid_record['actionname']['deadline'];
			//$time=$this->week(strtotime("+$bid_time month"));
			$time=$this->endMonth($bid_time);
			$refun['uid']=$uid;
			$refun['nper']=$collection['nper']+1;
			$refun['bid']=$borrow['id'];
			$refun['money']=$counters['total'];
			$refun['interest']=$counters['interest'];
			$refun['time']=$time;
			return $models->table('ds_collection')->add($refun);
		}else{	//如果是天标，直接显示最终还款计划
				$bid_time=$bid_record['actionname']['deadline'];
				$refun['uid']=$uid;
				$refun['nper']=$collection['nper']+1;
				$refun['bid']=$borrow['id'];
				$refun['money']=$counters['total'];
				$refun['interest']=$counters['interest'];
				$refun['time']=$this->week(strtotime("+$bid_time day"));
				return $models->table('ds_collection')->add($refun);
		}
	 }
	
	/**
	 *
	 * @判断是否为周末，是则延长至星期一
	 * @str		传入时间
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 *
	 */
	function week($str){
		if(date('w',$str)==6){	//如果是星期六
			return $str+172800;	//加2天
		}else if(date('w',$str) == 0){	//如果是星期天
			return $str+86400;	//加1天
		}else{
			return $str;
		}
	}
	
	/**
	 *
	 * @计算正确时间
	 * @interval间隔
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 *
	 */
	 function endMonth($interval){
		//$firstday = date("Y-m-01",$time);
		$time=time();
		$firstday = strtotime("+$interval month");//下N个月
		$thismonth=date("m",$time);//本月
		$nextmonth=date("m",$firstday);//下N个月
		$poor=$nextmonth-$thismonth;
		$poor=$poor>0?$poor:($poor+12);//如果是正数直接显示，不是则加12
		if($poor !== $interval){	//下N个月减本月不等于间隔时间
			$firstday = strtotime(date("Y-m-01",$firstday))-3600;
			$t=$this->week($firstday);
		}else{
			$t=$this->week($firstday);
		}
		return $t;
		//echo date("Y-m-d H:i:s",$t);
		//exit;
	 }
	 
	/**
	 *
	 * @利息计算器
	 * @a		贷款本金
	 * @i		贷款年利率
	 * @n		贷款期限
	 * @u		0月1天
	 * @w		0月付息到期还本1月付本息2等额本金3等额本息
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 *
	 */
	public function counters($a,$i,$n,$u=0,$w=0){	
		$curlPost = "dswjw=".$_SERVER['SERVER_NAME']."&dswjn=".DS_NUMbER."&dsa=".$a."&dsi=".$i."&dsn=".$n."&dsu=".$u."&dsw=".$w;
		$url='http://www.tifaweb.com/Api/Core/countersApi';  
		$in=$this->Curl($curlPost,$url);
		$remote=$this->remote($in);
		if(!$remote){
			$url='http://www.dswjcms.com/Api/Core/countersApi';  
			$in=$this->Curl($curlPost,$url);
			$remote=$this->remote($in);
		}
		return $remote;
	}
	
   /**
	* @秒标利息、奖励计算
	* @arr		标数据
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function secondsIncidentals($arr){
		if($arr['reward_type']==1){
			$reward=$arr['reward'];
		}elseif($arr['reward_type']==2){
			$reward=($arr['money']*$arr['reward']*0.01);
		}
		$reward=round($reward,2);	//奖励
		if($arr['candra']==1){
			$deadline=$arr['deadline']/30;
		}else{
			$deadline=$arr['deadline'];
		}
		$interest=round($arr['money']*($arr['rates']*0.01/12*$deadline),10);	//利息
		return $reward+$interest;
	}
	
   /**
	*
	* @获取用户记录表对应类型的数据
	* @mid		用户记录类型
	* @id		标ID
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function specifyUser($mid,$id=0,$uid=0){
		$bid_records=$this->bidRecords($mid,$id,$uid);
		foreach($bid_records as $bds){
			if($bid_record[$bds['actionname']['uid']]){
				$bid_record[$bds['actionname']['uid']]['money']=$this->moneySingle($bds['actionname']['uid'],'uid,total_money,available_funds,freeze_funds,due_in,stay_still,stay_interest,make_interest,make_reward');
				$bid_record[$bds['actionname']['uid']]['total']=$bds['actionname']['operation']+$bid_record[$bds['actionname']['uid']]['total'];
				$bid_record[$bds['actionname']['uid']]['interest']=$bds['actionname']['interest']+$bid_record[$bds['actionname']['uid']]['interest'];
				$bid_record[$bds['actionname']['uid']]['operation_reward']=$bds['actionname']['operation_reward']+$bid_record[$bds['actionname']['uid']]['operation_reward'];
			}else{
				$bid_record[$bds['actionname']['uid']]['id']=$bds['actionname']['uid'];
				$bid_record[$bds['actionname']['uid']]['money']=$this->moneySingle($bds['actionname']['uid'],'uid,total_money,available_funds,freeze_funds,due_in,stay_still,stay_interest,make_interest,make_reward');
				$bid_record[$bds['actionname']['uid']]['total']=$bds['actionname']['operation'];
				$bid_record[$bds['actionname']['uid']]['interest']=$bds['actionname']['interest'];
				$bid_record[$bds['actionname']['uid']]['operation_reward']=$bds['actionname']['operation_reward'];
			}
		}
		return $bid_record;
	}
	
   /**
	* 
	* @获取用户出借记录（用于协议书）
	* @mid		用户记录类型
	* @id		标ID
	* @fd       不合并
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function lendUser($mid,$id=0,$fd=0){
		$bid_records=$this->bidRecords($mid,$id);
		$user=M('user');
		foreach($bid_records as $id=> $bds){
			if($fd==1){//不合并
				$cand=$bds['actionname']['candra']?'天':'个月';
				$bid_record[$id]['deadline']=$bds['actionname']['deadline'].$cand;
				$bid_record[$id]['id']=$bds['actionname']['uid'];
				$bid_record[$id]['username']=reset($user->field('username')->where('id='.$bds['actionname']['uid'])->find());
				$bid_record[$id]['total']=$bds['actionname']['operation'];
				$bid_record[$id]['totals']=$bds['actionname']['operation']+$bds['actionname']['interest']+$bds['actionname']['operation_reward'];
			}else{
				if($bid_record[$bds['actionname']['uid']]){
					$bid_record[$bds['actionname']['uid']]['total']=$bds['actionname']['operation']+$bid_record[$bds['actionname']['uid']]['total'];
					$bid_record[$bds['actionname']['uid']]['totals']=$bds['actionname']['operation']+$bid_record[$bds['actionname']['uid']]['total']+$bds['actionname']['interest']+$bds['actionname']['operation_reward'];
				}else{
					$cand=$bds['actionname']['candra']?'天':'个月';
					$bid_record[$bds['actionname']['uid']]['deadline']=$bds['actionname']['deadline'].$cand;
					$bid_record[$bds['actionname']['uid']]['id']=$bds['actionname']['uid'];
					$bid_record[$bds['actionname']['uid']]['username']=reset($user->field('username')->where('id='.$bds['actionname']['uid'])->find());
					$bid_record[$bds['actionname']['uid']]['total']=$bds['actionname']['operation'];
					$bid_record[$bds['actionname']['uid']]['totals']=$bds['actionname']['operation']+$bds['actionname']['interest']+$bds['actionname']['operation_reward'];
				}
			}
		}
		return $bid_record;
	}
	
   /**
	*
	* @借款标满标审核时
	* @borr		借款标信息
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function withAudit($borr){	
        $bid_record=$this->specifyUser('3',$borr['id']);
		$money=M('money');
		$models = new Model();
		foreach($bid_record as $k=>$brd){
			$interestReward=$brd['interest']+$brd['operation_reward'];	//利息+奖励
			$array['type']				=5;
			$array['uid']				= $brd['id'];
			$array['bid']				=$borr['id'];
			$array['instructions']		='对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>的投标已生效，等待复审';
			$moneyarr['total_money']	=$array['total']				=$brd['money']['total_money']-$brd['total'];
			$moneyarr['freeze_funds']	=$array['freeze']				=$brd['money']['freeze_funds']-$brd['total'];
			$array['interest']			=$brd['interest'];
			$array['operation']			=$brd['total'];
			$moneyarr['due_in']			=$array['collected']			=$brd['money']['due_in']+$brd['total']+$interestReward;
			$array['operation_reward']	=$brd['operation_reward'];
			$moneyarr['stay_interest']	=$array['stay_interest']		=$brd['money']['stay_interest']+$brd['interest'];
			$borrowlog=R('Comm/borrowLog',array($array));	
			$money=$models->table('ds_money')->where('uid='.$brd['id'])->save($moneyarr);//投资者资金操作
			unset($array);
			unset($moneyarr);
			unset($bid_records);
			unset($bid_record);
		}
		
		//借款者操作
		$bid_record=$this->specifyUser('4',$borr['id']);
		$bid_record=reset($bid_record);
		$interestReward=$bid_record['interest']+$bid_record['operation_reward'];	//利息+奖励
		$array['type']				=6;
		$array['uid']				= $borr['uid'];
		$array['bid']				=$borr['id'];
		$array['instructions']		='<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>投标已完成，等待复审';
		$moneyarr['total_money']	=$array['total']				=$bid_record['money']['total_money']+$bid_record['total'];
		$moneyarr['freeze_funds']	=$array['freeze']				=$bid_record['money']['freeze_funds']+$bid_record['total'];
		$array['interest']				=$bid_record['interest'];
		$array['operation_reward']				=$bid_record['operation_reward'];
		$array['operation']			=$bid_record['total'];
		$moneyarr['stay_still']		=$array['also']					=$bid_record['money']['stay_still']+$bid_record['total']+$interestReward;
		$borrowlogs=R('Comm/borrowLog',array($array));	
		$moneys=$models->table('ds_money')->where('uid='.$bid_record['id'])->save($moneyarr);//借款者资金操作
		if($borrowlog && $money && $borrowlogs && $moneys){	//操作全成功返回1
			return 1;
		}
    }	
	
   /**
	*
	* @借款标满标审核通过时
	* @borr		借款标信息
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function fullApproval($borr){
		$msgTools = A('msg','Event');
        $bid_record=$this->specifyUser('3',$borr['id']);
		$money=M('money');
		foreach($bid_record as $k => $brd){
			if($borr['state']==7){	//审核通过
				$array['type']				=7;
				$array['uid']				= $brd['id'];
				$array['bid']				=$borr['id'];
				$array['instructions']		='对<a href="'.__ROOT__.'/Home/Loan/invest/'.$brd['id'].'.html">【'.$borr['title'].'】</a>的投标已生效，等待收款';
				$moneyarr['total_money']	=$array['total']=$brd['money']['total_money']-$brd['total']+$brd['operation_reward'];
				$moneyarr['available_funds']=$array['freeze']=$brd['money']['available_funds']+$brd['operation_reward'];
				$moneyarr['freeze_funds']	=$array['freeze']=$brd['money']['freeze_funds']-$brd['total'];
				$array['operation']			=$brd['total'];
				$array['interest']			=$brd['interest'];
				$array['operation_reward']	=$brd['operation_reward'];
				$moneyarr['due_in']			=$array['collected']=$brd['money']['due_in']+$brd['total']+$brd['interest'];	//待收=借出+利息
				$moneyarr['stay_interest']	=$array['stay_interest']=$brd['money']['stay_interest']+$brd['interest'];
				$moneyarr['make_reward']	=$array['make_reward']=$brd['make_reward']+$brd['operation_reward'];
				$borrowlog=R('Comm/borrowLog',array($array));	
				$moneys=$money->where('uid='.$brd['id'])->save($moneyarr);//借款者资金操作
				//记录添加点
				//投资者
				$available_funds=$money->field('total_money,available_funds,make_reward,freeze_funds')->where('uid='.$brd['id'])->find();	//总额和可用余额
				
				if($array['make_reward']>0){	//如果有奖励
					$moneyLog=R('Comm/moneyLog',array(array(0,'对【'.$borr['title'].'】投标成功，扣除冻结资金',$brd['total'],$borr['username'],($available_funds['total_money']-$brd['operation_reward']),($available_funds['available_funds']-$brd['operation_reward']),$available_funds['freeze_funds'],$brd['id'])));//资金记录
					$moneyLog=R('Comm/moneyLog',array(array(0,'对【'.$borr['title'].'】投标成功，获得奖励',$brd['operation_reward'],$borr['username'],$moneyarr['total_money'],$moneyarr['available_funds'],$available_funds['freeze_funds'],$brd['id'])));//资金记录
				}else{
					$moneyLog=R('Comm/moneyLog',array(array(0,'对【'.$borr['title'].'】投标成功，扣除冻结资金',$brd['total'],$borr['username'],$available_funds['total_money'],$available_funds['available_funds'],$available_funds['freeze_funds'],$brd['id'])));//资金记录
				}
				$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】投标成功','对<a href="'.__ROOT__.'/Home/Loan/invest/'.$brd['id'].'.html">【'.$borr['title'].'】</a>的投标已生效，等待收款','admin',$brd['id']);//站内信
				$this->collection($borr['id'],$brd['id']);//收款记录
			}else{	//审核失败
				$array['type']				=7;
				$array['uid']				= $brd['id'];
				$array['bid']				=$borr['id'];
				$array['instructions']		='对<a href="'.__ROOT__.'/Home/Loan/invest/'.$brd['id'].'.html">【'.$borr['title'].'】</a>的投标复审失败，资金返还';
				$moneyarr['total_money']	=$array['total']=$brd['money']['total_money'];
				$moneyarr['freeze_funds']	=$array['freeze']=$brd['money']['freeze_funds']-$brd['total'];
				$moneyarr['available_funds']=$array['freeze']=$brd['money']['available_funds']+$brd['total'];
				$array['operation']			=$brd['total'];
				$array['interest']			=$brd['interest'];
				$array['operation_reward']	=$brd['operation_reward'];
				$borrowlog=R('Comm/borrowLog',array($array));	
				$moneys=$money->where('uid='.$brd['id'])->save($moneyarr);//借款者资金操作
				//记录添加点
				//投资者
				$available_funds=$money->field('total_money,available_funds,make_reward,freeze_funds')->where('uid='.$brd['id'])->find();	//总额和可用余额
				$moneyLog=R('Comm/moneyLog',array(array(0,'对【'.$borr['title'].'】投标复审失败，资金返还',$brd['total'],$borr['username'],$available_funds['total_money'],$available_funds['available_funds'],$available_funds['freeze_funds'],$brd['id'])));//资金记录
				$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】投标复审失败','对<a href="'.__ROOT__.'/Home/Loan/invest/'.$brd['id'].'.html">【'.$borr['title'].'】</a>的投标复审失败，资金成功返还','admin',$brd['id']);//站内信
			}
			unset($array);
			unset($bid_records);
			unset($bid_record);
			unset($moneyarr);
			unset($available_funds);
			unset($moneyLog);
			unset($sendMsg);
		}	
		if($borrowlog){//借款者操作
			$bid_record=$this->specifyUser('4',$borr['id']);
			$bid_record=reset($bid_record);
			$interestReward=$bid_record['interest']+$bid_record['operation_reward'];	//利息+奖励
			if($borr['state']==7){	//审核通过
				$array['type']				=8;
				$array['uid']				= $borr['uid'];
				$array['bid']				=$borr['id'];
				$array['instructions']		='<a href="'.__ROOT__.'/Home/Loan/invest/'.$brd['id'].'.html">【'.$borr['title'].'】</a>投标已成功，等待还款';
				$array['total']				=$bid_record['money']['total_money'];
				$moneyarr['total_money']	=$array['total']=$bid_record['money']['total_money']+$bid_record['total']-$bid_record['operation_reward'];
				$moneyarr['available_funds']=$array['available']=$bid_record['money']['available_funds']+$bid_record['total']-$bid_record['operation_reward'];
				$moneyarr['stay_still']		=$array['also']=$bid_record['money']['stay_still']+$bid_record['total']+$bid_record['interest'];
				$array['interest']			=$bid_record['money']['interest'];
				$array['operation_reward']	=$bid_record['operation_reward'];
				$array['operation']			=$bid_record['total'];
			$borrowlog=R('Comm/borrowLog',array($array));	
			$moneys=$money->where('uid='.$bid_record['id'])->save($moneyarr);//借款者资金操作
			$available_funds=$money->field('total_money,available_funds,make_reward,freeze_funds')->where('uid='.$borr['uid'])->find();	//总额和可用余额
				//记录添加点
				//投资者
				$uid=$this->userinfo($borr['uid']);	
				
				if($brd['operation_reward']>0){	//如果有奖励
					$moneyLog=R('Comm/moneyLog',array(array(0,'【'.$borr['title'].'】投标成功，获得资金',$bid_record['total'],$uid['username'],($available_funds['total_money']+$bid_record['operation_reward']),($available_funds['available_funds']+$bid_record['operation_reward']),$available_funds['freeze_funds'],$borr['uid'])));//资金记录
					$moneyLog=R('Comm/moneyLog',array(array(0,'【'.$borr['title'].'】投标成功，扣除奖励',$bid_record['operation_reward'],$uid['username'],$available_funds['total_money'],$moneyarr['available_funds'],$available_funds['freeze_funds'],$borr['uid'])));//资金记录
				}else{
					$moneyLog=R('Comm/moneyLog',array(array(0,'【'.$borr['title'].'】投标成功，获得资金',$bid_record['total'],$uid['username'],$available_funds['total_money'],$available_funds['available_funds'],$available_funds['freeze_funds'],$borr['uid'])));//资金记录
				}
				$sendMsg=$msgTools->sendMsg(3,'【'.$borr['title'].'】通过复审','<a href="'.__ROOT__.'/Home/Loan/invest/'.$brd['id'].'.html">【'.$borr['title'].'】</a>投标已成功，等待还款','admin',$borr['uid']);//站内信	
				$this->refunds($borr['id']);	//还款记录
			}else{
				$sendMsg=$msgTools->sendMsg(3,'【'.$borr['title'].'】复审失败','<a href="'.__ROOT__.'/Home/Loan/invest/'.$brd['id'].'.html">【'.$borr['title'].'】</a>复审失败','admin',$borr['uid']);//站内信	
			}
			
		}
		
    }
	
   /**
	*
	*流标处理
	*borr				//借款标信息
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function flowStandard($borr){
		$msgTools = A('msg','Event');
        $bid_record=$this->specifyUser('3',$borr['id']);
		$money=M('money');
		foreach($bid_record as $brd){	
			$moneyarr['total_money']	=$array['total']=$brd['money']['total_money'];
			$moneyarr['freeze_funds']	=$array['freeze']=$brd['money']['freeze_funds']-$brd['total'];
			$moneyarr['available_funds']=$array['freeze']=$brd['money']['available_funds']+$brd['total'];
			$moneys=$money->where('uid='.$brd['id'])->save($moneyarr);//借款者资金操作
			//记录添加点
			//投资者
			$available_funds=$money->field('total_money,available_funds,make_reward,freeze_funds')->where('uid='.$brd['id'])->find();	//总额和可用余额
			$moneyLog=R('Comm/moneyLog',array(array(0,'【'.$borr['title'].'】流标，资金返还',$brd['total'],$borr['username'],$available_funds['total_money'],$available_funds['available_funds'],$available_funds['freeze_funds'],$brd['id'])));//资金记录
			$sendMsg=$msgTools->sendMsg(3,'【'.$borr['title'].'】流标','对<a href="'.__ROOT__.'/Home/Loan/invest/'.$brd['id'].'.html">【'.$borr['title'].'】</a>流标，资金成功返还','admin',$brd['id']);//站内信
			unset($array);
			unset($bid_records);
			unset($bid_record);
			unset($moneyarr);
		}	
		if($borrowlog){//借款者操作
			$bid_record=$this->specifyUser('4',$borr['id']);
			$bid_record=reset($bid_record);
			$sendMsg=$msgTools->sendMsg(3,'【'.$borr['title'].'】已流标','<a href="'.__ROOT__.'/Home/Loan/invest/'.$brd['id'].'.html">【'.$borr['title'].'】</a>已流标','admin',$borr['uid']);//站内信	
			
		}
		
    }
	
   /**
	* @用户手动还款
	* @bid		标ID
	* @id		期数
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	function repayment($bid,$id){
		$models = new Model();
		$msgTools = A('msg','Event');
		$refund=M('refund');
		$money=M('money');
		$borrowing=D('Borrowing');
		$collection=M('collection');
		$refun=$refund->where('bid='.$bid.' and nper='.$id)->find();
		$end_refun=$refund->where('bid='.$bid.' and nper='.($id+1))->find();//查看借款是否为最后一期
		$borr=$borrowing->relation(true)->field('uid,title,money,type')->where('id='.$bid)->find();
		$colle=$collection->where('bid='.$bid.' and nper='.$id)->select();
		$mon=$money->where('uid='.$this->_session('user_uid'))->find();
		//平台手续费
		$interest=$this->interest($refun['uid'],$borr['money']);
		if($mon['available_funds']<($refun['money']+$interest)){	//判断用户资金是否够还款
			//$this->error("可用资金不足！");
			echo '<p class="red">可用资金不足！</p>';
			echo '<p class="jump">
            页面自动 <a href="__ROOT__/Center/loan/is.html">跳转</a> 等待时间： <b>3秒</b>
            </p>';
		}
		/*借款者操作*/
		//还款状态更新
		$refund->where('bid='.$bid.' and nper='.$id)->save(array('type'=>1));	
		//扣除用户资金
		$models->query("UPDATE `ds_money` SET `total_money` = total_money-".($refun['money']+$interest).",`available_funds` = available_funds-".($refun['money']+$interest).",`stay_still` = stay_still-".$refun['money']." WHERE `uid` =".$refun['uid']);
		$total=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$refun['uid'])->find();	//查询资金
		//记录添加点
		$userLog=R('Comm/userLog',array('对【'.$borr['title'].'】的第'.$id.'期还款'));//会员记录
		$moneyLog=R('Comm/moneyLog',array(array(0,'对【'.$borr['title'].'】的第'.$id.'期还款',$refun['money'],'投资者',($total['total_money']+$interest),($total['available_funds']+$interest),$total['freeze_funds'],$refun['uid'])));//资金记录
		$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】的第'.$id.'期还款','对<a href="'.__ROOT__.'/Loan/invest/'.$bid.'.html">【'.$borr['title'].'】</a>的第'.$id.'期还款成功','admin',$this->_session('user_name'));//站内信
		
		R('Comm/moneyLog',array(array(0,'对【'.$borr['title'].'】的第'.$id.'期还款扣除借款手续费',$interest,'平台',$total['total_money'],$total['available_funds'],$total['freeze_funds'])));//资金记录
		$msgTools->sendMsg(3,'对【'.$borr['title'].'】的第'.$id.'期还款扣除借款手续费','对<a href="'.__ROOT__.'/Loan/invest/'.$bid.'.html">【'.$borr['title'].'】</a>的第'.$id.'期还款成功，扣除借款手续费 '.$interest.' 元','admin',$this->_session('user_name'));//站内信
		/*投资者操作*/
		foreach($colle as $co){
			//还款状态更新
			$collection->where('bid='.$bid.' and nper='.$id.' and uid='.$co['uid'])->save(array('type'=>1));
			//增加用户资金
			$models->query("UPDATE `ds_money` SET `total_money` = total_money+".$co['money'].",`available_funds` = available_funds+".$co['money'].",`stay_interest` = stay_interest-".$co['interest'].",`make_interest` = make_interest+".$co['interest'].",`due_in` = due_in-".$co['money']." WHERE `uid` =".$co['uid']);
			$total=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$co['uid'])->find();	//查询资金
			//记录添加点
			$moneyLog=R('Comm/moneyLog',array(array(0,'【'.$borr['title'].'】第'.$id.'期收款',$co['money'],$borr['username'],$total['total_money'],$total['available_funds'],$total['freeze_funds'],$co['uid'])));//资金记录
			$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】第'.$id.'期收款','<a href="'.__ROOT__.'/Loan/invest/'.$bid.'.html">【'.$borr['title'].'】</a>第'.$id.'期成功收款','admin',$co['uid']);//站内信
		}
		if(!$end_refun){	//已还完
			/*if($borr['type']==5){	//如果是担保标
				$assure_records=$this->bidRecords('14',$bid);
				//担保还款
				//担保者操作
				foreach($assure_records as $as){
					$price=$as['actionname']['operation']*$borr['flow_deadline']*0.01;	//担保者可获奖励
					$models->query("UPDATE `ds_money` SET `total_money` = total_money+".$price.",`available_funds` = available_funds+".$price." WHERE `uid` =".$as['actionname']['uid']);//增加资金
					$total=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$as['actionname']['uid'])->find();	//查询资金
					//记录添加点
					$userLog=R('Comm/userLog',array('对【'.$borr['title'].'】的担保获得奖励',$as['actionname']['uid']));//会员记录
					$moneyLog=R('Comm/moneyLog',array(array(0,'对【'.$borr['title'].'】的担保获得奖励',$price,$borr['username'],$total['total_money'],$total['available_funds'],$total['freeze_funds'],$as['actionname']['uid'])));//资金记录
					$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】的担保获得奖励','<a href="'.__ROOT__.'/Loan/invest/'.$bid.'.html">对【'.$borr['title'].'】</a>的担保获得奖励','admin',$as['actionname']['uid']);//站内信
				}
				//借款者操作
				$price=$borr['money']*$borr['flow_deadline']*0.01;	//借款者扣除奖励
				$models->query("UPDATE `ds_money` SET `total_money` = total_money-".$price.",`available_funds` = available_funds-".$price." WHERE `uid` =".$borr['uid']);//增加资金
				$total=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$borr['uid'])->find();	//查询资金
				//记录添加点
				$userLog=R('Comm/userLog',array('【'.$borr['title'].'】的担保奖励支出',$borr['uid']));//会员记录
				$moneyLog=R('Comm/moneyLog',array(array(0,'【'.$borr['title'].'】的担保奖励支出',$price,$borr['username'],$total['total_money'],$total['available_funds'],$total['freeze_funds'],$borr['uid'])));//资金记录
				$sendMsg=$msgTools->sendMsg(3,'【'.$borr['title'].'】的担保奖励支出','<a href="'.__ROOT__.'/Loan/invest/'.$bid.'.html">【'.$borr['title'].'】</a>的担保奖励支出','admin',$borr['uid']);//站内信
			}*/
			$borrowing->where('id='.$bid)->save(array('state'=>9));
		}
		//$this->success('还款成功','__ROOT__/Center/loan/plan.html?bid='.$bid);
		echo '<p class="green">还款成功</p>';
		echo '<p class="jump">
		页面自动 <a href="__ROOT__/Center/loan/is.html">跳转</a> 等待时间： <b>3秒</b>
		</p>';
	}
	
   /**
	* @用户回购
	* @bid		标ID
	* @id		还款计划ID
	* @fid		投资期数
	* @uid		用户ID
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/

	function flowrepayment($bid,$id,$uid,$fid){
		$models = new Model();
		$msgTools = A('msg','Event');
		$refund=M('refund');
		$money=M('money');
		$borrowing=D('Borrowing');
		$collection=M('collection');
		$refun=$refund->where('id='.$id)->find();
		$borr=$borrowing->relation(true)->field('uid,title,money,min,flows,repos,limittime,min_limit,candra')->where('id='.$bid)->find();
		$colle=$collection->where('bid='.$bid.' and nper='.$fid)->find();
		$mon=$money->where('uid='.$this->_session('user_uid'))->find();
		$borr_log=$this->bidRecords(15,$refun['bid'],$uid);
		$deadline=$borr_log[0]['actionname']['deadline'];
		unset($borr_log);
		//平台手续费
		$interest=$this->interest($refun['uid'],($refun['money']-$refun['interest']),$deadline);
		if($mon['available_funds']<($refun['money']+$interest)){	//判断用户资金是否够还款
			//$this->error("可用资金不足！");
			echo '<p class="red">可用资金不足！</p>';
			echo '<p class="jump">
            页面自动 <a href="__ROOT__/Center/loan/isflow.html">跳转</a> 等待时间： <b>3秒</b>
            </p>';
		}
	/*借款者操*/
		//还款状态更新
		$refund->where('id='.$id)->save(array('type'=>1));
		$copi=($refun['money']-$refun['interest'])/$borr['min'];//认购份数
		//扣除用户资金
		$models->query("UPDATE `ds_money` SET `total_money` = total_money-".($refun['money']+$interest).",`available_funds` = available_funds-".($refun['money']+$interest).",`stay_still` = stay_still-".$refun['money']." WHERE `uid` =".$refun['uid']);
		$total=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$refun['uid'])->find();	//查询资金
		//记录添加点
		$userLog=R('Comm/userLog',array('对【'.$borr['title'].'】的回购'));//会员记录
		$moneyLog=R('Comm/moneyLog',array(array(0,'对【'.$borr['title'].'】的回购',$refun['money'],'投资者',($total['total_money']+$interest),($total['available_funds']+$interest),$total['freeze_funds'])));//资金记录
		$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】的回购','对<a href="'.__ROOT__.'/Loan/invest/'.$bid.'.html">【'.$borr['title'].'】</a>的回购成功','admin',$this->_session('user_name'));//站内信
		
		R('Comm/moneyLog',array(array(0,'对【'.$borr['title'].'】的回购扣除借款手续费',$interest,'平台',$total['total_money'],$total['available_funds'],$total['freeze_funds'])));//资金记录
		$msgTools->sendMsg(3,'对【'.$borr['title'].'】的回购扣除借款手续费','对<a href="'.__ROOT__.'/Loan/invest/'.$bid.'.html">【'.$borr['title'].'】</a>的回购成功，扣除借款手续费 '.$interest.' 元','admin',$this->_session('user_name'));//站内信
		
	
	/*投资者操*/
		
		//还款状态更新
		$collection->where('bid='.$bid.' and nper='.$fid)->save(array('type'=>1));
		//增加用户资金
		$models->query("UPDATE `ds_money` SET `total_money` = total_money+".$refun['money'].",`available_funds` = available_funds+".$colle['money'].",`stay_interest` = stay_interest-".$colle['interest'].",`make_interest` = make_interest+".$colle['interest'].",`due_in` = due_in-".$colle['money']." WHERE `uid` =".$colle['uid']);
		$total=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$colle['uid'])->find();	//查询资金
		//记录添加点
		$moneyLog=R('Comm/moneyLog',array(array(0,'【'.$borr['title'].'】被借款者回购成功收款',$colle['money'],$borr['username'],$total['total_money'],$total['available_funds'],$total['freeze_funds'],$colle['uid'])));//资金记录
		$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】被借款者回购成功收款','<a href="'.__ROOT__.'/Loan/invest/'.$bid.'.html">【'.$borr['title'].'】</a>被借款者回购成功收款','admin',$colle['uid']);//站内信
		
		//回购后更新表可认购数
		if($borr['candra']==0){	//获取用户选择的是月标还是天标
			$month=$borr['min_limit'];
			$limittime=strtotime("+$month month");
		}else{
			$day=$borr['min_limit'];
			$limittime=strtotime("+$day day");
		}
		
		if($limittime<$borr['limittime']){	//如果原借款期限比最低认购期限大就执行
			$models->query("UPDATE  `ds_borrowing` SET  `state`='1',`repos`=repos+".$copi.",`flows`=flows-".$copi."  WHERE `id` =".$bid);
		}else{
			if(($borr['flows']-$copi)==0){	//如果正在流转为0时
				$borrowing->where('id='.$bid)->save(array('state'=>9));
			}
		}
		//$this->success('还款成功','__ROOT__/Center/loan/flowplan.html?bid='.$bid);
		echo '<p class="green">回购成功</p>';
		echo '<p class="jump">
		页面自动 <a href="__ROOT__/Center/loan/isflow.html">跳转</a> 等待时间： <b>3秒</b>
		</p>';
	}
	
   /**
	* @逾期还款
	* @bid		标ID
	* @days		时间
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	function latePayment($bid,$days){
		$msgTools = A('msg','Event');
		$refund=M('refund');
		$overdue=D('Overdue');
		$coverdue=D('Coverdue');
		$money=M('money');
		$models = new Model();
		$collection=D('Collection');
		$borrows=D('Borrowing');
		$mon=$money->where('uid='.$this->_session('user_uid'))->find();
		$overd=$overdue->relation(true)->where('bid='.$bid)->find();
		$overd_int=$this->overdueInterest($overd['money'],$overd['days']);//逾期管理费
		$penalty_int=$this->penaltyInterest($overd['money'],$overd['days']);//逾期管理费
		$voerd_total=$overd['money']+$overd_int+$penalty_int;//逾期还款总金额(本息+逾期管理费+罚息)
		if($mon['available_funds']<$voerd_total){	//判断用户资金是否够还款
			$this->error("可用资金不足！");
		}
		if($days<=30){	//小于30天
		
			//执行还款
			$cover=$coverdue->relation(true)->where('bid='.$bid)->select();
			foreach($cover as $co){
				$ar['money']=$co['money']+$this->penaltyInterest($co['money'],$co['days']);//投资人应收本息+罚息
				$ar['interest']=$co['interest']+$this->penaltyInterest($co['money'],$co['days']);//投资人应收利息+罚息
				$models->query("UPDATE `ds_money` SET `total_money` = total_money+".$ar['money'].",`available_funds` = available_funds+".$ar['money'].",`stay_interest` = stay_interest-".$co['interest'].",`make_interest` = make_interest+".$ar['interest'].",`due_in` = due_in-".$co['money']." WHERE `uid` =".$co['uid']);	//平台代付
				//投资者
				$total=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$co['uid'])->find();	//查询资金
				//记录添加点
				$moneyLog=$this->moneyLog(array(0,'对【'.$co['title'].'】的逾期还款',$ar['money'],$this->_session('user_name'),$total['total_money'],$total['available_funds'],$total['freeze_funds'],$co['uid']));//资金记录		
				$msgTools->sendMsg(3,'对【'.$co['title'].'】的逾期还款','对<a href="'.__ROOT__.'/Loan/invest/'.$co['bid'].'.html">【'.$co['title'].'】</a>的逾期还款，账户增加：'.$ar['money'].' 元','admin',$co['uid']);//站内信
			}
			//借款者
			$sumref=$refund->where('type=2 and bid='.$bid)->sum('money');	//统计待还总额
			$models->query("UPDATE `ds_money` SET `total_money` = total_money-".$voerd_total.",`available_funds` = available_funds-".$voerd_total.",`stay_still` = stay_still-".$sumref." WHERE `uid` =".$this->_session('user_uid'));	//还款
			$total=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$this->_session('user_uid'))->find();	//查询资金
			//记录添加点
			$moneyLog=$this->moneyLog(array(0,'对【'.$overd['title'].'】的逾期还款',$overd['money'],'投资者',($total['total_money']+$overd_int+$penalty_int),($total['available_funds']+$overd_int+$penalty_int),$total['freeze_funds']));//资金记录		
			$msgTools->sendMsg(3,'对【'.$overd['title'].'】的逾期还款','对<a href="'.__ROOT__.'/Loan/invest/'.$overd['uid'].'.html">【'.$overd['title'].'】</a>的逾期还款，账户减少：'.$voerd_total.' 元','admin',$this->_session('user_uid'));//站内信
			//逾期管理费扣除
			$moneyLog=$this->moneyLog(array(0,'对【'.$overd['title'].'】的逾期还款扣除管理费用',$overd_int,'平台',($total['total_money']+$penalty_int),($total['available_funds']+$penalty_int),$total['freeze_funds']));//资金记录		
			$msgTools->sendMsg(3,'【'.$overd['title'].'】的逾期还款扣除管理费用','【'.$overd['title'].'】的逾期还款扣除管理费用'.$overd_int.' 元','admin',$this->_session('user_uid'));//站内信
			$moneyLog=$this->moneyLog(array(0,'对【'.$overd['title'].'】的逾期还款扣除罚息费用',$penalty_int,'投资者',($total['total_money']),($total['available_funds']),$total['freeze_funds']));//资金记录		
			$msgTools->sendMsg(3,'【'.$overd['title'].'】的逾期还款扣除罚息费用','【'.$overd['title'].'】的逾期还款扣除罚息费用'.$penalty_int.' 元','admin',$this->_session('user_uid'));//站内信
			$overdue->where('uid='.$this->_session('user_uid'))->setField('type',1);	//更新逾期状态改为已还
		}else{
			$arr['total_money']=$mon['total_money']-$voerd_total;
			$arr['available_funds']=$mon['available_funds']-$voerd_total;
			$arr['stay_still']=$mon['stay_still']-$overd['money'];
			$money->where('uid='.$this->_session('user_uid'))->save($arr);	//扣除借款金额
			$overdue->where('uid='.$this->_session('user_uid'))->setField('type',1);	//更新逾期状态
			$total=$money->field('total_money,available_funds,freeze_funds')->where('uid='.$this->_session('user_uid'))->find();	//查询资金
			//记录添加点
			$moneyLog=$this->moneyLog(array(0,'对【'.$overd['title'].'】的逾期还款',$overd['money'],'平台',($total['total_money']+$overd_int+$penalty_int),($total['available_funds']+$overd_int+$penalty_int),$total['freeze_funds']));//资金记录		
			$msgTools->sendMsg(3,'对【'.$overd['title'].'】的逾期还款','对<a href="'.__ROOT__.'/Loan/invest/'.$overd['uid'].'.html">【'.$overd['title'].'】</a>的逾期还款，账户减少：'.$voerd_total.' 元','admin',$this->_session('user_uid'));//站内信
			//逾期管理费扣除
			$moneyLog=$this->moneyLog(array(0,'对【'.$overd['title'].'】的逾期还款扣除管理费用',$overd_int,'平台',($total['total_money']+$penalty_int),($total['available_funds']+$penalty_int),$total['freeze_funds']));//资金记录		
			$msgTools->sendMsg(3,'【'.$overd['title'].'】的逾期还款扣除管理费用','【'.$overd['title'].'】的逾期还款扣除管理费用'.$overd_int.' 元','admin',$this->_session('user_uid'));//站内信
			$moneyLog=$this->moneyLog(array(0,'对【'.$overd['title'].'】的逾期还款扣除罚息费用',$penalty_int,'平台',($total['total_money']),($total['available_funds']),$total['freeze_funds']));//资金记录		
			$msgTools->sendMsg(3,'【'.$overd['title'].'】的逾期还款扣除罚息费用','【'.$overd['title'].'】的逾期还款扣除罚息费用'.$penalty_int.' 元','admin',$this->_session('user_uid'));//站内信
		}
		$borrows->where(array('id'=>$bid))->save(array('state'=>9));	//将标状态改变还款成功
		$this->success('还款成功','__URL__/loan/overdue');
	}
	
   /**
	*
	* @秒还标满标时
	* @borr		借款标信息
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function secondPayment($borr){
		$msgTools = A('msg','Event');
        $bid_record=$this->specifyUser('3',$borr['id']);
		$models = new Model();
		$money=M('money');
		foreach($bid_record as $k => $brd){	
			$interestReward=$brd['interest']+$brd['operation_reward'];	//利息+奖励
			$array['type']				=9;
			$array['uid']				= $brd['id'];
			$array['bid']				=$borr['id'];
			$array['instructions']		=$borr['username'].'对<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>借款标的还款';
			$moneyarr['total_money']	=$array['total']				=$brd['money']['total_money']+$interestReward;
			$moneyarr['available_funds']=$array['available']			=$brd['money']['available_funds']+$brd['total']+$interestReward;
			$moneyarr['freeze_funds']	=$array['freeze']				=$brd['money']['freeze_funds']-$brd['total'];
			$array['operation']			=$brd['total']+$interestReward;
			$moneyarr['make_reward']	=$array['make_reward']			=$brd['money']['make_reward']+$brd['operation_reward'];
			$moneyarr['make_interest']	=$array['make_interest']		=$brd['money']['make_interest']+$brd['interest'];
			$borrowlog=R('Comm/borrowLog',array($array));	
			$money=$models->table('ds_money')->where('uid='.$brd['id'])->save($moneyarr);//投资者资金操作
			//记录添加点
			$moneyLog=R('Comm/moneyLog',array(array(0,'【'.$borr['title'].'】的收款',$array['operation'],$borr['username'],$moneyarr['total_money'],$moneyarr['available_funds'],$moneyarr['freeze_funds'],$brd['id'])));//资金记录
			$sendMsg=$msgTools->sendMsg(3,'【'.$borr['title'].'】的收款','<a href="'.__ROOT__.'/Loan/invest/'.$borr['id'].'.html">【'.$borr['title'].'】</a>的收款','admin',$brd['id']);//站内信
			unset($array);
			unset($moneyarr);
			unset($bid_record);
		}
		//借款者操作
		//$secondsIncidentals=$this->secondsIncidentals($borr);	//发标冻结资金
		$bid_record=$this->specifyUser('4',$borr['id']);
		$bid_record=reset($bid_record);
		$interestReward=$bid_record['interest']+$bid_record['operation_reward'];	//利息+奖励
		$array['type']				=10;
		$array['uid']				= $borr['uid'];
		$array['bid']				=$borr['id'];
		$array['instructions']		='<a href="'.$_SERVER['HTTP_REFERER'].'">【'.$borr['title'].'】</a>投标已完成，等待复审';
		$moneyarr['total_money']	=$array['total']=$bid_record['money']['total_money']-$interestReward;
		$moneyarr['available_funds']=$array['available']=$bid_record['money']['available_funds']-$interestReward;
		$moneyarr['freeze_funds']	=$array['freeze']				=$bid_record['money']['freeze_funds'];
		$array['operation']			=$interestReward;
		$array['has_also']			=$brd['total']+$interestReward;
		$array['stay_also']			=$brd['total']+$interestReward;
		$borrowlogs=R('Comm/borrowLog',array($array));	
		$moneys=$models->table('ds_money')->where('uid='.$bid_record['id'])->save($moneyarr);//借款者资金操作
		//记录添加点
		$moneyLog=R('Comm/moneyLog',array(array(0,'对【'.$borr['title'].'】的还款',$array['operation'],'投资者',$moneyarr['total_money'],$moneyarr['available_funds'],$moneyarr['freeze_funds'],$borr['uid'])));//资金记录
		$sendMsg=$msgTools->sendMsg(3,'对【'.$borr['title'].'】的还款','<a href="'.__ROOT__.'/Loan/invest/'.$borr['id'].'.html">对【'.$borr['title'].'】</a>的还款','admin',$borr['uid']);//站内信
		if($borrowlog && $money && $borrowlogs && $moneys){	//操作全成功返回1
			return 1;
		}
    }
	
	/**
	*积分商城显示
	*order			//排序
	*id				//id
	*l				//显示数量
	*show			//是否显示数量
	*w				//条件
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	public function integralLest($order,$id,$l,$show,$w){
		$orde=$order?$order:'`id` asc';
		$integral=M('integral');
		if($id>0){	//单条
			$list=reset($integral->order($orde)->where('state=0 and id='.$id)->limit($l)->select());
			$img=array_filter(explode(',',$list['img']));
			$list['surplus']=$list['number']-$list['convert'];	//剩余数量
			$list['imges']=$img;	//图片
			
		}else{
			$list=$integral->where($w.'state=0')->order($orde)->limit($l)->select();
			foreach($list as $id=>$lt){
				$img=array_filter(explode(',',$lt['img']));
				$list[$id]['master']=$img[0];
				$list[$id]['surplus']=$lt['number']-$lt['convert'];	//剩余数量
				$total+=1;
				if($lt['kind']==1){
					$members+=1;
				}else if($lt['kind']==2){
					$vip+=1;
				}else{
					$promote+=1;
				}
			}
			$list['total']=$total?$total:'0';	//总数
			$list['members']=$members?$members:'0';	//会员商品数
			$list['vip']=$vip?$vip:'0';	//VIP商品数
			$list['promote']=$promote?$promote:'0';	//推广商品数
		}
		
		if($show < 1){	
			unset($list['total']);
			unset($list['members']);
			unset($list['vip']);
			unset($list['promote']);
		}
		
		return $list;
	}
	
	/**
	*
	* @excel列转换
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*
	*/
	function letter() {
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
	function excelExport($array) {
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
	function statistical(){
		$borrowing=M('borrowing');
		$userinfo=M('userinfo');
		$lines=M('lines');
		$recharge=M('recharge');
		$withdrawal=M('withdrawal');
		$forrecord=M('forrecord');
		$vip_points=M('vip_points');
		$user=M('user');
		$money=M('money');
		$array['stay']=$borrowing->where('state=0')->count();	//待审核数
		$array['assure']=$lines->where('state=1')->count();	//额度申请
		$array['full']=$borrowing->where('state=5')->count();	//满标待审核数
		$array['autonym']=$userinfo->where('certification=1')->count();	//实名认证
		$array['video']=$userinfo->where('video_audit=1')->count();	//视频认证
		$array['scene']=$userinfo->where('site_audit=1')->count();	//现场认证
		$array['phone']=$userinfo->where('cellphone_audit=1')->count();	//手机认证
		$array['recharge']=$recharge->where('type=1')->count();	//充值申请
		$array['withdrawal']=$withdrawal->where('type=1')->count();	//提现申请
		$array['vip']=$vip_points->where('audit=1')->count();	//VIP申请
		$array['momentum']=$forrecord->where('type=1')->count();	//待发货
		//总
		$array['metotal']=$user->count();	//会员总数
		$array['mototals']=$money->sum('total_money');	//平台总资金
		$array['mototal']=number_format($array['mototals'],2,'.',',');
		$array['frtotal']=$money->sum('freeze_funds');	//冻结总资金
		$array['frtotal']=number_format($array['frtotal'],2,'.',',');
		$array['dutotal']=$money->sum('due_in');	//待收总资金
		$array['dutotal']=number_format($array['dutotal'],2,'.',',');
		$array['sttotal']=$money->sum('stay_still');	//待还总资金
		$array['sttotal']=number_format($array['sttotal'],2,'.',',');
		$array['ovtotal']=$money->sum('overdue');	//逾期总资金
		$array['ovtotal']=number_format($array['ovtotal'],2,'.',',');
		$array['wmototals']=$withdrawal->where('type=2')->sum('money');	//提现总资金
		$array['wmototal']=number_format($array['wmototals'],2,'.',',');
		$array['rmototals']=$recharge->where('type=2')->sum('money');	//充值总资金
		$array['rmototal']=number_format($array['rmototals'],2,'.',',');
		$array['gprofit']=number_format($array['rmototals']-$array['mototals']-$array['wmototals'],2,'.',',');	//平台总利润
		//今天
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$where='time >='.$beginToday.' and time <='.$endToday;
		$wheres='audittime >='.$beginToday.' and audittime <='.$endToday;
		$array['menow']=$user->where($where)->count();	//会员数
		$array['wmonow']=$withdrawal->where('type=2 and '.$wheres)->sum('money');	//提现总资金
		$array['wmonow']=number_format($array['wmonow'],2,'.',',');
		$array['rmonow']=$recharge->where('type=2 and '.$wheres)->sum('money');	//充值总资金
		$array['rmonow']=number_format($array['rmonow'],2,'.',',');		
		//本周
		$beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
		$endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));
		$where='time >='.$beginLastweek.' and time <='.$endLastweek;
		$wheres='audittime >='.$beginLastweek.' and audittime <='.$endLastweek;
		$array['meweeks']=$user->where($where)->count();	//会员数
		$array['wmoweeks']=$withdrawal->where('type=2 and '.$wheres)->sum('money');	//提现总资金
		$array['wmoweeks']=number_format($array['wmoweeks'],2,'.',',');
		$array['rmoweeks']=$recharge->where('type=2 and '.$wheres)->sum('money');	//充值总资金
		$array['rmoweeks']=number_format($array['rmoweeks'],2,'.',',');		
		//本月
		$beginThismonth=mktime(0,0,0,date('m'),1,date('Y')); 
		$endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
		$where='time >='.$beginThismonth.' and time <='.$endThismonth;
		$wheres='audittime >='.$beginThismonth.' and audittime <='.$endThismonth;
		$array['memonth']=$user->where($where)->count();	//会员数
		$array['wmomonth']=$withdrawal->where('type=2 and '.$wheres)->sum('money');	//提现总资金
		$array['wmomonth']=number_format($array['wmomonth'],2,'.',',');
		$array['rmomonth']=$recharge->where('type=2 and '.$wheres)->sum('money');	//充值总资金
		$array['rmomonth']=number_format($array['rmomonth'],2,'.',',');		
		return $array;
	}
	
	/**
	 * @查看头像是否存在
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	function headPortrait($img){
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
	function someArticle($id,$limt){
		$mod = D("Article");
		$list = $mod->field('id,title,addtime')->where("published=1 and catid=".$id)->order('`order` desc,`addtime` desc')->limit($limt)->select();
		return $list;
	}
	
	/**
	 * @发标基础验证
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	function bidValidation(){
		$money=M('money');
		$userinfo=M('userinfo');
		$mone=$money->field('total_money')->where('uid='.$this->_session('user_uid'))->find();
		$userin=$userinfo->field('certification,email_audit,location')->where('uid='.$this->_session('user_uid'))->find();
		if($mone['total_money']<=0){	//资金必须大于0
			$this->error("账户金额必须大于0！",'__ROOT__/Center/fund/inject.html');
		}	
		if($userin['certification']<2){	//实名
			$this->error("必须通过实名认证才可发布！",'__ROOT__/Center/approve/autonym.html');
		}
		if($userin['email_audit']<2){	//邮箱
			$this->error("必须通过邮箱认证才可发布！",'__ROOT__/Center/approve/email.html');
		}
		
		if(!$userin['location']){	//个人资料
			$this->error("请先完善个人资料！",'__ROOT__/Center/basic/personal_data.html');
		}
	}
	/**
	 * @版权管理
	 * @请不要做修改或删除，因多处调用此方法，如因自行修改造成的资金错误、软件不能正常使用后果自行承担
	 * @作者		shop猫
	 * @版权		宁波天发网络
	 * @官网		http://www.tifaweb.com http://www.dswjcms.com
	 */
	function copyright($tf=0){
		if($tf){
			$systems=$this->systems();
			$curlPost = "dswjw=".$_SERVER['SERVER_NAME']."&dswjn=".DS_NUMbER."&dswji=".$_SERVER["REMOTE_ADDR"]."&dswje=".$systems['sys_email']."&dswjc=".$systems['sys_cellphone']."&dswjp=".$systems['sys_phone']."&dswja=".$systems['sys_address']."&dswjco=".$systems['sys_company'];
			$url='http://www.tifaweb.com/Api/Core/counter';  
			$in=$this->Curl($curlPost,$url);
			if($in['state']=='yes'){
				echo "已授权";
			}else{
				echo "未授权 授权免费，地址：http://www.tifaweb.com/Index/counter.html";
			}
		}
	}
	
	/**
	 * @短信发送
	 * @number		号码
	 * @content		内容
	 * @作者			shop猫
	 * @版权			宁波天发网络
	 * @官网			http://www.tifaweb.com http://www.dswjcms.com
	 */
	function textMessaging($number,$content){
		$data=array(
            'userid'=>'883ww31',
            'password'=>'dy8w8331',
            'destnumbers'=>$number,
            'msg'=>$content
        );
		$query=http_build_query($data);
		$query='http://211.147.244.114:9801/CASServer/SmsAPI/SendMessage.jsp?'.$query;
		$ret = @file_get_contents($query);
		print_r($query);
		exit;
	}
	
	/**
	*
	* @curl数据传输
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	* @curlPost	传输数据
	* @url		传输地址
	*/
	function Curl($curlPost,$url){
		//$curlPost = "user=$username&pass=$password";
		//$url='http://xp.dswjjd.cn/index.php/Api/Index/login';  
		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_POST, 1);  
		curl_setopt($ch, CURLOPT_URL,$url);  
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);  
		ob_start();  
		curl_exec($ch);  
		$json = ob_get_contents() ;  
		ob_end_clean();
		$login=json_decode($json,true);	
		return $login;
	}
	
	/**
	*
	* @获取投资者逾期
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	* @uid		单用户
	*/
	function overdue($uid){
		$coverdue=D('Coverdue');
		$cover=$coverdue->relation(true)->where('uid='.$uid)->select();
		foreach($cover as $id=>$c){
			$cover[$id]['overdue']=$this->overdueInterest($c['money'],$c['days']);	//逾期管理管理费
			$cover[$id]['penalty']=$this->penaltyInterest($c['money'],$c['days']);	//逾期罚息
		}
		return $cover;
	}
	
	/**
	*
	* @获取借款者逾期
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	* @uid		单用户
	*/
	function verdue($uid){
		$coverdue=D('Overdue');
		if($uid){
			$cover=$coverdue->relation(true)->where('uid='.$uid)->select();
		}else{
			$cover=$coverdue->relation(true)->select();
		}
		foreach($cover as $id=>$c){
			$cover[$id]['overdue']=$this->overdueInterest($c['money'],$c['days']);	//逾期管理管理费
			$cover[$id]['penalty']=$this->penaltyInterest($c['money'],$c['days']);	//逾期罚息
		}
		return $cover;
	}
	
	/**
	*
	* @数据库自动备份
	* @作者		shop猫
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	function automaticBackup(){
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
	function directory($dirname){
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
	function templateData($dirname){
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
}
?>