<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Personnel;
use Illuminate\Support\Facades\DB;
class ExcemptionController extends Controller
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

    public function getExemptedList(){
         $arr['excemptedList']=Personnel::select('personnel.id','personnel.office_id','personnel.name','personnel.designation','personnel.mobile','personnel.exempted','personnel.exemp_type','personnel.exemp_reason','personnel.exemp_date','remarks.name as remark')
            ->leftJoin('remarks','remarks.id','=','personnel.remark_id')
            ->whereIn('exemp_type',array(1, 2, 3))
            ->where('district_id', $this->district)
            ->get();

            return response()->json($arr,201);
    }
    public function SearchForExemption(Request $request){
      if($this->level===12 ){
              $arr=array();
             if($request->mode=='office'){
            $arr['count']= Personnel::where('district_id', $this->district)
                                    ->where('office_id', $request->office_id)
                                    ->count();

            $arr['excemptionList']=Personnel::select('personnel.id','personnel.office_id','personnel.name','personnel.designation','personnel.mobile','personnel.exempted','personnel.exemp_type','personnel.exemp_reason','personnel.exemp_date','remarks.name as remark')
            ->join('remarks','remarks.id','=','personnel.remark_id')
            ->where('district_id', $this->district)
            ->where('office_id', $request->office_id)
            ->get();
            return response()->json($arr,201);

        }elseif($request->mode=='personnel'){

          $arr['excemptionList']=Personnel::select('personnel.id','personnel.office_id','personnel.name','personnel.designation','personnel.mobile','personnel.exempted','personnel.exemp_type','personnel.exemp_reason','personnel.exemp_date','remarks.name as remark')
            ->join('remarks','remarks.id','=','personnel.remark_id')
            ->where('personnel.id',$request->personnel_id)
            ->where('district_id', $this->district)
            ->get();
            return response()->json($arr,201);

        }elseif($request->mode=='remarks'){

            $arr['excemptionList']= Personnel::select('personnel.id','personnel.office_id','personnel.name','personnel.designation','personnel.mobile','personnel.exempted','personnel.exemp_type','personnel.exemp_reason','personnel.exemp_date','remarks.name as remark')
                                    ->join('remarks','remarks.id','=','personnel.remark_id')
                                    ->where('district_id', $this->district)
                                    ->where('subdivision_id',$request->subdivision_id)
                                    ->where('remark_id', $request->remark_id)
                                    ->get();
            return response()->json($arr,201);
        }else{
          //////
          return response()->json('No Mode Selected',401);
        }

    }

}
    public function doExcemption(Request $request){
    //
    if($this->level==12){
        if($request->mode=='office'){
                    if($request->personnl_selected=='ALL' && $request->office_id!='' && $request->reason!=''){
                        $update = [
                                'exempted' => 'Yes',
                                'exemp_type' => '1',
                                'exemp_reason' => $request->reason,
                                'exemp_date' => NOW(),
                                ];
                        Personnel::where('office_id',$request->office_id)
                                        ->where('district_id', $this->district)
                                            ->update($update);
                        return response()->json('Successfully Updated',201);
                     }else{
                        $update = [
                           'exempted' => 'Yes',
                           'exemp_type' => '1',
                           'exemp_reason' => $request->reason,
                           'exemp_date' => NOW(),
                            ];
                    Personnel::whereIn('id',$request->personnl_selected)
                                    ->where('district_id', $this->district)
                                    ->update($update);
                    return response()->json('Successfully Updated',201);
                    }
   }elseif($request->mode=='personnel' && $request->reason!=''){
                        $update = [
                           'exempted' => 'Yes',
                           'exemp_type' => '2',
                           'exemp_reason' => $request->reason,
                           'exemp_date' => NOW(),
                            ];
                    Personnel::where('id',$request->personnel_id)
                                    ->where('district_id', $this->district)
                                    ->WhereNull('exempted')
                                    ->update($update);
                    return response()->json('Successfully Updated',201);
   }elseif($request->mode=='remarks'){
       if($request->reason!='' && $request->remark_personnl_selected=='ALL'){
                        $update = [
                            'exempted' => 'Yes',
                            'exemp_type' => '3',
                            'exemp_reason' => $request->reason,
                            'exemp_date' => NOW(),
                            ];
                    Personnel::where('remark_id',$request->remark_id)
                                    ->where('district_id', $this->district)
                                    ->WhereNull('exempted')
                                    ->update($update);
                    return response()->json('Successfully Updated',201);
       }else{
        $update = [
            'exempted' => 'Yes',
            'exemp_type' => '3',
            'exemp_reason' => $request->reason,
            'exemp_date' => NOW(),
            ];
    Personnel::whereIn('id',$request->remark_personnl_selected)
                    ->where('remark_id',$request->remark_id)
                    ->where('district_id', $this->district)
                    ->update($update);
    return response()->json('Successfully Updated',201);
       }
   }else{
        return response()->json('No Mode Selected',401);
     }
   }
 }

 public function getRemarks(){

    $remarks=DB::select('SELECT id,name FROM `remarks` where id not in(99) order by id asc');
     return response()->json($remarks,201);
}

 public function revokeExcemption(Request $request){
if($this->level==12){  
                $personnelId=$request->personnel_id; 
                $update = [
                    'exempted' => NULL,
                    'exemp_type' => NULL,
                    'exemp_reason' => NULL,
                    'exemp_date' =>NULL,
                    ];
                Personnel::where('id',$personnelId)
                            ->where('district_id', $this->district)
                            ->update($update);
                return response()->json('Successfully Updated',201);             
            }else{
               return response()->json('Unauthenticated',401);  
         }
   }
}
