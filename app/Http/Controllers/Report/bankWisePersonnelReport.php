<?php

namespace App\Http\Controllers\Report;

use \Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Personnel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class bankWisePersonnelReport extends Controller
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
 public function bankWiseReport(){
    (array)$ifscwise_count= DB::select('select substring(personnel.branch_ifsc,1,4) as ifsc,count(personnel.id) as noofperonnel from personnel where district_id="'.$this->district.'"
   group by substring(personnel.branch_ifsc,1,4)');

   (array)$distinct_bank= DB::select('select distinct bank as bank_name,substring(ifsc,1,4) as ifsc from ifsc_code where district_id="'.$this->district.'"');
     foreach($ifscwise_count as $report){
            foreach($distinct_bank as $requerment){
            if($requerment->ifsc==$report->ifsc){
                $report->bank_name=$requerment->bank_name;
              }else{

                $report->bank_name='Bank Not Found';
              }



            }
        }
       return response()->json($ifscwise_count,200);

}



}
