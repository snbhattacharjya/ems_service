<?php

namespace App\Http\Controllers;
use App\User;
use App\Subdivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Http\Response;
//use App\Http\Controllers\AdminController;
class UserController extends Controller
{
	public function getState(){
		$UserArea=auth('api')->user()->area;
		$stateCode=DB::table('districts')->where('id',$UserArea)->pluck('state_id');
		return $stateCode[0];
	}
	public function createUser(Request $request){
		$request->validate([
            'name' => 'required|string|max:20',
			'email' => 'required|email',
           // 'mobile' => 'required|number|max:10',
			'aadhaar' => 'required',
			'level' => 'required',
			//'usercreationtype'=>'required',
			'designation'=>'required'
			]);
		$UserArea=auth('api')->user()->area;
		$user_type_code=$request->level;
		$userCreationType=$request->usercreationtype;
		$userGenertaionLevelCode=$this->getUserLevelName($user_type_code);
		$getStateCode=$this->getState();
		//print_r($getStateCode);exit;
		 //echo $request->level;
		if($request->level==='05'){//ADM
			  $user_id=$getStateCode.$userGenertaionLevelCode[0].$UserArea.$userCreationType;
			}else if($request->level==='06'){//SDO
			  $user_id=$getStateCode.$userGenertaionLevelCode[0].$UserArea.$userCreationType;
			}else if($request->level==='07'){//BDO
			 $user_id=$getStateCode.$userGenertaionLevelCode[0].$UserArea.$userCreationType;
			}else{
			 $user_id=$getStateCode.$userGenertaionLevelCode[0].$UserArea;
		}
		//echo $user_id;
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
		//$AddUser->created_at = now()->timestamp;
		$AddUser->user_id = $user_id;
		$AddUser->password = Hash::make($user_id);
		$AddUser->change_password =0 ;
		$AddUser->save();
		$lastInsertedId=$AddUser->id; // get office id
		//$arr=array('ok'=>'User Created with random Password','UserId'=>$lastInsertedId,'status'=>200);

		return response()->json($user_id,201);
	}

	public function updateUser(Request $request){

		$request->validate([
            'name' => 'required|string|max:20',
			'email' => 'required|email',
           // 'mobile' => 'required|number|max:10',
			'aadhaar' => 'required',
			'level' => 'required',
			'usercreationType'=>'required',
			'designation'=>'required'
			]);
		$UserArea=auth('api')->user()->area;
		$user_type_code=$request->level;
		$userGenertaionLevelCode=$this->getUserLevelName($user_type_code);
		$userCreationType=$request->userCreationType;
		 $user_id='WB'.$userGenertaionLevelCode[0].$UserArea.$userCreationType;exit;
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
		//$AddUser->created_at = now()->timestamp;
		$AddUser->user_id = $user_id;
		$rand=rand();
		$AddUser->password = Hash::make($rand);
		$AddUser->change_password =0 ;
		//$AddUser->save();
		 $lastInsertedId=$AddUser->id; // get office id
		$arr=array('ok'=>'User Created with random Password','UserId'=>$lastInsertedId,'status'=>201);

		return response()->json($arr);

    }
    public function getallUsers(){
        $area=auth('api')->user()->area;//exit;
       $list=User::where('area',$area)
                  ->whereNotIn('level', [1, 2, 3])
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
		$getUsercreationSubLevel = DB::table('user_sub_level')->where('user_type_code', $request->id)->get();
		//return $getUsercreationSubLevel;
		return response()->json($getUsercreationSubLevel);
	}
	public function getUserLevelName($user_type_code){
		$UserLevel=DB::table('user_levels')->where('user_type_code',$user_type_code)->pluck('name');
		return $UserLevel;
	}
	public function getDefaultMenuPermission_To_assignPermission($user_type_code){
		//To get Default Menu
	    $getDefaultMenuPermission=DB::table('default_permission')->where('user_type_code',$user_type_code)->pluck('menu_id');
		return $getDefaultMenuPermission;
	}
	public function getDefaultPrevillege_To_assignPrevillege($user_type_code){
		//To Get Default Previllege
	    $getDefaultMenuPermission=DB::table('default_previllege_assign')->where('user_type_code',$user_type_code)->get();
		return $getDefaultMenuPermission;
	}

}
