<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AnalyticsController extends Controller
{
    //

    public function __construct()
    {	
	   $this->userID=auth('api')->user()->user_id;
       $this->level=auth('api')->user()->level;
      $this->district=auth('api')->user()->area;
    }

    public function totalUsers(){

     $sql="SELECT 
     SUM(CASE WHEN id IS NOT NULL THEN 1 ELSE 0 END) AS total_user
    ,SUM(CASE WHEN change_password==1  THEN 1 ELSE 0 END) AS userLogging 
    FROM users where area='$this->district' ";  
    $totalUser=DB::select($sql);
     return $totalUser;  
    }


}
