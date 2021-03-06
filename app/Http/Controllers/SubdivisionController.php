<?php

namespace App\Http\Controllers;

use App\Subdivision;
use App\BlockMuni;

use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class SubdivisionController extends Controller
{
	 public function __construct()
    {	
		if(Auth::guard('api')->check()){
		$this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
		$this->district=auth('api')->user()->area;
		}
    }

    public function getSubdivisions(){
	
		if($this->level===3 || $this->level===4|| $this->level===5 || $this->level===12 || $this->level===8){//ADM,DM,DIO
		
			
			 $sdo=substr($this->userID,4,3);
			 $deo=substr($this->userID,11,3);
			if($sdo=='SDO' && $deo=='DEO'){
			 $subdivision_id=substr($this->userID,7,4);
			 return Subdivision::where('district_id',$this->district)
			 ->where('id',$subdivision_id)
			 ->get();
			}elseif($sdo=='SDO' && $deo=='OC0'){
				$subdivision_id=substr($this->userID,7,4);
				return Subdivision::where('district_id',$this->district)
				->where('id',$subdivision_id)
				->get();
			}else{
			return Subdivision::where('district_id',$this->district)->get();
			}
		}elseif($this->level===6){//SDO
			$subdivision_id=substr($this->userID,-4);
		  return Subdivision::where('district_id',$this->district)
		                    ->where('id',$subdivision_id)
							->get();
		}elseif($this->level===7){//BDO
		    $block_munis=substr($this->userID,-6,6);
			return BlockMuni::where('id' , $block_munis)
							     ->get();
		}else{
			return response()->json('Unauthorize Access',422);
		}
	}
	public function getBlockmuniBysubdivision(Request $request){
		if($this->level===3 || $this->level===4|| $this->level===5||$this->level===6 || $this->level===7  || $this->level===12 || $this->level===8){
        if($request->subdivision_id!=''){
		return BlockMuni::where('subdivision_id',$request->subdivision_id)->get();
		}
	}
	}

}
