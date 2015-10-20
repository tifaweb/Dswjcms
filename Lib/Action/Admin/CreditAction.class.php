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
class CreditAction extends AdminCommAction {
//--------征信-----------
    public function index(){
		
		if($this->_get('name')){
			$json=$this->reportingQueries($this->_get('name'));
		}else if($this->_get('idcard')){
			$json=$this->reportingQueries($this->_get('idcard'),1);
		}
		if($json){
			if($json['state']==88){
				$citys=$this->city();
				$linkageValue=$this->linkageValue(13);
				$con.='
				<table class="table">
                    <thead>
                      <tr>
                        <th>征信结果</th>
                      </tr>
                    </thead>';
				foreach($json['record'] as $id=>$j){
				$native_place=explode(" ",$j['native_place']);
				$native_place=$citys[$native_place[0]]." ".$citys[$native_place[1]]." ".$citys[$native_place[2]];
				$con.='
					
                    <tbody>   
						<tr>
                            <td class="red">数据'.$id.'</td>
                            <td></td>
                            <td></td>
                         </tr>             
                        <tr>
                            <td>真实姓名：'.$j['name'].'</td>
                            <td>身份证：'.$j['idcard'].'</td>
                            <td>籍贯：'.$native_place.'</td>
                         </tr>
                         
                         <tr>
                            <td>性别：';
				$con.=$j['gender']>0?'女':'男';
				$con.='</td>
                            <td>民族：'.$linkageValue[$j['national']].'</td>
                            <td>出生日期：'.date('Y年m月d日',$j['born']).'</td>
                         </tr>
						 <tr>
                            <td>内容提供：'.$j['information']['website'].'</td>
                            <td>平台官网：'.$j['information']['url'].'</td>
							<td></td>
                         </tr>
                    </tbody>
                    
				';
				unset($native_place);
				}
				$con.='</table>';
			}else{
				$con="<div>".$json['prompt']."</div>";
				
			}
		}
		$this->assign('con',$con);
		$this->display();
	}
	
	/**
	*
	* @征信查询
	* @name		输入的内容
	* @type		0为姓名1为身份证
	* @作者		purl
	* @版权		宁波天发网络
	* @官网		http://www.tifaweb.com http://www.dswjcms.com
	*/
	public function reportingQueries($name='',$type=0){
		//密钥验证,以下内容不可修改，修改后将无法使用征信功能
		$md5json=MD5($name);
		$curlPost['md5json']=$md5json;	//加密后的信息
		$curlPost['json']=$name;	
		$curlPost['type']=$type;	
		$url=C('DS_CREDIT_URL').'dsReportingQueries';  
		$in=$this->Curl($curlPost,$url);
		
		if($in['state']==88){
			
			if($in['md5json']==MD5($in['value'])){
				$val=json_decode($in['value'], true);
				foreach($val as $id=>$v){
					$encryption=$this->encryption($v,1);//解密
					$value['record'][$id]=json_decode($encryption, true);
					unset($encryption);
				}
				$value['state']=88;
			}else{
				$value['state']=1;
				$value['prompt']="接口地址错误";
			}
		}else if($in['state']==2){
			$value['state']=2;
			$value['prompt']="无此人信息";
		}else{
			$value['state']=1;
			$value['prompt']="验证出错";
			
		}
		return $value;
	}
}
?>