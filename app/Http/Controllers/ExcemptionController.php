<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\PersonnelController;
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
    public function SearchForExemption(Request $request){
      if($this->level===12 ){
              $arr=array();
             if($request->mode=='office'){
            $arr['count']= PersonnelController::where([
                ['district_id', $this->district],
                ['office_id', $request->s]
            ])->count();

            $arr['excemptionList']=PersonnelController::select('id','office_id','name','designation')
            ->where([
                ['district_id', $this->district],
                ['office_id', $request->s]
            ])->get();
            return response()->json($arr,201);
         }elseif($request->mode=='personnel'){
            // $arr['count']= PersonnelController::where([
            //     ['district_id', $this->district],
            //     ['id', $request->s]
            // ])->count();

          $arr['excemptionList']=PersonnelController::select('id','office_id','name','designation')
            ->where([
                ['id',$request->s],
                ['district_id', $this->district],
            ])->get();
            return response()->json($arr,201);
        }elseif($request->mode=='remarks'){
            $arr['count']= PersonnelController::where([
                ['district_id', $this->district],
                ['remark_id', $request->s]
            ])->count();
            // $arr['excemptionList']= PersonnelController::select('id','office_id','name','designation')
            //     ->where([
            //     ['remark_id',$request->s],
            //     ['district_id', $this->district],
            // ])->get();
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
                                ['excempted' => 'Yes'],
                                ['exmp_category' => '1'],
                                ['excmp_reason' => $request->reason],
                                ];   
                        PersonnelController::where('office_id',$request->office_id)
                                        ->where('district_id', $this->district)
                                            ->update($update);
                        return response()->json('Successfully Updated',201);                    
                     }else{
                        $update = [
                            ['excempted' => 'Yes'],
                            ['exmp_category' => '1'],
                            ['excmp_reason' => $request->reason],
                            ];   
                    PersonnelController::whereIn('id',$request->personnl_selected)
                                    ->where('district_id', $this->district)
                                    ->update($update);   
                    return response()->json('Successfully Updated',201);   
                    }             
   }elseif($request->mode=='personnel' && $request->reason!=''){
                        $update = [
                            ['excempted' => 'Yes'],
                            ['exmp_category' => '2'],
                            ['excmp_reason' => $request->reason],
                            ];   
                    PersonnelController::where('id',$request->personnl_selected)
                                    ->where('district_id', $this->district)
                                    ->update($update);   
                    return response()->json('Successfully Updated',201);
   }elseif($request->mode=='remarks' && $request->reason!=''){
                        $update = [
                            ['excempted' => 'Yes'],
                            ['exmp_category' => '3'],
                            ['excmp_reason' => $request->reason],
                            ];   
                    PersonnelController::where('remark_id',$request->remark_id)
                                    ->where('district_id', $this->district)
                                    ->update($update); 
                    return response()->json('Successfully Updated',201);                     
   }else{
        return response()->json('No Mode Selected',401);
     }   
   }
 }


}
