<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RemarksWiseController extends Controller
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
    public function RemarksWisePersonnelStatus(){
        if($this->level==3 || $this->level==4 || $this->level==5 || $this->level==12 ||$this->level==8 ){
    $sql='SELECT r.name,
    SUM(CASE WHEN p.remark_id = r.id and p.gender="M"  THEN 1 ELSE 0 END) AS male, 
    SUM(CASE WHEN p.remark_id = r.id and p.gender="F" THEN 1 ELSE 0 END) AS female,
    SUM(CASE WHEN p.remark_id = r.id  THEN 1 ELSE 0 END) AS total
    from personnel p join remarks r on  r.id=p.remark_id where p.district_id="'.$this->district.'" group by r.name order by r.id';

  (array)$reportRemark['available']=DB::select($sql);	
  return response()->json($reportRemark,200);
        }
    }


}
