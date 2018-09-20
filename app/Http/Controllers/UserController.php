<?php

namespace App\Http\Controllers;
use App\User;
use App\Subdivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Permission;
use App\Privilege;
class UserController extends Controller
{
	public function getState(){
			$UserArea=auth('api')->user()->area;
			$stateCode=DB::table('districts')->where('id',$UserArea)->pluck('state_id');
			return $stateCode[0];
	}
	public function createUser(Request $request){
		    $request->validate([
            'name' => 'required|string|max:40',
			'email' => 'required|email',
			//'aadhaar' => 'required',
			'level' => 'required',
			'designation'=>'required'
			]);
			$UserArea=auth('api')->user()->area;
			$user_type_code=$request->level;
			$userGenertaionLevelCode=$this->getUserLevelName($user_type_code);
			$userCreationType=$request->sub_level;
            $getStateCode=$this->getState();
            if($request->level==='04'){

                $user_id=$getStateCode.$UserArea.$userGenertaionLevelCode[0].$userCreationType;
					 if (User::where('user_id', '=',$user_id)->exists()){
					  $msg='User Already Exists';
					  $execute=0;
					 }else{
					  $msg= $user_id;
					  $execute=1;
					 }
            }
		elseif($request->level==='05'){//ADM
					 $user_id=$getStateCode.$UserArea.$userGenertaionLevelCode[0].$userCreationType;
					 if (User::where('user_id', '=',$user_id)->exists()){
					  $msg='User Already Exists';
					  $execute=0;
					 }else{
					  $msg= $user_id;
					  $execute=1;
					 }
		}else if($request->level==='06'){//SDO(userCreationType)->Subdivision Code
				     $user_id=$getStateCode.$UserArea.$userGenertaionLevelCode[0].$userCreationType;
					 if (User::where('user_id', '=',$user_id)->exists()){
					  $msg='User Already Exists';
					  $execute=0;
					 }else{
					  $msg= $user_id;
					  $execute=1;
					 }
	   }else if($request->level==='07'){//BDO(userCreationType) ->BlockMuni Code
					 $user_id=$getStateCode.$UserArea.$userGenertaionLevelCode[0].$userCreationType;
					 if (User::where('user_id', '=',$user_id)->exists()){
					  $msg='User Already Exists';
					  $execute=0;
					 }else{
					  $msg= $user_id;
					  $execute=1;
					 }
	   }elseif($request->level==='08'){//PPCELL
			        // $user_id=$getStateCode.$userGenertaionLevelCode[0].$UserArea.$userCreationType;
					//  $msg='User Already Exists';
					//  $execute=0;

		}else{
			       //  $user_id=$getStateCode.$userGenertaionLevelCode[0].$UserArea;
		}

		if($execute!='' and $execute==1){

			$AddUser=new User;
			$AddUser->name = $request->name;
			$AddUser->email = $request->email;
			$AddUser->mobile = $request->mobile;
			$AddUser->aadhaar = $request->aadhaar;
			$AddUser->designation = $request->designation;
			$AddUser->level = $request->level;
			$AddUser->sublevel = $request->sublevel;
			$AddUser->area = $UserArea;
			$AddUser->is_active = 1;
			$AddUser->created_at = now()->timestamp;
			$AddUser->user_id = $msg;
			$rand=rand();
			$AddUser->password = Hash::make($user_id);
			$AddUser->change_password =1 ;
			$AddUser->save();
			$lastInsertedId=$AddUser->id; // get office id
			 //get office id
			$this->getDefaultMenuPermission_To_assignPermission($lastInsertedId,$user_type_code);
            $msg ="User created succesfully with code - ".$msg;
        }

		    return response()->json($msg,201);
	}

	public function updateUser(Request $request){
          $getStateCode=$this->getState();
		$request->validate([
            'name' => 'required|string|max:40',
			'email' => 'required|email',
           // 'mobile' => 'required|number|max:10',
			//'aadhaar' => 'required',
			'level' => 'required',
			'usercreationType'=>'required',
			'designation'=>'required'
			]);
			$UserArea=auth('api')->user()->area;
			$user_type_code=$request->level;
			$userGenertaionLevelCode=$this->getUserLevelName($user_type_code);
			$userCreationType=$request->userCreationType;
			 $user_id=$getStateCode.$userGenertaionLevelCode[0].$UserArea.$userCreationType;exit;
			$AddUser=new User;
			$AddUser->name = $request->name;
			$AddUser->email = $request->email;
			$AddUser->mobile = $request->mobile;
			$AddUser->aadhaar = $request->aadhaar;
			$AddUser->designation = $request->designation;
			$AddUser->level = $request->level;
			$AddUser->sublevel = $request->sublevel;
			$AddUser->area = $UserArea;
			$AddUser->is_active = 1;
			$AddUser->created_at = now();
			$AddUser->user_id = $user_id;
			$rand=rand();
			$AddUser->password = Hash::make($rand);
			$AddUser->change_password =0 ;
			$AddUser->save();
			
			$arr=array('ok'=>'User Created with random Password','UserId'=>$lastInsertedId,'status'=>201);

		    return response()->json($arr);

    }
    public function getallUsers(){
			$area=auth('api')->user()->area;
			$level=auth('api')->user()->level;
			if($level===3){
				$canNotsee=[1,2,3];
			}elseif($level===6){//SDO
				//$canNotsee=[1,2,3,4,5,7,8,9,10];
				//can see all his sub level
			}elseif($level===7){//BDO
				//$canNotsee=[1,2,3,4,5,6,8,9,10];
				//can see all his sub level
			}elseif($level===8){//PPCELL
				//$canNotsee=[1,2,3,4,5,6,7,9,10];
				//can see all his sub level
			}else{

			}
			$list=User::where('area',$area)
					  ->whereNotIn('level',[1,2,3])
					  ->get();
		   return response()->json($list);
   }

