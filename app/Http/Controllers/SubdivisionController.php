<?php

namespace App\Http\Controllers;

use App\Subdivision;
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
        return Subdivision::where('district_id' , $this->district)->get();
    }
}
