<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\UserLevel;
use App\User;
use App\District;
use App\Http\Controllers\DashboardController;
class PermissionController extends Controller
{
    public function getPermission(){
	$userId=auth('api')->user()->id;
	$level=auth('api')->user()->level;
	$area=auth('api')->user()->area;
	$arr=array();
	$arr['user']=auth('api')->user();

	//$arr['state']='WB';


	$arr['menu'][]=array('parent_menu'=>'Dashboard','group'=>'dashboard','menu_icon_name'=>'dashboard','menu_link'=>'/dashboard','submenu'=>'null');
	$menu=DB::select('SELECT m.menu_id, m.menu_name,m.menu_link,m.menu_icon_name FROM menu m JOIN permission p ON p.menu_id = m.menu_id WHERE p.user_id ='.$userId.' and m.top_menu_id=0  order by menu_order asc ');
	foreach($menu as $mval){
		 $menuname=$mval->menu_name;
		 $menuslug=strtolower(str_replace(' ', '_', $mval->menu_name));
		(array)$arr['menu'][]=array('parent_menu'=>ucfirst(strtolower($menuname)),'group'=>$menuslug,'menu_link'=>$mval->menu_link,'menu_icon_name'=>$mval->menu_icon_name,'submenu'=>$this->submenu($userId,$mval->menu_id));
	}
	$previllege=DB::select('SELECT m.menu_name,p.menu_id, prevg.prev_add, prevg.prev_edit,prevg.prev_delete,prevg.prev_view FROM menu m JOIN permission p ON p.menu_id = m.menu_id JOIN previllege_assign prevg ON p.menu_id = prevg.menu_id WHERE m.menu_id=p.menu_id and p.user_id = prevg.user_id and p.user_id ='.$userId.' ');
	foreach($previllege as $val){
		$menuname=strtolower(str_replace(' ', '_', $val->menu_name));
		(array)$arr['previllege'][$menuname]=array('add'=>$val->prev_add,'edit'=>$val->prev_edit,'delete'=>$val->prev_delete,'view'=>$val->prev_view);
	}
    $arr['dashboard']=(new DashboardController)->getOfficeData();
    $arr['user']['district']=$this->getDistrict($area);
    $arr['election']=$this->getElection();
    $office=DB::select('SELECT category_id FROM offices  WHERE id ='.$arr['user']['user_id'].' ');
    //print_r($office) ;
    foreach($office as $item){
        $arr['user']['officelevel']=$item->category_id;
    }
	return response()->json($arr,200);
	}
	public function submenu($userId,$id){
		$submenu=DB::select('SELECT  m.menu_name,m.menu_link,m.menu_icon_name FROM menu m JOIN permission p ON p.menu_id = m.menu_id WHERE p.user_id ='.$userId.' and m.top_menu_id='.$id.'');
		if($submenu){
			$arrsub=array();
			foreach($submenu as $submenuVal){
			(array)$arrsub[]=array("menu_name"=>ucfirst(strtolower($submenuVal->menu_name)),"menu_link"=>$submenuVal->menu_link,"menu_icon_name"=>$submenuVal->menu_icon_name);
			}
			return $arrsub;
		}
		else{
			return 'null';
		}
	}

	public function getDistrict($districtID){
	 $district= District::where('id',$districtID)->pluck('name');
	 return $district;
   }
   public function getElection(){
	 $election= DB::select("select name,year from elections");
	 return $election;
   }
}
