<?php

namespace App\Http\Controllers;
use App\DistrictPPdata;
use App\Office;
use App\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistrictPPdataController extends Controller
{
    //
	    public function __construct(){
            $this->userID=auth('api')->user()->user_id;
			$this->level=auth('api')->user()->level;
			$this->district=auth('api')->user()->area;
		    }
		

}