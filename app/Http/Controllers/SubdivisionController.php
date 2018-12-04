<?php

namespace App\Http\Controllers;

use App\Subdivision;
use App\BlockMuni;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
class SubdivisionController extends Controller
{
	 public function __construct()
    {	$this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
    }

    public function getSubdivisions(){
		if($this->level===3 || $this->level===4|| $this->level===5 || $this->level===12 || $this->level===8){//ADM,DM,DIO
           return Subdivision::where('district_id',$this->district)->get();
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
}
