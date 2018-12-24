<?php

namespace App\Http\Controllers;

use App\AssemblyConstituency;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class AssemblyConstituencyController extends Controller{

 public function __construct()
    {	
        if(Auth::guard('api')->check()){
        $this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
        }
    }

    public function getAssemblies()
    {
        return AssemblyConstituency::where('district_id' , $this->district)->get();
    }
    public function getAssembliesAll()
    {
        
        return AssemblyConstituency::all();
    }


}
