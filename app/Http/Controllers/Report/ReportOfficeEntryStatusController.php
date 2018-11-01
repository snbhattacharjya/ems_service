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
    public function getOfficeStatus(){
          $status=DB::select("select id,name,mobile from offices where district_id='".$this->district."' and id not in(select office_id from personnel)");
          
          return response()->json($status,201);
    }





}
