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
class Tree{
    public $tree = array();
    public $newtree = array();
	public $pidtree = array();
    public $i = 1;
    public $l = 0;
    public $prestr = "----";
    public $outer = "{#id#}{#url#}{#name#}";	//无子类标签
	public $childouter = "{#id#}{#url#}{#name#}";	//有子类的标签
	public $formatstr = "{#url#}{#name#}";	//子类标签
    public function __construct($tree){
        if (is_array($tree)){
            $this->tree = $tree;
        }else{
            return false;
        }
    }

    public function getchild($arr,$pid = 0){
        $c = $v = array();
        if(is_array($arr)){
            foreach($arr as $k => $v){
                if($v['pid'] == $pid){
                    $c[$k] = $v;
                }
            }
        }
		
        return $c?$c:false;

    }
 
    public function getTree($pid){
     $child = $this->getchild($this->tree,$pid);
     if(is_array($child)){
		 $this->pidtree[$pid]=$pid;
        $this->l ++;
        $cnum = count($child);
        $ps = "";
        for($j = 1; $j <= $this->l; $j ++){
            $ps = $ps.$this->prestr;
        }
        foreach($child as $k => $v){
            $this->newtree[$this->i] = $v;
            $this->newtree[$this->i]['lever'] = $this->l;
            $this->newtree[$this->i]['prestr'] = $ps;
            $this->i ++;
            $this->getTree($v['id']);
			
        }
        $this->l --;
     }else{

     }
     return $this->newtree;

    }
 
    public function getFormatStr($pid){
        $fstr = "";
        $nt = $this->getTree($pid);
        foreach($nt as $k=>$v){
            if($v['pid']==0){
                //$fstr .= "<tr class='odd' data-tt-id='".$v['id']."'>".str_replace('{#id#}',$v['prestr'].$v['id'],$this->formatstr)."<td>".$v['name']."</td>"."<td>".$v['parent_id']."</td><td class='button-column'><a class='view' title='View' rel='tooltip' href='/website/backend/www/mall/category/view/id/".$v['id']."'><i class='icon-eye-open'></i></a> <a class='update' title='Update' rel='tooltip' href='/website/backend/www/mall/category/update/id/".$v['id']."'><i class='icon-pencil'></i></a> <a class='delete' title='Delete' rel='tooltip' href='/website/backend/www/mall/category/delete/id/".$v['id']."'><i class='icon-trash'></i></a></td></tr>";
				if(in_array($v['id'],$this->pidtree)){//用来判断是不有子类
                	$fstr .= str_replace('{#url#}',$v['link'],str_replace('{#name#}',$v['title'],$this->childouter)).'<ul class="dropdown-menu">
					#child'.$v['id'].'#
					</ul>
					</li>
					';
				}else{
					$fstr .= str_replace('{#url#}',$v['link'],str_replace('{#name#}',$v['title'],$this->outer));
				}
			}else{
			   //$fstr .= "<tr class='odd' data-tt-id='".$v['id']."' data-tt-parent-id='".$v['pid']."'>".str_replace('{#id#}',$v['prestr'].$v['id'],$this->formatstr)."<td>".$v['name']."</td>"."<td>".$v['parent_id']."</td><td class='button-column'><a class='view' title='View' rel='tooltip' href='/website/backend/www/mall/category/view/id/".$v['id']."'><i class='icon-eye-open'></i></a> <a class='update' title='Update' rel='tooltip' href='/website/backend/www/mall/category/update/id/".$v['id']."'><i class='icon-pencil'></i></a> <a class='delete' title='Delete' rel='tooltip' href='/website/backend/www/mall/category/delete/id/".$v['id']."'><i class='icon-trash'></i></a></td></tr>";
                $child[$v['pid']].= str_replace('{#url#}',$v['link'],str_replace('{#name#}',$v['title'],$this->formatstr));
            }
        }
		foreach($child as $id=>$c){
			$fstr=str_replace('#child'.$id.'#',$c,$fstr);
		}
		return $fstr;
    }
 
}
?>