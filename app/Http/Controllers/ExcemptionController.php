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
         $arr['excemptedList']=Personnel::select('personnel.id','personnel.office_id','offices.name as officename','personnel.name','personnel.designation','personnel.mobile','personnel.exempted','personnel.exemp_type','personnel.exemp_reason','personnel.exemp_date','remarks.name as remark')
            ->leftJoin('remarks','remarks.id','=','personnel.remark_id')
            ->leftJoin('offices','offices.id','=','personnel.office_id')
            ->whereIn('personnel.exemp_type',array(1, 2, 3))
            ->where('personnel.district_id', $this->district)
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

            $arr['excemptionList']=Personnel::select('personnel.id','personnel.office_id','offices.name as officename','personnel.name','personnel.designation','personnel.mobile','personnel.exempted','personnel.exemp_type','personnel.exemp_reason','personnel.exemp_date','remarks.name as remark')
            ->leftJoin('remarks','remarks.id','=','personnel.remark_id')
            ->leftJoin('offices','offices.id','=','personnel.office_id')
            ->where('personnel.district_id', $this->district)
            ->where('personnel.office_id', $request->office_id)
            ->get();
            return response()->json($arr,201);

        }elseif($request->mode=='personnel'){

          $arr['excemptionList']=Personnel::select('personnel.id','personnel.office_id','offices.name as officename','personnel.name','personnel.designation','personnel.mobile','personnel.exempted','personnel.exemp_type','personnel.exemp_reason','personnel.exemp_date','remarks.name as remark')
            ->leftJoin('remarks','remarks.id','=','personnel.remark_id')
            ->leftJoin('offices','offices.id','=','personnel.office_id')
            ->where('personnel.id',$request->personnel_id)
            ->where('personnel.district_id', $this->district)
            ->get();
            return response()->json($arr,201);

        }elseif($request->mode=='remarks'){

            $arr['excemptionList']= Personnel::select('personnel.id','personnel.office_id','offices.name as officename','personnel.name','personnel.designation','personnel.mobile','personnel.exempted','personnel.exemp_type','personnel.exemp_reason','personnel.exemp_date','remarks.name as remark')
                                    ->leftJoin('remarks','remarks.id','=','personnel.remark_id')
                                    ->leftJoin('offices','offices.id','=','personnel.office_id')
                                    ->where('personnel.district_id', $this->district)
                                    ->where('personnel.subdivision_id',$request->subdivision_id)
                                    ->where('personnel.remark_id', $request->remark_id)
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
   }elseif($request->mode=='age'){
    if($request->reason!='' && $request->personnl_selected=='ALL'){
                // $update = [
                //     'exempted' => 'Yes',
                //     'exemp_type' => '4',
                //     'exemp_reason' => $request->reason,
                //     'exemp_date' => NOW(),
                //     ];
        $exemp_date =NOW();
        $sql="update personnel set exempted='Yes',exemp_type='4',exemp_reason='.$request->reason.',exemp_date='.$exemp_date.'  where district_id='.$this->district.' and  AND YEAR('2019-05-31') - YEAR(personnel.dob) - IF(STR_TO_DATE(CONCAT(YEAR('2019-05-31'), '-', MONTH(personnel.dob), '-', DAY(personnel.dob)) ,'%Y-%c-%e') > '2019-05-31', 1, 0) >59";
        DB::select($sql);
            return response()->json('Successfully Updated',201);
        }else{
        $update = [
        'exempted' => 'Yes',
        'exemp_type' => '4',
        'exemp_reason' => $request->reason,
        'exemp_date' => NOW(),
        ];
        Personnel::whereIn('id',$request->personnl_selected)
            ->where('district_id', $this->district)
            ->update($update);
        return response()->json('Successfully Updated',201);
        }
    }elseif($request->mode=='designation'){
        if($request->reason!='' && $request->personnl_selected=='ALL'){
                    $update = [
                        'exempted' => 'Yes',
                        'exemp_type' => '5',
                        'exemp_reason' => $request->reason,
                        'exemp_date' => NOW(),
                        ];

                    Personnel::where('district_id', $this->district)
                             ->where('designation','like','%'.$request->designation.'%')
                             ->update($update);
                             return response()->json('Successfully Updated',201);

            }else{
            $update = [
            'exempted' => 'Yes',
            'exemp_type' => '5',
            'exemp_reason' => $request->reason,
            'exemp_date' => NOW(),
            ];
            Personnel::whereIn('id',$request->personnl_selected)
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

  public function getExcemptionByAge(){
    if($this->level==12){
        $arr['excemptionList']=DB::select("SELECT personnel.id,personnel.office_id,offices.name as officename,
                              personnel.name,personnel.designation,personnel.mobile,
                              personnel.exempted,personnel.exemp_type,personnel.exemp_reason,
                              personnel.exemp_date,remarks.name as remark,YEAR('2019-05-31') - YEAR(personnel.dob) - IF(STR_TO_DATE(CONCAT(YEAR('2019-05-31'), '-', MONTH(personnel.dob), '-', DAY(personnel.dob)) ,'%Y-%c-%e') > '2019-05-31', 1, 0) as age FROM `personnel`
                              left join remarks on remarks.id=personnel.remark_id
                              left join offices on offices.id=personnel.office_id
                              WHERE personnel.district_id='".$this->district."' and exempted is NULL
                              and YEAR('2019-05-31') - YEAR(personnel.dob) - IF(STR_TO_DATE(CONCAT(YEAR('2019-05-31'), '-', MONTH(personnel.dob), '-', DAY(personnel.dob)) ,'%Y-%c-%e') > '2019-05-31', 1, 0)>59");
                              return response()->json($arr,201);
    }
  }

 public function getExemptionListByDesignation(Request $request){

    $arr['excemptionList']= Personnel::select('personnel.id','personnel.office_id','offices.name as officename','personnel.name','personnel.designation','personnel.mobile','personnel.exempted','personnel.exemp_type','personnel.exemp_reason','personnel.exemp_date','remarks.name as remark')
    ->leftJoin('remarks','remarks.id','=','personnel.remark_id')
    ->leftJoin('offices','offices.id','=','personnel.office_id')
    ->where('personnel.district_id', $this->district)
    ->where('designation','like','%'.$request->designation.'%')
    ->get();
     return response()->json($arr,201);
    }


public function revokeExemptionByType(Request $request){

    if($this->level==12){
        $exemp_type=$request->exemp_type;
        $update = [
            'exempted' => NULL,
            'exemp_type' => NULL,
            'exemp_reason' => NULL,
            'exemp_date' =>NULL,
            ];

        if(Personnel::where('exemp_type',$exemp_type)->where('district_id',$this->district)->exists()){
        Personnel::where('exemp_type',$exemp_type)
                    ->where('district_id', $this->district)
                    ->update($update);
            return response()->json('Successfully Updated',201);
        }else{
            return response()->json('No Data Available to Revoke',201);
        }
    }else{
           return response()->json('Unauthenticated',401);
    }
 }




}
