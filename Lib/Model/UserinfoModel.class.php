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
class UserinfoModel extends RelationModel{
	protected $_validate = array(
		array('certification','require','状态有误！',0),
		array('name','/^[\x{4e00}-\x{9fa5}]+$/u','真实姓名有误！'),
		array('gender','require','性别有误！'),
		array('national','require','民族有误！'),
		array('born','require','出生日期有误！'),
		array('idcard','checkIdCard','身份证错误!',0,'callback'),
		array('native_place[]','require','籍贯有误！'),
		array('idcard_img[]','require','身份证必须上传！'),
		array('location[]','require','所在地址有误！',0),
		array('marriage','require','婚姻状况有误！',0),
		array('education','require','学历有误！',0),
		array('monthly_income','require','月收入有误！',0),
		array('housing','require','住房条件有误！',0),
		array('buy_cars','require','购车情况有误！',0),
		array('qq','/^\d+$/','QQ有误！',2),
		array('fixed_line','/^(\d{3}-|\d{4}-)?(\d{8}|\d{7})?$/','固定电话有误！',2),
		array('cellphone','number','手机有误！',2),
		array('industry','require','当前所在行业有误！',0),
		array('company','/^[a-zA-Z\x{4e00}-\x{9fa5}]+$/u','公司有误！',2),
		array('email','email','邮箱有误！'),
		array('video_audit','number','参数有误！'),
		array('site_audit','number','参数有误！'),
		array('bank','number','参数有误！'),
		array('bank_name','/^[\w\x{4e00}-\x{9fa5}]+$/u','开户支行名称有误！'),
		array('site_audit','number','银行账号有误！'),
		
	);
	
	protected $_auto = array ( 
		array('born','conversion',3,'callback',1), 
		array('native_place','nativePlace',3,'callback',1),
		array('location','nativePlace',3,'callback',1),  
		array('idcard_img','idCard',3,'callback',1),  
	);
	
	protected function conversion($born){
			return strtotime($born);
	}
	
	protected function nativePlace($nativePlace){
			return implode(" ",$nativePlace);
	}

	protected function idCard($nativePlace){
			return implode(",",$nativePlace);
	}

	/*
	* 身份证验证
	* $idcard			//需要验证的值
	*/
	public function checkIdCard($idcard){
		if(empty($idcard)){
			return false;
		}
		$City = array(11=>"北京",12=>"天津",13=>"河北",14=>"山西",15=>"内蒙古",21=>"辽宁",22=>"吉林",23=>"黑龙江",31=>"上海",32=>"江苏",33=>"浙江",34=>"安徽",35=>"福建",36=>"江西",37=>"山东",41=>"河南",42=>"湖北",43=>"湖南",44=>"广东",45=>"广西",46=>"海南",50=>"重庆",51=>"四川",52=>"贵州",53=>"云南",54=>"西藏",61=>"陕西",62=>"甘肃",63=>"青海",64=>"宁夏",65=>"新疆",71=>"台湾",81=>"香港",82=>"澳门",91=>"国外");
		$iSum = 0;
		$idCardLength = strlen($idcard);
		//长度验证
		if(!preg_match('/^\d{17}(\d|x)$/i',$idcard) and!preg_match('/^\d{15}$/i',$idcard))
		{
			return false;
		}
		//地区验证
		if(!array_key_exists(intval(substr($idcard,0,2)),$City))
		{
		   return false;
		}
		// 15位身份证验证生日，转换为18位
		if ($idCardLength == 15)
		{
			$sBirthday = '19'.substr($idcard,6,2).'-'.substr($idcard,8,2).'-'.substr($idcard,10,2);
			$d = new DateTime($sBirthday);
			$dd = $d->format('Y-m-d');
			if($sBirthday != $dd)
			{
				return false;
			}
			$idcard = substr($idcard,0,6)."19".substr($idcard,6,9);//15to18
			$Bit18 = getVerifyBit($idcard);//算出第18位校验码
			$idcard = $idcard.$Bit18;
		}
		// 判断是否大于2078年，小于1900年
		$year = substr($idcard,6,4);
		if ($year<1900 || $year>2078 )
		{
			return false;
		}

		//18位身份证处理
		$sBirthday = substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
		$d = new DateTime($sBirthday);
		$dd = $d->format('Y-m-d');
		if($sBirthday != $dd)
		 {
			return false;
		 }
		//身份证编码规范验证
		$idcard_base = substr($idcard,0,17);
		if(strtoupper(substr($idcard,17,1)) != $this->getVerifyBit($idcard_base))
		{
			return false;
		}else{
			return true;
		}
	}

	// 计算身份证校验码，根据国家标准GB 11643-1999
	public function getVerifyBit($idcard_base)
	{
		if(strlen($idcard_base) != 17)
		{
			return false;
		}
		//加权因子
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		//校验码对应值
		$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4','3', '2');
		$checksum = 0;
		for ($i = 0; $i < strlen($idcard_base); $i++)
		{
			$checksum += substr($idcard_base, $i, 1) * $factor[$i];
		}
		$mod = $checksum % 11;
		$verify_number = $verify_number_list[$mod];
		return $verify_number;
	}
	protected $_link=array(
		'user'=> array(  
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'user',
            'foreign_key'=>'uid',
            'mapping_name'=>'user',
			'mapping_fields'=>'username,time',
			'as_fields'=>'username:username,time:join_date',
		),
	);
}
?>