	public function getUserCreation(){
			$id=auth('api')->user()->level;
			$response=$this->getUsercreationLevel($id);
			return response()->json($response);
	}
	public function getUsercreationLevel($id){
			$UserCreationLevel = DB::table('user_levels')->where('parent_type_code', $id)->get();
			return $UserCreationLevel;
	}
	public function getUsercreationSubLevel(Request $request){

		$levelparentId=$request->id;
		$area=auth('api')->user()->area;
		if($levelparentId=='05'){
		  (array)$adm=DB::select("select sub_user_code,sub_user_name from user_sub_level where user_type_code=".$levelparentId."");
		 foreach($adm as $admval){
		 $arr[]=array('sub_user_code'=>$admval->sub_user_code,'sub_user_name'=>$admval->sub_user_name);
		 }
		  return (array)$arr;
	     }
		  if($levelparentId=='06'){
			  (array)$adm=DB::select("select id,name from subdivisions where district_id=".$area."");

			  foreach($adm as $admval){
		 $arr[]=array('sub_user_code'=>$admval->id,'sub_user_name'=>$admval->name);
		 }
			  return (array)$arr;
	     }
		 if($levelparentId=='07'){
		  (array)$sdo=DB::select("select id,name,subdivision_id from block_munis where SUBSTRING(id,1,2)=".$area."");

		  foreach($sdo as $admval){
		 $arr[]=array('sub_user_code'=>$admval->id,'sub_user_name'=>$admval->name);
		 }
     	  return (array)$arr;
	     }
          if($levelparentId=='08'){
		  (array)$ppcell=DB::select("select sub_user_code,sub_user_name from user_sub_level where user_type_code=".$levelparentId."");

		  foreach($ppcell as $admval){
		 $arr[]=array('sub_user_code'=>$admval->sub_user_code,'sub_user_name'=>$admval->sub_user_name);
		 }
     	  return (array)$arr;
	     } 
	}
	public function getUserLevelName($user_type_code){
			$UserLevel=DB::table('user_levels')->where('user_type_code',$user_type_code)->pluck('name');
			return $UserLevel;
	}


     public function diocreation(Request $request){
	        $AddUser=new User;
			$AddUser->name = $request->name;
			$AddUser->email = $request->email;
			$AddUser->mobile = $request->mobile;
			$AddUser->aadhaar = $request->aadhaar;
			$AddUser->designation = $request->designation;
			$AddUser->level =11;
			//$AddUser->sublevel = $request->sublevel;
			$AddUser->area = $request->area;
			$AddUser->is_active = 1;
			$AddUser->created_at = now();
			$AddUser->user_id = $request->user_id;
			$pass=$request->user_id;
			$AddUser->password = Hash::make($pass);
			$AddUser->change_password =0 ;
			$AddUser->save();
			$lastInsertedId=$AddUser->id; // get office id
			if(!empty($lastInsertedId)){
			$this->getDefaultMenuPermission_To_assignPermission($lastInsertedId);
			}
			$arr=array('ok'=>'User Created with random Password','UserId'=>$lastInsertedId,'status'=>201);

		    return response()->json($arr);

           }
	public function getDefaultMenuPermission_To_assignPermission($lastInsertedId,$user_type_code){
			$getDefaultMenuPermission=DB::table('default_permission')->where('user_type_code',$user_type_code)->pluck('menu_id');
			$arr=array();
			foreach($getDefaultMenuPermission as $permissionVal){
			$arr[]=array('user_id'=>$lastInsertedId,'user_type_code'=>$user_type_code,'menu_id'=>$permissionVal);
			}
			//print_r($arr);
		    Permission::insert($arr);
	}
	public function getDefaultPrevillege_To_assignPrevillege(){
			$getDefaultMenuPermission=DB::table('default_previllege_assign')->where('user_type_code','10')->array();
			$arr=array();
			if(count($getDefaultMenuPermission)!=0 and $getDefaultMenuPermission!='' ){
			foreach($getDefaultMenuPermission as $privilegeVal){
			$arr[]=array('user_id'=>30,'user_type_code'=>'10','menu_id'=>$privilegeVal);
			}
			Privilege::insert($arr);
			}


	}



}
