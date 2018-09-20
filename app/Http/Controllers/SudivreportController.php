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
	
	public function reportOnSubdivsion(Request $request){
		
	if($this->district=='' and $request->district_id!=''){	
	  $district_id=$request->district_id;//exit;
		
         $sqlAvailable='SELECT sd.name,p.gender,
						SUM(CASE WHEN p.post_stat = "MO" THEN 1 ELSE 0 END) AS MO, 
						SUM(CASE WHEN p.post_stat = "P1" THEN 1 ELSE 0 END) AS P1, 
						SUM(CASE WHEN p.post_stat = "P2" THEN 1 ELSE 0 END) AS P2,
						SUM(CASE WHEN p.post_stat = "P3" THEN 1 ELSE 0 END) AS P3, 
						SUM(CASE WHEN p.post_stat = "PR" THEN 1 ELSE 0 END) AS PR
						FROM personnel p inner join subdivisions sd on sd.id=p.subdivision_id and sd.district_id="'.$district_id.'"
					    group by p.subdivision_id,sd.name,p.gender';
						
		$reportAvailable=DB::select($sqlAvailable);	
        $arr['available']=$reportAvailable;		
	 
	     $sqlRequirement='SELECT d.name,sum(ap.party_count) as party from subdivisions d 
                          inner join assembly_constituencies ac on (ac.district_id=d.district_id) 
						  and ac.subdivision_id=d.id
                          inner join assembly_party ap on (ap.assembly_id=ac.id) 
						  and d.district_id="'.$district_id.'"
                          group by d.id,d.name';
	 
	     $reportRequirement=DB::select($sqlRequirement);	
	     $arr['requirement']=$reportRequirement;
	  }else{
		  
		$sqlAvailable='SELECT sd.name,p.gender,
						SUM(CASE WHEN p.post_stat = "MO" THEN 1 ELSE 0 END) AS MO, 
						SUM(CASE WHEN p.post_stat = "P1" THEN 1 ELSE 0 END) AS P1, 
						SUM(CASE WHEN p.post_stat = "P2" THEN 1 ELSE 0 END) AS P2,
						SUM(CASE WHEN p.post_stat = "P3" THEN 1 ELSE 0 END) AS P3, 
						SUM(CASE WHEN p.post_stat = "PR" THEN 1 ELSE 0 END) AS PR
						FROM personnel p inner join subdivisions sd on sd.id=p.subdivision_id and sd.district_id="'.$this->district.'"
					    group by p.subdivision_id,sd.name,p.gender';
						
						//echo $sqlAvailable;
						
		$reportAvailable=DB::select($sqlAvailable);	
        $arr['available']=$reportAvailable;		
	 
	     $sqlRequirement=' SELECT d.name,sum(ap.party_count) as party from subdivisions d 
                           inner join assembly_constituencies ac on (ac.district_id=d.district_id) 
						   and ac.subdivision_id=d.id
                           inner join assembly_party ap on (ap.assembly_id=ac.id) 
						   and d.district_id="'.$this->district.'"
                           group by d.id,d.name';
	 
	     $reportRequirement=DB::select($sqlRequirement);	
	     $arr['requirement']=$reportRequirement;  
		  
	  }
	  
	  return response()->json($arr,200);
	}	
	
	
}
