<?php

namespace App\Http\Controllers;
use App\User;
use App\Office;
use \Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Hash;

class OfficeController extends Controller
{
    public function __construct()
    {	$this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
    }

	public function getAllOffices()
    {
     if($this->level===3 || $this->level===12){ //DIO and DEO
	    return Office::where('district_id',$this->district)->get();
	    }elseif($this->level===6){//SDO
		 $subdivision_id=substr($this->userID,-4);
	     return Office::where('district_id',$this->district)
	                 ->where('subdivision_id',$subdivision_id)
	                 ->get();
	    }elseif($this->level===7){//BDO
		$block_munis=substr($this->userID,-6);
	    return Office::where('district_id',$this->district)
	                 ->where('block_muni_id',$block_munis)
	                 ->get();
	    }else{
		
		return response()->json('Unauthorize Access',422);
	  }


   }

	public function getAllofficeBysubdivision(Request $request)
    {
	   if($this->level===3 || $this->level===12){//DIO	and DEO
		
            $subdivision_id=$request->subdivision_id;
	        return Office::where('district_id' ,'=',$this->district)
                   ->where('subdivision_id' ,'=', $subdivision_id)
				   ->get();
	   }elseif($this->level===6){//SDO	
            $subdivision_id=substr($this->userID,-4);	   
			return Office::where('district_id' ,'=',$this->district)
                   ->where('subdivision_id' ,'=', $subdivision_id)
				   ->get();	   
	   }elseif($this->level===7){//BDO
		    $block_munis=substr($this->userID,-6);	   
			return Office::where('district_id' ,'=',$this->district)
                   ->where('block_muni_id' ,'=', $block_munis)
				   ->get();	
	   }else{
		 return response()->json('Unauthorize Access',422);  
	   }		   
    }
   public function getOfficeById(Request $request) //
    {
        return Office::where('id' , $request->id)->get();

    }
    public function store(Request $request)
    {

		$UserArea=auth('api')->user()->area;
		    $request->validate([
            'office_name' => 'required|string|max:50',
            'identification_code' => 'required|string|max:50',
            'officer_designation' => 'required|string|max:50',
            'office_address' => 'required|string|max:50',
            'post_office' => 'required|string|max:50',
            'pin' => 'required|digits:6',
            'block_muni_id' => 'required',
            'police_station_id' => 'required',
            'ac_id' => 'required',
            'pc_id' => 'required',
            'subdivision_id' => 'required',
            'category_id' => 'required',
            'institute_id' => 'required',
            //'email' => 'required|email',
            //'phone' => 'required|numeric',
            'mobile' => 'required|digits:10',
            'fax' => 'max:15',
            'total_staff' => 'required|numeric',
            'male_staff' => 'required|numeric',
            'female_staff' => 'required|numeric',
			'agree' => 'required|boolean'
        ]);

        $id = DB::select('SELECT MAX(CAST(SUBSTR(id,-4) AS UNSIGNED)) AS MaxID FROM offices WHERE subdivision_id = ?',[$request->subdivision_id]);

        $id = $id[0]->MaxID;

        if(is_null($id)){
            $id = $request->police_station_id.'0001';
        }
        else{
            $id = $request->police_station_id.str_pad($id+1,4,"0",STR_PAD_LEFT);
        }
        
        
        $request = array_add($request,'id',$id);
        $request->validate([
            'id' => 'required|unique:offices|digits:10'
        ]);
        $office =new Office;
        $office->id = $request->id;
        $office->name = $request->office_name;
        $office->identification_code = $request->identification_code;
        $office->officer_designation =  $request->officer_designation;
        $office->address =  $request->office_address;
        $office->post_office =  $request->post_office;
        $office->pin =  $request->pin;
        $office->subdivision_id = $request->subdivision_id;
        $office->district_id = $UserArea;
        $office->block_muni_id =  $request->block_muni_id;
        $office->police_station_id =  $request->police_station_id;
        $office->ac_id =  $request->ac_id;
        $office->pc_id =  $request->pc_id;
        $office->category_id =  $request->category_id;
        $office->institute_id =  $request->institute_id;
        $office->identification_code =  $request->identification_code;
        $office->email =  $request->email;
        $office->phone =  $request->phone;
        $office->mobile =  $request->mobile;
        $office->fax =  $request->fax;
        $office->total_staff =  $request->total_staff;
        $office->male_staff =  $request->male_staff;
        $office->female_staff =  $request->female_staff;
        $office->created_at = date('Y-m-d H:i:s');
       
		if($request->agree==1){
		$office->agree = 1;
		}else{
			$office->agree = 0;
		}
       $office->save();
        //echo $office->id;
       $request=array('name'=>$request->office_name,'email'=>$request->email,'mobile'=>$request->mobile,'officer_designation'=>$request->officer_designation);
        if($office->id!=''){
            $this->createUserFromOffice($request,$office->id);
          }
       

	    return response()->json($office->id,201);
	}
   public function createUserFromOffice($request,$user_id){
        $user_id=$user_id;
        $district=auth('api')->user()->area;
        $AddUser=new User;
		$AddUser->name = $request['name'];
		$AddUser->email = $request['email'];
        $AddUser->mobile = $request['mobile'];
        $AddUser->designation = $request['officer_designation'];
		$AddUser->level = 10;
		$AddUser->area = $district;
		$AddUser->is_active = 1;
		$AddUser->created_at = date('Y-m-d H:i:s');;
		$AddUser->user_id = $user_id;
	    $pass=(new UserController)->random_password();
		$AddUser->password = Hash::make($pass);
		$AddUser->change_password =0 ;
        $AddUser->save();
        //add permission
        $user_increment_id=$AddUser->id;
        if($user_increment_id!=''){
         (new UserController)->getDefaultMenuPermission_To_assignPermission($user_increment_id,10);
        DB::table('user_random_password')->insert(
        ['rand_id' =>$user_id  , 'rand_password' => $pass,'created_at'=>now()]
          );
        }
	  }

