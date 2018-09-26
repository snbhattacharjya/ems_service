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
	//echo 'For Wb CEO';exit;
		$sqlAvailable='SELECT d.name,p.district_id,
                    SUM(CASE WHEN p.post_stat = "MO" and p.gender="M"  THEN 1 ELSE 0 END) AS MO_M, 
                    SUM(CASE WHEN p.post_stat = "P1" and p.gender="M" THEN 1 ELSE 0 END) AS P1_M, 
                    SUM(CASE WHEN p.post_stat = "P2" and p.gender="M" THEN 1 ELSE 0 END) AS P2_M,
                    SUM(CASE WHEN p.post_stat = "P3" and p.gender="M" THEN 1 ELSE 0 END) AS P3_M, 
                    SUM(CASE WHEN p.post_stat = "PR" and p.gender="M" THEN 1 ELSE 0 END) AS PR_M,
                    SUM(CASE WHEN p.post_stat = "MO" and p.gender="F"  THEN 1 ELSE 0 END) AS MO_F, 
                    SUM(CASE WHEN p.post_stat = "P1" and p.gender="F" THEN 1 ELSE 0 END) AS P1_F, 
                    SUM(CASE WHEN p.post_stat = "P2" and p.gender="F" THEN 1 ELSE 0 END) AS P2_F,
                    SUM(CASE WHEN p.post_stat = "P3" and p.gender="F" THEN 1 ELSE 0 END) AS P3_F, 
                    SUM(CASE WHEN p.post_stat = "PR" and p.gender="F" THEN 1 ELSE 0 END) AS PR_F
                    FROM personnel p inner join districts d on d.id=p.district_id 
                    group by p.district_id,d.name order by p.district_id ';
		
		
		
		(array)$reportAvailable=DB::select($sqlAvailable);
		$arr['available']=$reportAvailable;
		
		$sqlRequirement='SELECT d.name,sum(ap.male_party_count) as male_party_count,sum(ap.female_party_count) as female_party_count from districts d 
						inner join assembly_constituencies ac on (ac.district_id=d.id)
						inner join assembly_party ap on (ap.assembly_id=ac.id) 
						group by d.id,d.name';
						
		(array)$reportRequirement=DB::select($sqlRequirement);	
		
		//$arr['requirement']=$reportRequirement;
		 foreach($reportAvailable as $report){
			 foreach($reportRequirement as $requerment){
			   if($requerment->name==$report->name){
				  if(!$requerment->male_party_count || $requerment->male_party_count==''){
					  $report->male_party=$requerment->male_party_count;
				  }else{
					  $report->male_party=$requerment->male_party_count;
				  }
				 if(!$requerment->female_party_count || $requerment->female_party_count==''){
					  $report->female_party=$requerment->female_party_count;
				  }else{
					  $report->female_party=$requerment->female_party_count;
				  }  
			   }
			  
			 }
		   }
		return response()->json($reportAvailable,200);
	 }else if($this->district!='' & $this->level===3){// For District User
		
		 $sqlAvailable='SELECT d.name,
                    SUM(CASE WHEN p.post_stat = "MO" and p.gender="M"  THEN 1 ELSE 0 END) AS MO_M, 
                    SUM(CASE WHEN p.post_stat = "P1" and p.gender="M" THEN 1 ELSE 0 END) AS P1_M, 
                    SUM(CASE WHEN p.post_stat = "P2" and p.gender="M" THEN 1 ELSE 0 END) AS P2_M,
                    SUM(CASE WHEN p.post_stat = "P3" and p.gender="M" THEN 1 ELSE 0 END) AS P3_M, 
                    SUM(CASE WHEN p.post_stat = "PR" and p.gender="M" THEN 1 ELSE 0 END) AS PR_M,
                    SUM(CASE WHEN p.post_stat = "MO" and p.gender="F"  THEN 1 ELSE 0 END) AS MO_F, 
                    SUM(CASE WHEN p.post_stat = "P1" and p.gender="F" THEN 1 ELSE 0 END) AS P1_F, 
                    SUM(CASE WHEN p.post_stat = "P2" and p.gender="F" THEN 1 ELSE 0 END) AS P2_F,
                    SUM(CASE WHEN p.post_stat = "P3" and p.gender="F" THEN 1 ELSE 0 END) AS P3_F, 
                    SUM(CASE WHEN p.post_stat = "PR" and p.gender="F" THEN 1 ELSE 0 END) AS PR_F
                    FROM personnel p inner join districts d on  d.id=p.district_id where p.district_id="'.$this->district.'" 
                    group by d.name';
						
		  (array)$reportAvailable=DB::select($sqlAvailable);	
           //$arr['available']=$reportAvailable;		
	
	       $sqlRequirement='SELECT d.name,sum(ap.male_party_count) as male_party_count,sum(ap.female_party_count) as female_party_count from districts d 
						inner join assembly_constituencies ac on (ac.district_id=d.id)
						inner join assembly_party ap on (ap.assembly_id=ac.id) and d.id="'.$this->district.'"
						group by d.id,d.name';
	 
	       (array)$reportRequirement=DB::select($sqlRequirement);	
	        //$arr['requirement']=$reportRequirement;
		   
		
			 
			foreach($reportAvailable as $report){
			 foreach($reportRequirement as $requerment){
			   if($requerment->name==$report->name){
				   $report->district_id=$this->district;
				  if(!$requerment->male_party_count || $requerment->male_party_count==''){
					  $report->male_party=$requerment->male_party_count;
				  }else{
					  $report->male_party=$requerment->male_party_count;
				  }
				 if(!$requerment->female_party_count || $requerment->female_party_count==''){
					  $report->female_party=$requerment->female_party_count;
				  }else{
					  $report->female_party=$requerment->female_party_count;
				  }  
			   }
			  
			 }
		   } 
		 return response()->json($reportAvailable,200);
	 
	 }else{
		return response()->json("Unauthorize Access",200);   
		 
	 }
	
	//print_r($arr);
	
     }	
	

 }

 