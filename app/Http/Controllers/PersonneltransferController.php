<?php

namespace App\Http\Controllers;
use App\Personnel;
use App\Personneltransfer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PersonneltransferController extends Controller
{
    //
    public function __construct()
    {
        if(Auth::guard('api')->check()){
        $this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
        }
    }


   public function getTransferList(){
    if($this->level===12 ){
    return Personnel::select('personnel.id','personnel.office_id','personnel.name','personnel.designation',
                             'personnel.email','personnel.mobile','personnel.bank_account_no','officeBlock.name as block','peresentBlock.name as permblock')
                      ->join('block_munis as officeBlock', 'personnel.block_muni_off_id', '=', 'officeBlock.id')
                      ->join('block_munis as peresentBlock', 'personnel.block_muni_temp_id	', '=', 'peresentBlock.id')
                      ->where('district_id',$this->district)
                      ->where('remark_id',17)
                      ->get();
     }else{
        return response()->json('Unauthenticat',201);
    }
    }
    public function doTransfer(Request $request){
        if($this->level===12 ){
        $personnel =Personnel::where('district_id',$this->district)
                                ->where('id',$request->personnel_id)
                                ->where('remark_id',17)
                                ->get();
        if(!empty($personnel)){
        $Personneltransfer=new Personneltransfer;
        $Personneltransfer->id=$personnel[0]->id;
        $Personneltransfer->office_id=$personnel[0]->office_id;
        $Personneltransfer->name = strip_tags($personnel[0]->officer_name,'');
        $Personneltransfer->designation = strip_tags($personnel[0]->designation,'');
       // $personnel->aadhaar = $personnel[0]->aadhaar;
        $Personneltransfer->qualification_id = strip_tags($personnel[0]->qualification_id,'');
        $Personneltransfer->language_id = strip_tags($personnel[0]->language_id,'');
        $Personneltransfer->dob = strip_tags($personnel[0]->dob,'');
        $Personneltransfer->gender = strip_tags($personnel[0]->gender,'');
        $Personneltransfer->scale = strip_tags($personnel[0]->scale,'');
        $Personneltransfer->basic_pay = strip_tags($personnel[0]->basic_pay,'');
        $Personneltransfer->grade_pay = strip_tags($personnel[0]->grade_pay,'');
        $Personneltransfer->working_status = strip_tags($personnel[0]->working_status,'');
        $Personneltransfer->emp_group = strip_tags($personnel[0]->emp_group,'');
        $Personneltransfer->email = strip_tags($personnel[0]->email,'');
        $Personneltransfer->phone = strip_tags($personnel[0]->phone,'');
        $Personneltransfer->mobile = strip_tags($personnel[0]->mobile,'');
        $Personneltransfer->present_address =strip_tags($personnel[0]->present_address,'');
        $Personneltransfer->permanent_address = strip_tags($personnel[0]->permanent_address,'');
        $Personneltransfer->block_muni_temp_id = strip_tags($personnel[0]->block_muni_temp_id,'');
        $Personneltransfer->block_muni_perm_id = strip_tags($personnel[0]->block_muni_perm_id,'');
        $Personneltransfer->block_muni_off_id = strip_tags($personnel[0]->block_muni_off_id,'');
        $Personneltransfer->epic = strip_tags($personnel[0]->epic,'');
        $Personneltransfer->part_no = strip_tags($personnel[0]->part_no,'');
        $Personneltransfer->sl_no = strip_tags($personnel[0]->sl_no,'');
        $Personneltransfer->assembly_temp_id = strip_tags($personnel[0]->assembly_temp_id,'');
        $Personneltransfer->assembly_perm_id = strip_tags($personnel[0]->assembly_perm_id,'');
        $Personneltransfer->assembly_off_id = strip_tags($personnel[0]->assembly_off_id,'');
        $Personneltransfer->branch_ifsc = strip_tags($personnel[0]->branch_ifsc,'');
        $Personneltransfer->bank_account_no = strip_tags($personnel[0]->bank_account_no,'');
        $Personneltransfer->remark_id =strip_tags($personnel[0]->remark_id,'');
        $Personneltransfer->district_id =  $this->district;
        $Personneltransfer->subdivision_id =$personnel[0]->subdivision_id;
        $Personneltransfer->remark_reason = strip_tags($personnel[0]->remark_reason,'');
        $Personneltransfer->pay_level = strip_tags($personnel[0]->pay_level,'');
        $Personneltransfer->created_at =date('Y-m-d H:i:s');
        $Personneltransfer->memo_date =strip_tags($request->memo_date,'');
        $Personneltransfer->memo_no =strip_tags($request->memo_no,'');
        $Personneltransfer->save();
        if($Personneltransfer->id){
        Personnel::where('id',$request->personnel_id)->delete();
        $arr=array('msg'=>'Successfully Deleted','id'=>$request->personnel_id);
        return response()->json($arr,201);
        }else{
            $arr=array('msg'=>'Can not Delete,Please Try Again','id'=>$request->personnel_id);
            return response()->json($arr,201);
        }
       }else{
        $arr=array('msg'=>'Can not find Personnel,Please Try Again','id'=>$request->personnel_id);
        return response()->json($arr,201);
    }


    }else{
        return response()->json('Unauthenticat',201);
    }
}

}
