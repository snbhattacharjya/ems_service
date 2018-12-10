<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\SubdivisionController;
use App\Http\Controllers\ParliamentaryConstituencyController;

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
		 
		 
	  }elseif($this->district!='' & ($this->level===3 || $this->level===4 || $this->level===12)){
		
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
		 
	   
	  }elseif($this->level===6){
	   $subdivision_id= substr($this->userID,7,4);
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
		FROM personnel p inner join subdivisions sd on sd.id=p.subdivision_id and sd.district_id="'.$this->district.'" and sd.id="'. $subdivision_id.'"
		group by p.subdivision_id,sd.name';
		
		//echo $sqlAvailable;
		
(array)$reportAvailable['available']=DB::select($sqlAvailable);	
//$arr['available']=$reportAvailable;		
$reportAvailable['district']=$this->getDistrictName($this->district);
$sqlRequirement=' SELECT d.name,sum(ap.male_party_count) as male_party_count,sum(ap.female_party_count) as female_party_count from subdivisions d 
		   inner join assembly_constituencies ac on (ac.district_id=d.district_id) 
		   and ac.subdivision_id=d.id
		   inner join assembly_party ap on (ap.assembly_id=ac.id) 
		   and d.district_id="'.$this->district.'" and d.id="'.$subdivision_id.'"
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
		public function subdivisionWiseAssemblyReport(){
			if($this->district!='' & ($this->level===3 || $this->level===4 || $this->level===12  || $this->level===5)){
			$sqlRequirement='select assembly_constituencies.id,assembly_constituencies.name,
			assembly_constituencies.pc_id,parliamentary_constituencies.name as pcname,
			assembly_constituencies.subdivision_id as subdivision_id,
			subdivisions.name as subdivisions,
			assembly_party.male_party_count  from assembly_constituencies 
			join assembly_party on assembly_constituencies.id=assembly_party.assembly_id
			join subdivisions on subdivisions.id=assembly_constituencies.subdivision_id
			join parliamentary_constituencies on parliamentary_constituencies.id=assembly_constituencies.pc_id
			where assembly_constituencies.district_id='.$this->district.' order by assembly_constituencies.subdivision_id asc,assembly_constituencies.pc_id asc';
			$arr['subdivisionWiseAssemblyReport']=DB::select($sqlRequirement);
			
			$subdivision=(new SubdivisionController)->getSubdivisions();
           for($i=0;$i<count($subdivision);$i++){
			
			$subdivisionwise='select assembly_constituencies.id,assembly_constituencies.name,
			assembly_constituencies.pc_id,parliamentary_constituencies.name as pcname,
			assembly_constituencies.subdivision_id as subdivision_id,
			subdivisions.name as subdivisions,
			assembly_party.male_party_count  from assembly_constituencies 
			join assembly_party on assembly_constituencies.id=assembly_party.assembly_id
			join subdivisions on subdivisions.id=assembly_constituencies.subdivision_id
			join parliamentary_constituencies on parliamentary_constituencies.id=assembly_constituencies.pc_id
			where assembly_constituencies.district_id='.$this->district.' and assembly_constituencies.subdivision_id="'.$subdivision[$i]->id.'" ';
			//$arr['subdivisionwise']['subdivisionname']=$subdivision[$i]->name;
			$arr['subdivisionwise'][$subdivision[$i]->name]=DB::select($subdivisionwise);

		   } 
          $pc=(new ParliamentaryConstituencyController)->getPcs();
			
		  for($i=0;$i<count($pc);$i++){
			
			$pcwise='select assembly_constituencies.id,assembly_constituencies.name,
			assembly_constituencies.pc_id,parliamentary_constituencies.name as pcname,
			assembly_constituencies.subdivision_id as subdivision_id,
			subdivisions.name as subdivisions,
			assembly_party.male_party_count  from assembly_constituencies 
			join assembly_party on assembly_constituencies.id=assembly_party.assembly_id
			join subdivisions on subdivisions.id=assembly_constituencies.subdivision_id
			join parliamentary_constituencies on parliamentary_constituencies.id=assembly_constituencies.pc_id
			where assembly_constituencies.district_id='.$this->district.' and assembly_constituencies.pc_id="'.$pc[$i]->id.'" ';
			//$arr['subdivisionwise']['subdivisionname']=$subdivision[$i]->name;
			$arr['pcwise'][$pc[$i]->name]=DB::select($pcwise);

		   } 
		   return response()->json($arr,200);
	     	}elseif($this->level==6 || $this->level==8 ){
				$subdivision_id=substr($this->userID,7,4);
				$sqlRequirement='select assembly_constituencies.id,assembly_constituencies.name,
				assembly_constituencies.pc_id,parliamentary_constituencies.name as pcname,
				assembly_constituencies.subdivision_id as subdivision_id,
				subdivisions.name as subdivisions,
				assembly_party.male_party_count  from assembly_constituencies 
				join assembly_party on assembly_constituencies.id=assembly_party.assembly_id
				join subdivisions on subdivisions.id=assembly_constituencies.subdivision_id
				join parliamentary_constituencies on parliamentary_constituencies.id=assembly_constituencies.pc_id
				where assembly_constituencies.district_id='.$this->district.' and subdivisions.id='.$subdivision_id.'  order by assembly_constituencies.subdivision_id asc,assembly_constituencies.pc_id asc';
				$arr['subdivisionWiseAssemblyReport']=DB::select($sqlRequirement);
				
				$subdivision=(new SubdivisionController)->getSubdivisions();
			   for($i=0;$i<count($subdivision);$i++){
				
				$subdivisionwise='select assembly_constituencies.id,assembly_constituencies.name,
				assembly_constituencies.pc_id,parliamentary_constituencies.name as pcname,
				assembly_constituencies.subdivision_id as subdivision_id,
				subdivisions.name as subdivisions,
				assembly_party.male_party_count  from assembly_constituencies 
				join assembly_party on assembly_constituencies.id=assembly_party.assembly_id
				join subdivisions on subdivisions.id=assembly_constituencies.subdivision_id
				join parliamentary_constituencies on parliamentary_constituencies.id=assembly_constituencies.pc_id
				where assembly_constituencies.district_id='.$this->district.' and assembly_constituencies.subdivision_id="'.$subdivision[$i]->id.'" ';
				//$arr['subdivisionwise']['subdivisionname']=$subdivision[$i]->name;
				$arr['subdivisionwise'][$subdivision[$i]->name]=DB::select($subdivisionwise);
	
			   } 
			  $pc=(new ParliamentaryConstituencyController)->getPcs();
				
			  for($i=0;$i<count($pc);$i++){
				
				$pcwise='select assembly_constituencies.id,assembly_constituencies.name,
				assembly_constituencies.pc_id,parliamentary_constituencies.name as pcname,
				assembly_constituencies.subdivision_id as subdivision_id,
				subdivisions.name as subdivisions,
				assembly_party.male_party_count  from assembly_constituencies 
				join assembly_party on assembly_constituencies.id=assembly_party.assembly_id
				join subdivisions on subdivisions.id=assembly_constituencies.subdivision_id
				join parliamentary_constituencies on parliamentary_constituencies.id=assembly_constituencies.pc_id
				where assembly_constituencies.district_id='.$this->district.' and assembly_constituencies.pc_id="'.$pc[$i]->id.'" and assembly_constituencies.subdivision_id="'.$subdivision_id.'" ';
				//$arr['subdivisionwise']['subdivisionname']=$subdivision[$i]->name;
				$arr['pcwise'][$pc[$i]->name]=DB::select($pcwise);
	
			   } 
			   return response()->json($arr,200);

			 }else{

		return response()->json($reportRequirement,400);
	       }


	
        }
}