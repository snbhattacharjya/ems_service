<?php

namespace App\Http\Controllers;
use App\Personnel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
class PersonnelController extends Controller
{


    public function __construct()
    {  
        if(Auth::guard('api')->check()){
        $this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
        }
    }





    public function getAllPersonnel()
    {

        if($this->level===10){
            return Personnel::where('office_id' , $this->userID)->get();

        }else if($this->level===3 || $this->level===12 || $this->level=8 ){
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

        if($this->level===10){
            $officeid=$this->userID;

        }else{
            $officeid=$request->office_id;
         }

         if($this->is_acPc_exists($officeid)){
         if($this->is_personnelOffice_countMatch($officeid)){

         $request->validate([
             'officer_name' => 'required|string|max:50',
             'designation' => 'required|string|max:50',

             'present_address' => 'required|string|max:100',
             'permanent_address' => 'required|string|max:100',
             'dob' => 'required|date',
             'gender' => 'required',
             'scale' => 'required|max:15',
             'basic_pay' => 'required|numeric|max:9999999',
             //'grade_pay' => 'required|numeric',
             'emp_group' => 'required',
             'working_status' => 'required',

             'phone'=> 'max:15',
             'mobile' => 'required|digits:10',
             'qualification_id' => 'required|numeric',
             'language_id' => 'required|numeric',
             'epic' => 'required|max:20',
             'part_no' => 'numeric|max:9999',
             'sl_no' => 'numeric|max:9999',
             'assembly_temp_id' => 'required|numeric',
             'assembly_perm_id' => 'required|numeric',
             'assembly_off_id' => 'required|numeric',
             'block_muni_temp_id' => 'numeric',
             'block_muni_temp_id' => 'numeric',
             'block_muni_temp_id' => 'numeric',
             'branch_ifsc' => 'required|max:11',
             'bank_account_no' => 'required|unique:personnel,bank_account_no|max:16'



         ]);






        $id = DB::select('SELECT MAX(CAST(SUBSTR(id,-5) AS UNSIGNED)) AS MaxID FROM personnel WHERE subdivision_id = ?',[substr($officeid,0,4)]);

        $id = $id[0]->MaxID;

        if(is_null($id)){
            $id = substr($officeid,0,6).'00001';
        }
        else{
            $id = substr($officeid,0,6).str_pad($id+1,5,"0",STR_PAD_LEFT);
        }


        $request = array_add($request,'id',$id);
        $request->validate([
            'id' => 'required|unique:personnel|digits:11'
        ]);

        $personnel =new personnel;
        $personnel->id = $request->id;
		if($this->level===10){
            $personnel->office_id = $this->userID;

		}else{
        $personnel->office_id = $officeid;

		}

        $personnel->name = strip_tags($request->officer_name,'');
        $personnel->designation = strip_tags($request->designation,'');
       // $personnel->aadhaar = $request->aadhaar;
        $personnel->qualification_id = strip_tags($request->qualification_id,'');
        $personnel->language_id = strip_tags($request->language_id,'');
        $personnel->dob = strip_tags($request->dob,'');
        $personnel->gender = strip_tags($request->gender,'');

        $personnel->scale = strip_tags($request->scale,'');
        $personnel->basic_pay = strip_tags($request->basic_pay,'');
        $personnel->grade_pay = strip_tags($request->grade_pay,'');
        $personnel->working_status = strip_tags($request->working_status,'');
        $personnel->emp_group = strip_tags($request->emp_group,'');

        $personnel->email = strip_tags($request->email,'');
        $personnel->phone = strip_tags($request->phone,'');
        $personnel->mobile = strip_tags($request->mobile,'');
        $personnel->present_address =strip_tags($request->present_address,'');
        $personnel->permanent_address = strip_tags($request->permanent_address,'');
        $personnel->block_muni_temp_id = strip_tags($request->block_muni_temp_id,'');
        $personnel->block_muni_perm_id = strip_tags($request->block_muni_perm_id,'');
        $personnel->block_muni_off_id = strip_tags($request->block_muni_off_id,'');

        $personnel->epic = strip_tags($request->epic,'');
        $personnel->part_no = strip_tags($request->part_no,'');
        $personnel->sl_no = strip_tags($request->sl_no,'');
        $personnel->assembly_temp_id = strip_tags($request->assembly_temp_id,'');
        $personnel->assembly_perm_id = strip_tags($request->assembly_perm_id,'');
        $personnel->assembly_off_id = strip_tags($request->assembly_off_id,'');
       
        $personnel->post_office_account = strip_tags($request->post_office_account,'');
        $personnel->branch_ifsc = strip_tags($request->branch_ifsc,'');
        $personnel->bank_account_no = strip_tags($request->bank_account_no,'');
        $personnel->remark_id =strip_tags($request->remark_id,'');
        $personnel->district_id = substr($officeid,0,2);
        $personnel->subdivision_id = substr($officeid,0,4);
        $personnel->remark_reason = strip_tags($request->remark_reason,'');
        $personnel->pay_level = strip_tags($request->pay_level,'');
        $personnel->created_at =date('Y-m-d H:i:s');
        $personnel->updated_at =date('Y-m-d H:i:s');
        $personnel->save();

        return response()->json($personnel->id,201);
        }else{

        return response()->json("Total Number Exceeded",401);
        }

    }else{
        return response()->json("Please Update Office Data First",401);
    }
    }



    public function update(Request $request)
    {


      $personnelId= $this->getpersonnelid($request->token,$request->office_id);


       if($personnelId!='' ){

         $request->validate([
             'officer_name' => 'required|string|max:50',
             'designation' => 'required|string|max:50',
             'phone'=> 'max:15',
             'present_address' => 'required|string|max:100',
             'permanent_address' => 'required|string|max:100',
             'dob' => 'required|date',
             'gender' => 'required',
             'scale' => 'required|max:15',
             'basic_pay' => 'required|numeric|max:9999999',
             //'grade_pay' => 'required|numeric',
             'emp_group' => 'required',
             'working_status' => 'required',
             'mobile' => 'required|digits:10',
             'qualification_id' => 'required',
             'language_id' => 'required',
             'epic' => 'required|max:20',
             'part_no' => 'numeric|max:9999',
             'sl_no' => 'numeric|max:9999',
             'assembly_temp_id' => 'required|numeric',
             'assembly_perm_id' => 'required|numeric',
             'assembly_off_id' => 'required|numeric',
             'block_muni_temp_id' => 'numeric',
             'block_muni_temp_id' => 'numeric',
             'block_muni_temp_id' => 'numeric',
             'branch_ifsc' => 'required|max:11',
             'bank_account_no' => 'required|unique:personnel,bank_account_no,'.$personnelId.'|max:16'



         ]);

        $personnel =Personnel::find($personnelId);

        $personnel->id = strip_tags($personnelId);


        if($this->level===10){
			$personnel->office_id = $this->userID;
		}else{
        $personnel->office_id = strip_tags($request->office_id,'');

		}
        $personnel->name = strip_tags($request->officer_name,'');
        $personnel->designation = strip_tags($request->designation,'');
        $personnel->aadhaar = strip_tags($request->aadhaar,'');
        $personnel->qualification_id = strip_tags($request->qualification_id,'');
        $personnel->language_id = strip_tags($request->language_id,'');
        $personnel->dob = strip_tags($request->dob,'');
        $personnel->gender = strip_tags($request->gender,'');

        $personnel->scale = strip_tags($request->scale,'');
        $personnel->basic_pay = strip_tags($request->basic_pay,'');
        $personnel->grade_pay = strip_tags($request->grade_pay,'');
        $personnel->working_status = strip_tags($request->working_status,'');
        $personnel->emp_group = strip_tags($request->emp_group,'');

        $personnel->email = strip_tags($request->email,'');
        $personnel->phone = strip_tags($request->phone,'');
        $personnel->mobile = strip_tags($request->mobile,'');
        $personnel->present_address =  strip_tags($request->present_address,'');
        $personnel->permanent_address =strip_tags($request->permanent_address,'');
        $personnel->block_muni_temp_id = strip_tags($request->block_muni_temp_id,'');
        $personnel->block_muni_perm_id =  strip_tags($request->block_muni_perm_id,'');
        $personnel->block_muni_off_id =  strip_tags($request->block_muni_off_id,'');

        $personnel->epic = strip_tags($request->epic,'');
        $personnel->part_no = strip_tags($request->part_no,'');
        $personnel->sl_no = strip_tags($request->sl_no,'');
        $personnel->assembly_temp_id = strip_tags($request->assembly_temp_id,'');
        $personnel->assembly_perm_id = strip_tags($request->assembly_perm_id,'');
        $personnel->assembly_off_id = strip_tags($request->assembly_off_id,'');
        
        $personnel->post_office_account = strip_tags($request->post_office_account,'');
        $personnel->branch_ifsc = strip_tags($request->branch_ifsc,'');
        $personnel->bank_account_no = strip_tags($request->bank_account_no,'');
        $personnel->remark_id =strip_tags($request->remark_id,'');
        if($this->level===10){
			$personnel->district_id = substr($this->userID,0,2);
			$personnel->subdivision_id = substr($this->userID,0,4);
		}else{
        $personnel->district_id = substr($request->office_id,0,2);
        $personnel->subdivision_id = substr($request->office_id,0,4);
		}
        $personnel->remark_reason = strip_tags($request->remark_reason,'');
        $personnel->pay_level = strip_tags($request->pay_level,'');
        $personnel->updated_at =date('Y-m-d H:i:s');
        
        $personnel->save();

        $personnelId=0;
        return response()->json($personnel->id,201);
       }else{

        return response()->json('Unauthorize Access Denied',401);
       }


    }
    public function getRemarks(){

		$remarks=DB::select('SELECT id,name FROM `remarks` where id not in(13) order by id asc');
		 return response()->json($remarks,201);
    }

    public function getIfsc(Request $request){
        $ifsc=$request->branch_ifsc;
		if(!empty($ifsc)){
        $remarks=DB::select('SELECT ifsc,bank,branch FROM `ifsc_code` where ifsc="'.$ifsc.'"');
        if(empty($remarks)){
        return response()->json('Your Bank not in WB',201);

        }else{
            return response()->json($remarks,201);
        }



    }else{
            return response()->json('please entered',201);

        }
    }
   public function is_personnelOffice_countMatch($officeId){
    $officeStuff="SELECT total_staff as officeStuff FROM `offices`  where district_id='".$this->district."' and id='".$officeId."'";
    $officeStuff = DB::select($officeStuff);
    $officeStuff=$officeStuff[0]->officeStuff;

       $sql='SELECT  count(o.id) as totalEmployee
						  FROM personnel p
						  inner join offices o on (o.id=p.office_id )
						  where o.district_id="'.$this->district.'" and p.office_id="'.$officeId.'"';

				$office = DB::select($sql);

        $totalEmployee=$office[0]->totalEmployee;
        if($officeStuff>$totalEmployee){
         return true;

        }else{

            return false;
        }



   }
    public function is_acPc_exists($officeId){
        $sql="SELECT ac_id as ac,pc_id as pc,subdivision_id,police_station_id,date(updated_at) as updated_at,agree as agree_pp1  FROM `offices`  where district_id='".$this->district."' and id='".$officeId."'";
        $acPc = DB::select($sql);
        $ac=$acPc[0]->ac;
        $pc=$acPc[0]->pc;
        $ps=$acPc[0]->police_station_id;
        $subdiv=$acPc[0]->subdivision_id;
        $updated_at=$acPc[0]->updated_at;
        $agree_pp1=$acPc[0]->agree_pp1;
      
        if($ac!='' && $pc!='' && $ps!='' && $subdiv!='' && $updated_at >='2018-12-06' &&  $agree_pp1==1){
            return true;
        }else{

            return false;
        }


    }



  public function duplicateBankAccount(Request $request){
    $accountNumber=$request->bankNumber;
   if( Personnel::where('bank_account_no', '=',$accountNumber)->exists()){

    return response()->json(array('status'=>201,'msg'=>'Account Exists'));

     }else{
    return response()->json(array('status'=>401,'msg'=>'Not Found'));
     }

  }

  public function getpersonnelid($hash,$office_id){
    $sql="select * from personnel where office_id='".$office_id."'";
    $res=DB::select($sql);

  foreach($res as $of){
        if(Hash::check($of->id , $hash)){
        return $of->id;
        }
  }
}

}
