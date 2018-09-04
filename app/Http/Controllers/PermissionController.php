<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\UserLevel;
class PermissionController extends Controller
{
    //
	
	public function getPermission(){
	$userId=auth('api')->user()->id;
	$level=auth('api')->user()->level;
	
	$arr=array();
	$arr['user']=auth('api')->user();
	
	
	$menu=DB::select('SELECT m.menu_id, m.menu_name,m.menu_link,m.menu_icon_name FROM menu m JOIN permission p ON p.menu_id = m.menu_id WHERE p.user_id ='.$userId.' and m.top_menu_id=0 ');
	foreach($menu as $mval){
		 $menuname=$mval->menu_name;
		 $menuslug=strtolower(str_replace(' ', '_', $mval->menu_name));
		
		 $arr['menu'][]=array('parent_menu'=>$menuname,'menu_link'=>$mval->menu_link,'menu_icon_name'=>$mval->menu_icon_name,'submenu'=>$this->submenu($userId,$mval->menu_id));
		 }
	 $previllege=DB::select('SELECT m.menu_name,p.menu_id, prevg.prev_add, prevg.prev_edit,prevg.prev_delete,prevg.prev_view FROM menu m JOIN permission p ON p.menu_id = m.menu_id JOIN previllege_assign prevg ON p.menu_id = prevg.menu_id WHERE m.menu_id=p.menu_id and p.user_id = prevg.user_id and p.user_id ='.$userId.' ');
	foreach($previllege as $val){
		$menuname=strtolower(str_replace(' ', '_', $val->menu_name));
		$arr['previllege'][$menuname]=array('add'=>$val->prev_add,'edit'=>$val->prev_edit,'delete'=>$val->prev_delete,'view'=>$val->prev_view);
	}
	return response()->json($arr,200);
	}
	
	public function submenu($userId,$id){
		$submenu=DB::select('SELECT  m.menu_name,m.menu_link,m.menu_icon_name FROM menu m JOIN permission p ON p.menu_id = m.menu_id WHERE p.user_id ='.$userId.' and m.top_menu_id='.$id.'');
		if($submenu){
			return $submenu;
			
		}else{
			return 'null';
		}
		
	}
	
	
	
}
