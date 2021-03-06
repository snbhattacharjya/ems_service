<?php

namespace App\Http\Controllers\categorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Personnel;
use Illuminate\Support\Facades\DB;
use App\PollingPost;
class ManualPoststatSetController extends Controller
{
    public function __construct()
    {
        if(Auth::guard('api')->check()){
        $this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
        }
    }
    public function GetPersonnelByOfficeAndPoststat(Request $request){//acccessable only district level
       $office_id=$request->office_id;
       $post_stat=$request->post_stat;
       if(!empty($office_id) && empty($post_stat) && ($this->level===3 || $this->level===4|| $this->level===12)){
                return Personnel:: where('district_id','=',$this->district)
                                    ->where('office_id' ,'=',$office_id)
                                    ->get();
       }else if(!empty($office_id) && !empty($post_stat) && ($this->level===3 || $this->level===4|| $this->level===12)){
                return Personnel:: where('district_id','=',$this->district)
                ->where('office_id' ,'=',$office_id)
                ->where('post_stat' ,'=',$post_stat)
                ->get();
       }else{
        return response()->json('Error',400);
       }
    }
  public function postStatManualSave(Request $request){

            $office_id=$request->office_id;
            $personnelId=$request->personnel_id;
            $postStat=$request->poststat;

if(!empty($personnelId)  && !empty($office_id) && ($this->level===3 || $this->level===4|| $this->level===12 || $this->level===8)){
                Personnel:: where('district_id','=',$this->district)
                                ->where('id' ,'=',$personnelId)
                                ->where('office_id' ,'=',$office_id)
                                ->update(['post_stat'=>$postStat]);
                return response()->json('Successfully Updated',201);
     }else{

                return response()->json('Error',400);
        }


        }

        public function GetPersonnelByPoststat(Request $request){//acccessable only district level
         $post_stat=$request->post_stat;
         if(!empty($request->gender)){
            return DB::table('personnel')->select(DB::raw("personnel.id,personnel.office_id,personnel.name,personnel.designation,personnel.mobile,personnel.designation,personnel.basic_pay,personnel.pay_level,personnel.grade_pay,personnel.emp_group,personnel.dob,personnel.gender,personnel.post_stat,remarks.name as remark,categories.name as office_category,offices.name as office_name,offices.address as office_address,YEAR('2019-05-31') - YEAR(personnel.dob) - IF(STR_TO_DATE(CONCAT(YEAR('2019-05-31'), '-', MONTH(personnel.dob), '-', DAY(personnel.dob)) ,'%Y-%c-%e') > '2019-05-31', 1, 0) AS age"))
            ->leftJoin('remarks','remarks.id','=','personnel.remark_id')
            ->leftJoin('offices','offices.id','=','personnel.office_id')
            ->leftJoin('categories','categories.id','=','offices.category_id')
             ->where('personnel.district_id','=',$this->district)
             ->where('personnel.post_stat' ,'=',$post_stat)
             ->where('personnel.gender' ,'=',$request->gender)
             ->whereNull('personnel.exempted')
             ->get();


         }else{

                 return DB::table('personnel')->select(DB::raw("personnel.id,personnel.office_id,personnel.name,personnel.designation,personnel.mobile,personnel.designation,personnel.basic_pay,personnel.pay_level,personnel.grade_pay,personnel.emp_group,personnel.dob,personnel.gender,personnel.post_stat,remarks.name as remark,categories.name as office_category,offices.name as office_name,offices.address as office_address,YEAR('2019-05-31') - YEAR(personnel.dob) - IF(STR_TO_DATE(CONCAT(YEAR('2019-05-31'), '-', MONTH(personnel.dob), '-', DAY(personnel.dob)) ,'%Y-%c-%e') > '2019-05-31', 1, 0) AS age"))
                ->leftJoin('remarks','remarks.id','=','personnel.remark_id')
                ->leftJoin('offices','offices.id','=','personnel.office_id')
                ->leftJoin('categories','categories.id','=','offices.category_id')
                 ->where('personnel.district_id','=',$this->district)
                 ->where('personnel.post_stat' ,'=',$post_stat)
                 ->whereNull('personnel.exempted')
                 ->get();

            }

         }
      public function getPPListByDistinctDesignation(Request $request){
        if($this->level==12 || $this->level==8 || $this->level==5 ){
         $sql='select distinct(designation),count(post_stat) as poststatcount,post_stat from personnel where district_id="'.$this->district.'" and  post_stat="'.$request->post_stat.'" and gender="M" and exempted is NULL and to_district is NULL group by designation';
         return DB::select($sql);
        }else{
            return response()->json('Unathunticated',401);
        }
      }
     public function createAdhocRule(Request $request){
    if($this->level==12 || $this->level==8 || $this->level==5 ){
      $designation=$request->designation;
      $current_poststat=$request->current_poststat;
      $change_to_poststat=$request->change_to_poststat;
      $update = [
        'post_stat' => $change_to_poststat,
        ];
    Personnel:: where('post_stat', $current_poststat)
                ->where('designation', $designation)
                ->where('district_id', $this->district)
                ->where('gender','M')
                ->update($update);
                return response()->json('Successfully Updated',201);
            }else{
                return response()->json('Unathunticated',401);
            }
     }
  public function bulkUpdateByPostStatType(Request $request){
    if($this->level==12 || $this->level==8 || $this->level==5 ){
    if($request->personnl_selected=='ALL' && $request->poststat_to!='' && $request->poststat_from!=''){
        $update = ['post_stat' => $request->poststat_to];

        Personnel::where('post_stat',$request->poststat_from)
                 ->where('district_id', $this->district)
                ->update($update);
        return response()->json('Successfully Updated',201);
    }else{
    $update = ['post_stat' => $request->poststat_to];
    foreach($request->personnl_selected as $p){
     Personnel::where('id',$p)
                 ->where('district_id', $this->district)
                 ->update($update);
    }
     return response()->json('Successfully Updated',201);
    }
  }else{
    return response()->json('Unauthorize',401);
  }

 }
}
