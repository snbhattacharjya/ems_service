<?php

namespace App\Http\Controllers;
use app\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ReportController extends Controller
{
    public function __construct()
    {	
	   $this->userID=auth('api')->user()->user_id;
       $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
    }
   public function getReport(){
	  $arr=array();
	 if($this->district=='' & ($this->userID="WBCEO" || $this->userID=="WBCEONODAL")){
		//For Wb CEO
		$sqlAvailable='SELECT d.name,p.gender,
						SUM(CASE WHEN p.post_stat = "MO" THEN 1 ELSE 0 END) AS MO, 
						SUM(CASE WHEN p.post_stat = "P1" THEN 1 ELSE 0 END) AS P1, 
						SUM(CASE WHEN p.post_stat = "P2" THEN 1 ELSE 0 END) AS P2,
						SUM(CASE WHEN p.post_stat = "P3" THEN 1 ELSE 0 END) AS P3, 
						SUM(CASE WHEN p.post_stat = "PR" THEN 1 ELSE 0 END) AS PR
						FROM personnel p inner join districts d on d.id=p.district_id 
						group by p.district_id,d.name,p.gender';
		
		$reportAvailable=DB::select($sqlAvailable);
		$arr['available']=$reportAvailable;
		$sqlRequirement='SELECT d.name,sum(ap.party_count) as party from districts d 
						inner join assembly_constituencies ac on (ac.district_id=d.id)
						inner join assembly_party ap on (ap.assembly_id=ac.id) 
						group by d.id,d.name';
						
		$reportRequirement=DB::select($sqlRequirement);				
		$arr['requirement']=$reportRequirement;
		return response()->json($arr,200);
	 }else if($this->district!='' & $this->level===3){// For District User
		
		$sqlAvailable='SELECT d.name,p.gender,
						SUM(CASE WHEN p.post_stat = "MO" THEN 1 ELSE 0 END) AS MO, 
						SUM(CASE WHEN p.post_stat = "P1" THEN 1 ELSE 0 END) AS P1, 
						SUM(CASE WHEN p.post_stat = "P2" THEN 1 ELSE 0 END) AS P2,
						SUM(CASE WHEN p.post_stat = "P3" THEN 1 ELSE 0 END) AS P3, 
						SUM(CASE WHEN p.post_stat = "PR" THEN 1 ELSE 0 END) AS PR
						FROM personnel p inner join districts d on d.id=p.district_id 
						and d.id="'.$this->district.'" group by p.district_id,d.name,p.gender';
						
		$reportAvailable=DB::select($sqlAvailable);	
        $arr['available']=$reportAvailable;		
	 
	     $sqlRequirement='SELECT d.name,sum(ap.party_count) as party from districts d 
						inner join assembly_constituencies ac on (ac.district_id=d.id)
						inner join assembly_party ap on (ap.assembly_id=ac.id) and d.id="'.$this->district.'"
						group by d.id,d.name';
	 
	        $reportRequirement=DB::select($sqlRequirement);	
	       $arr['requirement']=$reportRequirement;
	       return response()->json($arr,200);
	 
	 }else{
		return response()->json("Unauthorize Access",200);   
		 
	 }
	
	//print_r($arr);
	
     }	
	

 }

 