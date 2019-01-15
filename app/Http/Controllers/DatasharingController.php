<?php
namespace App\Http\Controllers;

use Eloquent;
use Illuminate\Http\Request;
use App\Personnel;
use App\AssemblyConstituency;
use Illuminate\Support\Facades\DB;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class DatasharingController extends Controller
{
    //
    public function __construct(){  
        if(Auth::guard('api')->check()){
        $this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
        }
    }


    public function instructDataForShare(Request $request){
        $from_district=$request->from_district;
        $to_district=$request->to_district;
        $categroy=$request->categroy;
        $requirement=$request->requirement;

    }
    public function getAvailability(){
       $acList= AssemblyConstituency::with('assemblyParty')->get();
       return response()->json($acList,200);
    }

}
