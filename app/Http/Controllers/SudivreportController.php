<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SudivreportController extends Controller
{
    // Report Subdivision Wise
	  public function __construct()
    {	
	    $this->userID=auth('api')->user()->user_id;
       $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
    }
	public function getDistrictName($district){
			$stateCode=DB::table('districts')->where('id',$district)->pluck('name');
			return $stateCode[0];
	}
	public function reportOnSubdivsion(Request $request){
		
		
	if($this->district=='' and $request->district_id!=''){	
	  $district_id=$request->district_id;//exit;
		 $arr['district']=$this->getDistrictName($district_id);
         $sqlAvailable='SELECT sd.name,p.subdivision_id,
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
						FROM personnel p inner join subdivisions sd on sd.id=p.subdivision_id and sd.district_id="'.$district_id.'"
					    group by p.subdivision_id,sd.name';
						
		(array)$reportAvailable['available']=DB::select($sqlAvailable);	
		       $reportAvailable['district']=$this->getDistrictName($district_id);
       
	 
	     $sqlRequirement='SELECT d.name,sum(ap.male_party_count) as male_party_count,sum(ap.female_party_count) as female_party_count from subdivisions d 
                          inner join assembly_constituencies ac on (ac.district_id=d.district_id) 
						  and ac.subdivision_id=d.id
                          inner join assembly_party ap on (ap.assembly_id=ac.id) 
						  and d.district_id="'.$district_id.'"
                          group by d.id,d.name';
	 
	    (array) $reportRequirement=DB::select($sqlRequirement);	
	     //$arr['requirement']=$reportRequirement;
		 
		 
		 (array)$reportRequirement=DB::select($sqlRequirement);	
		//$arr['requirement']=$reportRequirement;
		 foreach($reportAvailable['available'] as $report){
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
		 
		 
	  }elseif($this->district!='' & $this->level===3){
		
		$sqlAvailable='SELECT sd.name,p.subdivision_id,
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
						FROM personnel p inner join subdivisions sd on sd.id=p.subdivision_id and sd.district_id="'.$this->district.'"
					    group by p.subdivision_id,sd.name';
						
						//echo $sqlAvailable;
						
		(array)$reportAvailable['available']=DB::select($sqlAvailable);	
        //$arr['available']=$reportAvailable;		
	         $reportAvailable['district']=$this->getDistrictName($this->district);
	     $sqlRequirement=' SELECT d.name,sum(ap.male_party_count) as male_party_count,sum(ap.female_party_count) as female_party_count from subdivisions d 
                           inner join assembly_constituencies ac on (ac.district_id=d.district_id) 
						   and ac.subdivision_id=d.id
                           inner join assembly_party ap on (ap.assembly_id=ac.id) 
						   and d.district_id="'.$this->district.'"
                           group by d.id,d.name';
	 
	    

        (array)$reportRequirement=DB::select($sqlRequirement);	
		//$arr['requirement']=$reportRequirement;
		foreach($reportAvailable['available'] as $report){
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
	  
	  
	}	
	
	
}