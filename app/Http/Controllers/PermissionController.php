<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\UserLevel;
use App\User;
use App\District;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
class PermissionController extends Controller
{
    public function getPermission(){

	if(Auth::guard('api')->check()){	
	$userId=auth('api')->user()->id;
	$level=auth('api')->user()->level;
	$area=auth('api')->user()->area;
	}

	$arr=array();
	$arr['user']=auth('api')->user();
    $arr['menu'][]=array('parent_menu'=>'Dashboard','group'=>'dashboard','menu_icon_name'=>'dashboard','menu_link'=>'/dashboard','submenu'=>'null');
	
    $arr['dashboard']=(new DashboardController)->getOfficeData();
	
    $arr['user']['district']=$this->getDistrict($area);
	//$arr['election']=$this->getElection();



    if($level===10){
    $office=DB::select('SELECT category_id FROM offices  WHERE id ='.$arr['user']['user_id'].' ');
    foreach($office as $item){
        $arr['user']['officelevel']=$item->category_id;
	}
    $arr['menu'][]=array('parent_menu'=>'About','group'=>'about','menu_link'=>'/dashboard/help','menu_icon_name'=>'help','submenu'=>'null');
	$arr['menu'][]=array('parent_menu'=>'Contact','group'=>'contact','menu_link'=>'/dashboard/contact','menu_icon_name'=>'contacts','submenu'=>'null');
	
	$arr['menu'][]=array('parent_menu'=>'Office','group'=>'office','menu_link'=>'','menu_icon_name'=>'business',
	'submenu'=>array(
	array("menu_name"=>'Edit Office',"menu_link"=>'/office/edit',"menu_icon_name"=>'create')));

   $arr['menu'][]=array('parent_menu'=>'Personnel','group'=>'personnel','menu_link'=>'','menu_icon_name'=>'people',
	'submenu'=>array(
	array("menu_name"=>'Create new personnel',"menu_link"=>'/personnel/create',"menu_icon_name"=>'create'),
	array("menu_name"=>'Personnel Lists',"menu_link"=>'/personnel/list',"menu_icon_name"=>'list_alt')));
	
    $arr['menu'][]=array('parent_menu'=>'MIS Report','group'=>'mis_report','menu_link'=>'','menu_icon_name'=>'assessment',
	'submenu'=>array(
	array("menu_name"=>'Office Declaration PP1 Format',"menu_link"=>'/misreport/pp1',"menu_icon_name"=>'business'),
	array("menu_name"=>'Personnel Details  PP2 Format',"menu_link"=>'/misreport/pp2',"menu_icon_name"=>'people')));
	
	$arr['menu'][]=array('parent_menu'=>'Download','group'=>'download','menu_link'=>'/downloads','menu_icon_name'=>'cloud_download',
	'submenu'=>'null'
	);
	$arr['menu'][]=array('parent_menu'=>'Password Change','group'=>'password_change','menu_link'=>'/change_password','menu_icon_name'=>'restore_page',
	'submenu'=>'null'
	);

}else{
		$menu=DB::select('SELECT m.menu_id, m.menu_name,m.menu_link,m.menu_icon_name FROM menu m JOIN permission p ON p.menu_id = m.menu_id WHERE p.user_id ='.$userId.' and m.top_menu_id=0  order by menu_order asc ');
		foreach($menu as $mval){
			 $menuname=$mval->menu_name;
			 $menuslug=strtolower(str_replace(' ', '_', $mval->menu_name));
			(array)$arr['menu'][]=array('parent_menu'=>$menuname,'group'=>$menuslug,'menu_link'=>$mval->menu_link,'menu_icon_name'=>$mval->menu_icon_name,'submenu'=>$this->submenu($userId,$mval->menu_id));
		}
	}
return response()->json($arr,200);
	}
	public function submenu($userId,$id){
		$submenu=DB::select('SELECT  m.menu_name,m.menu_link,m.menu_icon_name FROM menu m JOIN permission p ON p.menu_id = m.menu_id WHERE p.user_id ='.$userId.' and m.top_menu_id='.$id.'');
		if($submenu){
			$arrsub=array();
			foreach($submenu as $submenuVal){
			(array)$arrsub[]=array("menu_name"=>$submenuVal->menu_name,"menu_link"=>$submenuVal->menu_link,"menu_icon_name"=>$submenuVal->menu_icon_name);
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