    public function update(Request $request){
		 if($this->level===10 && $request->office_id != $this->userID){
		 	return response()->json('Invalid Office',401);

		}else{
			 $request->validate([
            'office_name' => 'required|string|max:50',
            'identification_code' => 'required|string|max:50',
            'officer_designation' => 'required|string|max:50',
            'office_address' => 'required|string|max:50',
            'post_office' => 'required|string|max:50',
            'pin' => 'required|digits:6',
            'block_muni_id' => 'required',
            'police_station_id' => 'required',
            'ac_id' => 'required',
            'pc_id' => 'required',
            'category_id' => 'required',
            'institute_id' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|digits:10',
            'fax' => 'max:15',
            'total_staff' => 'required|numeric',
            'male_staff' => 'required|numeric',
            'female_staff' => 'required|numeric',
			'agree' => 'required|boolean'
        ]);

        $office =Office::find($request->office_id);
       // print_r($office);exit;

        $office->id = $request->office_id;
        $office->name = $request->office_name;
        $office->identification_code = $request->identification_code;
        $office->officer_designation =  $request->officer_designation;
        $office->address =  $request->office_address;
        $office->post_office =  $request->post_office;
        $office->pin =  $request->pin;
		if($this->level!=10){
        $office->subdivision_id = $request->subdivision_id;
		}
        $office->district_id = $this->district;
        $office->block_muni_id =  $request->block_muni_id;
        $office->police_station_id =  $request->police_station_id;
        $office->ac_id =  $request->ac_id;
        $office->pc_id =  $request->pc_id;
        $office->category_id =  $request->category_id;
        $office->institute_id =  $request->institute_id;
        $office->email =  $request->email;
        $office->phone =  $request->phone;
        $office->mobile =  $request->mobile;
        $office->fax =  $request->fax;
        $office->total_staff =  $request->total_staff;
        $office->male_staff =  $request->male_staff;
        $office->female_staff =  $request->female_staff;
		$office->updated_at = date('Y-m-d H:i:s');
		if($request->agree==1){
		$office->agree = 1;
		}else{
			$office->agree = 0;
        }
        
        //echo $request->agree;exit;
        $office->save();

        return response()->json($office->id,201);


		 }

    }

    public function delete(Request $request){
        $office =Office::find($request->office_id);
        if(!$office->isEmpty()){
            $office->delete();
            return response()->json("Office deleted",201);
        }
        else{
            return response()->json("Office does not exist",401);
        }
    }
   
	public function resetPassword(Request $request){
        $officeId=$request->officeId;
        $newPassword=$this->random_password();
        $date=date('Y-m-d H:i:s');
        if($this->level==3 || $this->level==12){
            User::where('user_id', $officeId)
            ->update(['password'=>Hash::make($newPassword),'change_password'=>0, 'updated_at' =>date('Y-m-d H:i:s')]);
         if(DB::table('user_random_password')->where('rand_id', '=', $officeId)->exists()){

           DB::update("update user_random_password set 	rand_password='$newPassword' , created_at='$date' where rand_id= '$officeId'");
           }else{
            $values = array('rand_id' => $officeId,'rand_password' => $newPassword,'created_at'=>date('Y-m-d H:i:s')); 
            DB::table('user_random_password')->insert($values);;   
           }
        
           return response()->json($newPassword,201);
       
       
        }
    
        }

    function random_password( $length = 8 ) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_?";
		$password = substr( str_shuffle( $chars ), 0, $length );
		return $password;
	}
   
   public function getOfficeType(Request $request){
     
    if($this->level==3 || $this->level==12){
        $arr=array();
        $arr['officeType']= Office::where('id',$request->officeId)->pluck('category_id');  
        return $arr;
    }
   }



}
