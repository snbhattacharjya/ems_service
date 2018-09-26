<?php

namespace App\Http\Controllers;
use App\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Illuminate\Http\Response;
class PersonnelController extends Controller
{


    public function __construct()
    {
        $this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
    }





    public function getAllPersonnel()
    {
		 
        if($this->level===10){
            return Personnel::where('office_id' , $this->userID)->get();

        }else if($this->level===3 || $this->level===12){
			 return Personnel:: where('district_id',$this->district)->get();
			//
		}else{
			 return response()->json('Unauthorize Access',401);
		}

    }
	public function getAllPersonnelbyoffice(Request $request)
    {
		 $officeid=$request->officeid;
        return Personnel:: where('district_id','=',$this->district)
			                  ->where('office_id' ,'=',$officeid)
				              ->get();
       
    }
	

    public function getPersonnelById(Request $request)
    {

        return Personnel::where('id' , $request->id)->get();

    }
    public function store(Request $request)
    {


         $request->validate([
             'officer_name' => 'required|string|max:50',
             'designation' => 'required|string|max:50',
             'aadhaar' => 'digits:12',
             'present_address' => 'required|string|max:100',
             'permanent_address' => 'required|string|max:100',
             'dob' => 'required|date',
             'gender' => 'required',
             'scale' => 'required',
             'basic_pay' => 'required|numeric',
             'grade_pay' => 'required|numeric',
             'emp_group' => 'required',
             'working_status' => 'required',
             'email' => 'required|email',
             'phone' => 'required|numeric',
             'mobile' => 'required|digits:10',
             'qualification_id' => 'required',
             'language_id' => 'required',
             'epic' => 'required',
             'part_no' => 'numeric',
             'sl_no' => 'numeric',
             'assembly_temp_id' => 'required',
             'assembly_perm_id' => 'required',
             'assembly_off_id' => 'required',
             'block_muni_temp_id' => 'required',
             'block_muni_temp_id' => 'required',
             'block_muni_temp_id' => 'required',
             'branch_ifsc' => 'required',
             'bank_account_no' => 'required|numeric',



         ]);
		if($this->level===10){$officeid=$this->userID;}else{$officeid=$request->office_id; }
        $id = DB::select('SELECT MAX(CAST(SUBSTR(id,-5) AS UNSIGNED)) AS MaxID FROM personnel WHERE subdivision_id = ?',[substr($officeid,0,4)]);

        $id = $id[0]->MaxID;

        if(is_null($id)){
            $id = substr($officeid,0,4).'00001';
        }
        else{
            $id = substr($officeid,0,4).str_pad($id+1,5,"0",STR_PAD_LEFT);
        }

        $request = array_add($request,'id',$id);
        $request->validate([
            'id' => 'required|unique:personnel|digits:9'
        ]);

        $personnel =new personnel;
        $personnel->id = $request->id;
		if($this->level===10){
			$personnel->office_id = $this->userID;
		}else{
        $personnel->office_id = $request->office_id;

		}

        $personnel->name = $request->officer_name;
        $personnel->designation = $request->designation;
        $personnel->aadhaar = $request->aadhaar;
        $personnel->qualification_id = $request->qualification_id;
        $personnel->language_id = $request->language_id;
        $personnel->dob = $request->dob;
        $personnel->gender = $request->gender;

        $personnel->scale = $request->scale;
        $personnel->basic_pay = $request->basic_pay;
        $personnel->grade_pay = $request->grade_pay;
        $personnel->working_status = $request->working_status;
        $personnel->emp_group = $request->emp_group;

        $personnel->email = $request->email;
        $personnel->phone = $request->phone;
        $personnel->mobile = $request->mobile;
        $personnel->present_address = $request->present_address;
        $personnel->permanent_address = $request->permanent_address;
        $personnel->block_muni_temp_id = $request->block_muni_temp_id;
        $personnel->block_muni_perm_id = $request->block_muni_perm_id;
        $personnel->block_muni_off_id = $request->block_muni_off_id;

        $personnel->epic = $request->epic;
        $personnel->part_no = $request->part_no;
        $personnel->sl_no = $request->sl_no;
        $personnel->assembly_temp_id = $request->assembly_temp_id;
        $personnel->assembly_perm_id = $request->assembly_perm_id;
        $personnel->assembly_off_id = $request->assembly_off_id;

        $personnel->branch_ifsc = $request->branch_ifsc;
        $personnel->bank_account_no = $request->bank_account_no;

        $personnel->district_id = substr($officeid,0,2);
        $personnel->subdivision_id = substr($officeid,0,4);

        $personnel->save();

        return response()->json($personnel->id,201);

    }
    public function update(Request $request)
    {

	 if($this->level===10 && $request->id != $this->userID){
			return response()->json('Invalid Office',401);

		}else{


         $request->validate([
             'officer_name' => 'required|string|max:50',
             'designation' => 'required|string|max:50',
             'aadhaar' => 'digits:12',
             'present_address' => 'required|string|max:100',
             'permanent_address' => 'required|string|max:100',
             'dob' => 'required|date',
             'gender' => 'required',
             'scale' => 'required',
             'basic_pay' => 'required|numeric',
             'grade_pay' => 'required|numeric',
             'emp_group' => 'required',
             'working_status' => 'required',
             'email' => 'required|email',
             'phone' => 'required|numeric',
             'mobile' => 'required|digits:10',
             'qualification_id' => 'required',
             'language_id' => 'required',
             'epic' => 'required',
             'part_no' => 'numeric',
             'sl_no' => 'numeric',
             'assembly_temp_id' => 'required',
             'assembly_perm_id' => 'required',
             'assembly_off_id' => 'required',
             'block_muni_temp_id' => 'required',
             'block_muni_temp_id' => 'required',
             'block_muni_temp_id' => 'required',
             'branch_ifsc' => 'required',
             'bank_account_no' => 'required|numeric',



         ]);

        $personnel =Personnel::find($request->id);
        $personnel->id = $request->id;
        if($this->level===10){
			$personnel->office_id = $this->userID;
		}else{
        $personnel->office_id = $request->office_id;

		}
        $personnel->name = $request->officer_name;
        $personnel->designation = $request->designation;
        $personnel->aadhaar = $request->aadhaar;
        $personnel->qualification_id = $request->qualification_id;
        $personnel->language_id = $request->language_id;
        $personnel->dob = $request->dob;
        $personnel->gender = $request->gender;

        $personnel->scale = $request->scale;
        $personnel->basic_pay = $request->basic_pay;
        $personnel->grade_pay = $request->grade_pay;
        $personnel->working_status = $request->working_status;
        $personnel->emp_group = $request->emp_group;

        $personnel->email = $request->email;
        $personnel->phone = $request->phone;
        $personnel->mobile = $request->mobile;
        $personnel->present_address = $request->present_address;
        $personnel->permanent_address = $request->permanent_address;
        $personnel->block_muni_temp_id = $request->block_muni_temp_id;
        $personnel->block_muni_perm_id = $request->block_muni_perm_id;
        $personnel->block_muni_off_id = $request->block_muni_off_id;

        $personnel->epic = $request->epic;
        $personnel->part_no = $request->part_no;
        $personnel->sl_no = $request->sl_no;
        $personnel->assembly_temp_id = $request->assembly_temp_id;
        $personnel->assembly_perm_id = $request->assembly_perm_id;
        $personnel->assembly_off_id = $request->assembly_off_id;

        $personnel->branch_ifsc = $request->branch_ifsc;
        $personnel->bank_account_no = $request->bank_account_no;
        if($this->level===10){
			$personnel->district_id = substr($this->userID,0,2);
			$personnel->subdivision_id = substr($this->userID,0,4);
		}else{
        $personnel->district_id = substr($request->office_id,0,2);
        $personnel->subdivision_id = substr($request->office_id,0,4);
		}

        $personnel->save();

        return response()->json($personnel->id,201);

    }

    }
}
