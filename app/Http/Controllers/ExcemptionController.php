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
             if($request->mode=='office'){
            return PersonnelController::where([
                ['district_id', $this->district],
                ['office_id', $request->s]
            ])->get();
        }elseif($request->mode=='office'){
            return PersonnelController::where([
                ['id',$request->s],
                ['district_id', $this->district],
            ])->get();
        }elseif($request->mode=='office'){
                return PersonnelController::where([
                ['remark_id',$request->s],
                ['district_id', $this->district],
            ])->get();
        }else{
          //////
          return response()->json('No Mode Selected',401);
        }
    
    }

}
    public function doExcemption(Request $request){
    // 
    

    }


}
