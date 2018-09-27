<?php

namespace App\Http\Controllers;
use App\User;
use App\Office;
use \Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'mobile' => 'required|digits:10',
            'fax' => 'max:15',
            'total_staff' => 'required|numeric',
            'male_staff' => 'required|numeric',
            'female_staff' => 'required|numeric',
        ]);

        $id = DB::select('SELECT MAX(CAST(SUBSTR(id,-4) AS UNSIGNED)) AS MaxID FROM offices WHERE subdivision_id = ?',[$request->subdivision_id]);

        $id = $id[0]->MaxID;

        if(is_null($id)){
            $id = $request->subdivision_id.'0001';
        }
        else{
            $id = $request->subdivision_id.str_pad($id+1,6,"0",STR_PAD_LEFT);
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
		if($office->agree==true){
		$office->agree = 1;
		}else{
			$office->agree = 0;
		}
        $office->save();
	    return response()->json($office->id,201);
	}
   public function createUserFromOffice($data,$user_id){
	    print_r($data);
	    $AddUser=new User;
		$AddUser->name = $request->name;
		$AddUser->email = $request->email;
		$AddUser->mobile = $request->mobile;
		//$AddUser->aadhaar = $request->aadhaar;
		//$AddUser->designation = $request->designation;
		$AddUser->level = 10;
		$AddUser->area = $UserArea;
		$AddUser->is_active = 1;
		$AddUser->created_at = now()->timestamp;
		$AddUser->user_id = $user_id;
		$rand=rand();
		$AddUser->password = Hash::make($rand);
		$AddUser->change_password =0 ;
		//$AddUser->save();
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
            'phone' => 'required|numeric',
            'mobile' => 'required|digits:10',
            'fax' => 'max:15',
            'total_staff' => 'required|numeric',
            'male_staff' => 'required|numeric',
            'female_staff' => 'required|numeric',
        ]);

        $office =Office::find($request->office_id);
        $office->id = $request->office_id;
        $office->name = $request->office_name;
        $office->identification_code = $request->identification_code;
        $office->officer_designation =  $request->officer_designation;
        $office->address =  $request->office_address;
        $office->post_office =  $request->post_office;
        $office->pin =  $request->pin;
		if(!$this->level===10){
        $office->subdivision_id = $request->subdivision_id;
		}
        $office->district_id = $this->district;
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
		$office->updated_at = date('Y-m-d H:i:s');
		if($office->agree==true){
		$office->agree = 1;
		}else{
			$office->agree = 0;
		}
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
}
