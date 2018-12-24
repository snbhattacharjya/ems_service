<?php
namespace App\Http\Controllers;
use app\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class ReportassembleController extends Controller
{
    //
	public function __construct()
    {	
		if(Auth::guard('api')->check()){
	   $this->userID=auth('api')->user()->user_id;
       $this->level=auth('api')->user()->level;
		$this->district=auth('api')->user()->area;
		}
    }
	public function getDistrictName($district){
			$stateCode=DB::table('districts')->where('id',$district)->pluck('name');
			return $stateCode[0];
	}
	public function getAllDistrict(){
		     if($this->userID=='WBCEO' || $this->userID=='WBCEONODAL'){
			$getAll=DB::select('SELECT id,name FROM `districts` ORDER BY `id` ASC');
			return $getAll;
			 }else{
				 
			 $getAll=DB::select('SELECT id,name FROM `districts` where id='.$this->district.'');
			return $getAll;	 
			 }
	}
	
	
   public function getAssmblyReport(Request $request){
	  $arr=array();
	 if($this->district=='' & ($this->userID=="WBCEO" || $this->userID=="WBCEONODAL")){
	      $arr['district']=$this->getDistrictName($request->district_id);
		  $sqlAvailable='SELECT ac.id,ac.name,ap.male_party_count as male_party_count,
		                ap.female_party_count as female_party_count from 
						assembly_constituencies ac inner join assembly_party ap on (ap.assembly_id=ac.id) 
						where ac.district_id="'.$request->district_id.'" order by ac.id asc';
					//echo $sqlAvailable;exit;
		  $reportAvailable=DB::select($sqlAvailable);
		  $arr['available']=$reportAvailable;
		   
		  return response()->json($arr,200);
	 }else if($this->district!='' & ($this->level===3 || $this->level===4 || $this->level===12 || $this->level===5) & $this->district===$request->district_id){
		
	
		$arr['district']=$this->getDistrictName($this->district);
		  $sqlAvailable='SELECT ac.id,ac.name,ap.male_party_count as male_party_count,
		                ap.female_party_count as female_party_count from 
						assembly_constituencies ac inner join assembly_party ap on (ap.assembly_id=ac.id) 
						where ac.district_id="'.$request->district_id.'"   order by ac.id asc';
		
		
		
		(array)$reportAvailable=DB::select($sqlAvailable);
		$arr['available']=$reportAvailable;
		   
		return response()->json($arr,200);
		 
	 }else if($this->level===6){
		$subdivision_id=substr($this->userID,7,4);
		
		$arr['district']=$this->getDistrictName($this->district);
		 $sqlAvailable='SELECT ac.id,ac.name,ap.male_party_count as male_party_count,
					   ap.female_party_count as female_party_count from 
					   assembly_constituencies ac inner join assembly_party ap on (ap.assembly_id=ac.id) 
					   where ac.district_id="'.$request->district_id.'"  and  ac.subdivision_id="'.$subdivision_id.'" order by ac.id asc';
	   
	   
	   
	   (array)$reportAvailable=DB::select($sqlAvailable);
	   $arr['available']=$reportAvailable;
		  
	   return response()->json($arr,200);
	}elseif($this->level===8){

		$usertype=substr($this->userID,4,4);
 
		if($usertype=="DTOC"){ 
          
        
		$arr['district']=$this->getDistrictName($this->district);
		$sqlAvailable='SELECT ac.id,ac.name,ap.male_party_count as male_party_count,
					  ap.female_party_count as female_party_count from 
					  assembly_constituencies ac inner join assembly_party ap on (ap.assembly_id=ac.id) 
					  where ac.district_id="'.$request->district_id.'"   order by ac.id asc';
	  
	  
	  
	  (array)$reportAvailable=DB::select($sqlAvailable);
	  $arr['available']=$reportAvailable;
		 
	  return response()->json($arr,200);

		}else{
		$subdivision_id=substr($this->userID,7,4);
		$usertype=substr($this->userID,11,2);
		 if($usertype=='OC'){
		$arr['district']=$this->getDistrictName($this->district);
		 $sqlAvailable='SELECT ac.id,ac.name,ap.male_party_count as male_party_count,
					   ap.female_party_count as female_party_count from 
					   assembly_constituencies ac inner join assembly_party ap on (ap.assembly_id=ac.id) 
					   where ac.district_id="'.$request->district_id.'"  and  ac.subdivision_id="'.$subdivision_id.'" order by ac.id asc';
	   
	   
	   
	   (array)$reportAvailable=DB::select($sqlAvailable);
	   $arr['available']=$reportAvailable;
		  
	   return response()->json($arr,200);
		 }
		}
   }else{
		return response()->json("Unauthorize Access",200);   
		 
	 }
   }
  public function updateAssmblyByReport(Request $request){
	$assembly_id=$request->assembly_id;
	$male_party_count=$request->male_party_count;
	$female_party_count=$request->female_party_count;
	$district_id=$request->district_id;

if(!empty($assembly_id) && (!empty($district_id)) && ($this->district==$district_id) && ($this->level===3 || $this->level===4|| $this->level===12)){ 
		
	DB::table('assembly_party')->where('assembly_id',$assembly_id)
	->update(['male_party_count' => $male_party_count,'female_party_count' => $female_party_count,'updated_at' => date('Y-m-d H:i:s')]);
	
	return response()->json('Successfully Updated',201);                 
}else{

		return response()->json('Error',400);
}
  

}
	

}