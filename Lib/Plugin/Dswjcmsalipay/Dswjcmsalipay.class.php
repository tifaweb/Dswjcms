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
import('@.Plugin.Basis');
class Dswjcmsalipay extends Basis {
	/**
     * 构造方法
     */
    public function __construct(){
		$this->pluginname='Dswjcmsalipay';	//插件名，必须和插件目录名相同
    }
	//插件安装
	public function install(){
		$add['name']='支付宝';
		$add['logo']='alipay.gif';
		$add['introduce']='支付宝标准双接口';
		$arr['version']='0.0.1';	//当前版本
		$arr['type']=1;		//插件安装状态
		
		
		$this->Dsdata($arr,$this->pluginname,1);//配置检测
		$this->Dsrenames('./Lib/Plugin/Dswjcmsalipay/alipay.gif','./Public/uploadify/uploads/online/alipay.gif');	//移动图片
		
		$online=M('online')->add($add);
		$unite=M('unite')->where('`pid`=15')->order('`value` DESC')->find('value');
		$unite=M('unite')->add(array('pid'=>15,'name'=>'支付宝','value'=>($unite+1)));
		$arr['data']=array(		//配置参数
					'online'=>$online,	//线上表插入的ID
					'unite'=>$unite,	//联动表插入的ID
					);
		$arr['allocationtype']=1;//配置类型1为URL2为配置参数
		$arr['allocation']['url']='/Basis/editonl/id/'.$online.'.html';//URL
		/*
		//配置类型为2时
		$arr['allocationtype']=2;//配置类型1为URL2为配置参数
		$arr['allocation']['checking']['value']='';
		$arr['allocation']['checking']['name']='密钥';
		$arr['allocation']['account']['value']='';
		$arr['allocation']['account']['name']='收款账号';
		$arr['allocation']['pid']['value']='';
		$arr['allocation']['pid']['name']='合作者ID';*/
		$this->Dsdata($arr,$this->pluginname);//配置添加
		//控制器生成
		$replace='
		if($this->_post(\'onid\')=='.$online.'){
			echo "<script>window.location.href=\'".__ROOT__."/Center/alipayapi?price=".$this->_post(\'money\')."\';</script>";
			exit;
		}
		';
		$this->DsreadFile('./Lib/Action/Home/CenterAction.class.php','/\/\/网上充值/','//网上充值','',$replace,$this->pluginname);
		$replace='
	//支付宝跳转页
	public function alipayapi($price){
		header("Content-Type:text/html; charset=utf-8");
		import(\'@.Plugin.Dswjcmsalipay.Alipay.Submit\');
		$online=M(\'online\');
		$list=$online->where(\'`id`='.$online.'\')->find();
		$alipay_config[\'partner\']		= $list[\'pid\'];
		$alipay_config[\'key\']			= $list[\'checking\'];
		$alipay_config[\'sign_type\']    = strtoupper(\'MD5\');//签名方式 不需修改
		$alipay_config[\'input_charset\']= strtolower(\'utf-8\');//字符编码格式 目前支持 gbk 或 utf-8
		$alipay_config[\'transport\']    = \'http\';//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $payment_type = "1";//支付类型
        $notify_url = "http://".$_SERVER[\'HTTP_HOST\']."/Center/notify";//服务器异步通知页面路径 
        $return_url = "http://".$_SERVER[\'HTTP_HOST\']."/Center/alipayreturn";//页面跳转同步通知页面路径
        $seller_email = $list[\'account\'];//卖家支付宝帐户
        $out_trade_no = $this->orderNumber();//商户订单号
        $subject = \'支付宝\';//订单名称
        $quantity = "1";//商品数量
        $logistics_fee = "0.00";//物流费用
        $logistics_type = "EXPRESS";//物流类型
        $logistics_payment = "SELLER_PAY";//物流支付方式
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "trade_create_by_buyer",
				"partner" => trim($alipay_config[\'partner\']),
				"payment_type"	=> $payment_type,
				"notify_url"	=> $notify_url,
				"return_url"	=> $return_url,
				"seller_email"	=> $seller_email,
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"price"	=> $price,
				"quantity"	=> $quantity,
				"logistics_fee"	=> $logistics_fee,
				"logistics_type"	=> $logistics_type,
				"logistics_payment"	=> $logistics_payment,
				"body"	=> $body,
				"show_url"	=> $show_url,
				"receive_name"	=> $receive_name,
				"receive_address"	=> $receive_address,
				"receive_zip"	=> $receive_zip,
				"receive_phone"	=> $receive_phone,
				"receive_mobile"	=> $receive_mobile,
				"_input_charset"	=> trim(strtolower($alipay_config[\'input_charset\']))
		);
		//插入数据
		$recharge=M(\'recharge\');
		$poundage=$this->onlineUpFees($price);//充值手续费
		$amount=$price-$poundage;	//到账金额
		$add=$recharge->add(array(\'uid\'=>$this->_session(\'user_uid\'),\'genre\'=>1,\'nid\'=>$out_trade_no,\'money\'=>$price,\'time\'=>time(),\'type\'=>1,\'account_money\'=>$amount,\'poundage\'=>$poundage));	//插入数据库
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		echo $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
	}
	
	
	//支付宝跳转同步通知
	public function alipayreturn(){
		header("Content-Type:text/html; charset=utf-8");
		import(\'@.Plugin.Dswjcmsalipay.Alipay.Notify\');
		$online=M(\'online\');
		$list=$online->where(\'`id`='.$online.'\')->find();
		$alipay_config[\'partner\']		= $list[\'pid\'];
		$alipay_config[\'key\']			= $list[\'checking\'];
		$alipay_config[\'sign_type\']    = strtoupper(\'MD5\');//签名方式 不需修改
		$alipay_config[\'input_charset\']= strtolower(\'utf-8\');//字符编码格式 目前支持 gbk 或 utf-8
		$alipay_config[\'transport\']    = \'http\';//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		//获取充值
		$recharge=M(\'recharge\');
		$rechar=$recharge->where(\'nid=\'.$this->_get(\'out_trade_no\'))->find();
		if($verify_result) {//验证成功
			$recharge->where(\'nid=\'.$this->_get(\'out_trade_no\'))->save(array(\'type\'=>2,\'audittime\'=>time(),\'date\'=>json_encode($_GET),\'handlers\'=>\'第三方支付\'));
			//获取用户资金
			$money=M(\'money\');
			$mon=$money->field(\'total_money,available_funds,freeze_funds\')->where(array(\'uid\'=>$rechar[\'uid\']))->find();
			$array[\'total_money\']				=$mon[\'total_money\']+$rechar[\'account_money\'];
			$array[\'available_funds\']			=$mon[\'available_funds\']+$rechar[\'account_money\'];	
			//记录添加点
			$money->where(array(\'uid\'=>$rechar[\'uid\']))->save($array);
			$this->silSingle(array(\'title\'=>\'充值成功\',\'sid\'=>$rechar[\'uid\'],\'msg\'=>\'充值成功，帐户增加\'.$rechar[\'account_money\'].\'元\'));//站内信
			$this->moneyLog(array(0,\'充值成功\',$rechar[\'money\'],\'平台\',$array[\'total_money\']+$rechar[\'poundage\'],$array[\'available_funds\']+$rechar[\'poundage\'],$mon[\'freeze_funds\'],$rechar[\'uid\']));	//资金记录
			$this->moneyLog(array(0,\'充值手续费扣除\',$rechar[\'poundage\'],\'平台\',$array[\'total_money\'],$array[\'available_funds\'],$mon[\'freeze_funds\'],$rechar[\'uid\']));	//资金记录
			$this->userLog(\'充值成功\');//会员操作
			$this->success(\'充值成功\',\'__ROOT__/Center/fund/injectrecord.html\');
		}else{
			$recharge->where(\'nid=\'.$billno)->save(array(\'type\'=>3,\'audittime\'=>time(),\'date\'=>json_encode($_GET),\'handlers\'=>\'第三方支付\'));	//充值失败
			//记录添加点
			$this->error(\'充值失败!\',\'__ROOT__/Center/fund/injectrecord.html\');
		}
	}	
		';
		$this->DsreadFile('./Lib/Action/Home/CenterAction.class.php','/\/\/协议书/','','//协议书',$replace,$this->pluginname);
		$this->Dssucceed('插件安装成功');
	}
	
	//插件删除
	public function delete(){
		$this->DsdeleteFile('./Lib/Action/Home/CenterAction.class.php',$this->pluginname);	//删除代码
		$this->DsdeleteDatabase($this->pluginname);	//删除数据库
		$this->Dsrenames('./Public/uploadify/uploads/online/alipay.gif','./Lib/Plugin/Dswjcmsalipay/alipay.gif');	//移动图片
		$this->Dsdeletedata($this->pluginname);
		$this->Dssucceed('插件已成功卸载');
	}
	
	//插件更新
	public function renewal(){
		
	}
	
	
}