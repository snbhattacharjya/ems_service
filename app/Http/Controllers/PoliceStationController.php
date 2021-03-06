<?php

namespace App\Http\Controllers;

use App\PoliceStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class PoliceStationController extends Controller
{
	 public function __construct()
    {	
        if(Auth::guard('api')->check()){
        $this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
        }
    }

    public function getPoliceStations(Request $request)
    {

        if($request->subdivision_id && $request->subdivision_id!='' && $request->subdivision_id!='all'){
         return DB::select("select id,name,subdivision_id from police_stations where subdivision_id='".$request->subdivision_id."' and  SUBSTRING(id,1,2)= ?",[$this->district]);
        }elseif($request->subdivision_id=='all'){
        return DB::select("select id,name,subdivision_id from police_stations where  SUBSTRING(id,1,2)= ?",[$this->district]);
        }else{
            return DB::select("select id,name,subdivision_id from police_stations where  SUBSTRING(id,1,2)= ?",[$this->district]);

        }

    }


}
