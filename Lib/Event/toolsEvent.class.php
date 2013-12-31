<?php
/*
*Author :xq
*use :小工具，常用操作
*/
class toolsEvent extends Action{
	
	function aa(){
		$arr = array(1,2,3,4,5,6,7,8);
		per($arr);
	}
	
	//提成，资金
	function bonus($funds,$id=0){
		
		if(!$id){
			return array("status"=>0,"info"=>"请提供ID");
		}	
		if(!$funds){
			return array("status"=>0,"info"=>"请提供要分配的资金");
		}
		
		$mod = new  Model();
		$field = " a.id , a.uid,a.username,c.id as gid,c.catpid as gcatpid , c.name";
		$sql = "select ".$field."  from ds_user as a  right  join ds_user_commision as b on a.uid = b.uid  right join ds_commision as c on  b.group_id = c.id where a.id=".$id." limit 1";

		$list = $mod->query($sql);
		
		 $ret = array('source_id'=>$list[0]['id'],'source_name'=>$list[0]['username']);		
		 $ret['funds'] = $funds;
		 $ret['list'] = array();
		if(!$list){
			return array("info"=>"没有分成");
		}
       	$in = explode('-',$list[0]['gcatpid']);
		$in = implode(",",$in);
		$field = " a.bonus,a.level,a.ratio,a.id as gid ,b.uid ,c.username";
		$sql = "select ".$field."  from ds_commision as a  left  join ds_user_commision as b on a.id = b.group_id  left join ds_user as c on  b.uid = c.id ";	
		$sql .="where a.id in(".$in.") order by a.level";	
        $list = $mod->query($sql);
        foreach($list as $k=>$v){
			if(intval($v['level']) == 1){
				$ret['webBonus'] =$v['bonus'];
				$ret['webProfit'] = intval($funds) * floatval($v['bonus']);
			}else{
				$ret['list'][$k] = $v;
				$ret['list'][$k]['profit'] = ($funds - $ret['webProfit']) *floatval($v['bonus']);
			}
		}
		return $ret;
	}
	
	
	
	
}
?>