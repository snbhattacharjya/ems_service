<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class ReportOfficeEntryStatusController extends Controller
{
    //
    public function __construct()
    {	$this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
    }
    public function getOfficeEntryStatus(){
        if($this->level==3 || $this->level==4 || $this->level==5 || $this->level==12 ){
          $status=DB::select("select id,name,mobile,identification_code,address,post_office,pin from offices where district_id='".$this->district."' and id not in(select office_id from personnel)");
          return response()->json($status,201);
         }else if($this->level==6){
          $subdivision_id= substr($this->userID,7,10);
          $status=DB::select("select id,name,mobile,identification_code,address,post_office,pin from offices where district_id='".$this->district."' and subdivision_id='".$subdivision_id."'  and id not in(select office_id from personnel)");
          return response()->json($status,201);

         }else if($this->level==7){
            $block_muni_id= substr($this->userID,7,10);
            $status=DB::select("select id,name,mobile,identification_code,address,post_office,pin from offices where district_id='".$this->district."' and block_muni_id='".$block_muni_id."'  and id not in(select office_id from personnel)");
            return response()->json($status,201);
         }else{

         }
       }

       public function getOfficePartialEntryStatus(){
       $offices=DB::select("select distinct(offices.id) as officeId,offices.name as officeName,offices.mobile,offices.total_staff as totalStuff from offices join personnel on offices.id=personnel.office_id where offices.district_id='$this->district'");
       for($i=0;$i<count($offices);$i++){
        $personnel=DB::select("select count(personnel.id) as totpersonnel  from offices join personnel on offices.id=personnel.office_id where offices.district_id='$this->district'  and personnel.office_id=".$offices[$i]->officeId);
        // print_r($personnel->totpersonnel);
        if($offices[$i]->totalStuff>$personnel[0]->totpersonnel && $personnel[0]->totpersonnel!=''){
       $arr['officelist'][]=array('officeId'=>$offices[$i]->officeId,'officeName'=>$offices[$i]->officeName,
           'mobile'=>$offices[$i]->mobile,'totalStuff'=>$offices[$i]->totalStuff ,'personelenty'=>$personnel[0]->totpersonnel );
       }
    }
    $arr['totalpartialoffice']=count($arr['officelist']);
       return response()->json($arr,201);
       }

}
