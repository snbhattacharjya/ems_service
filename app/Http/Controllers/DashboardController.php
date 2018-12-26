<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\PersonnelController;
use App\User;
use App\Office;
use App\District;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller
{
    public function __construct()
    {	
		if(Auth::guard('api')->check()){
		$this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
		$this->district=auth('api')->user()->area;
		$area=auth('api')->user()->area;
		}
    }  

	public function getOfficeData(){//get Dashboard Content
	        $arr=array();
			//echo $this->level;
	    if($this->level===3 || $this->level===5 || $this->level===4 || $this->level===12){
			
			$sql="SELECT count(*)id from offices where district_id='".$this->district."'";
			$office = DB::select($sql);
			
			if(!empty($office[0]->id)){
				$arr['totalOffice']=$office[0]->id;
					$sql='SELECT count(DISTINCT(personnel.office_id)) as office_count,
					count(personnel.id) as totalEmployee,
					sum(CASE WHEN personnel.gender = "M" THEN 1 END) AS Male, 
					sum(CASE WHEN personnel.gender = "F" THEN 1 END) AS Female 
					FROM `personnel` inner join offices on personnel.office_id=offices.id and offices.agree=1  where offices.district_id="'.$this->district.'"';	
				$office = DB::select($sql);
				$arr['distinct_office']=$office[0]->office_count;
				$arr['totalfemale']=$office[0]->Female;
				$arr['totalMale']=$office[0]->Male ;
				$arr['totalemployee']=$office[0]->totalEmployee;

	            DB::update("update district_pp_data set pp_data='".$arr['totalOffice']."', distinct_office='".$arr['distinct_office']."'  , total_register_male='".$arr['totalMale']."',total_register_female='".$arr['totalfemale']."',total_register_emp='".$arr['totalemployee']."' where district_id='".$this->district."'");
           
				$arr['success']='Check Office Details';
				$arr['status']=201;
				
			
			}else{
				$arr['error']='Office Details Does Not Exists';
				$arr['status']=401;
		        }
		}elseif($this->level===10){
			$officeStuff="SELECT total_staff as officeStuff FROM `offices`  where district_id='".$this->district."' and id='".$this->userID."'";
            $officeStuff = DB::select($officeStuff);
        	$arr['officeStuff']=$officeStuff[0]->officeStuff;	  
            $sql='SELECT  count(o.id) as totalEmployee,
						  sum(CASE WHEN p.gender = "M" THEN 1  END) AS Male, 
						  sum(CASE WHEN p.gender = "F" THEN 1  END) AS Female 
						  FROM personnel p
						  inner join offices o on (o.id=p.office_id )
						  where o.district_id="'.$this->district.'" and p.office_id="'.$this->userID.'"';	
				$office = DB::select($sql);
				$arr['totalfemale']=$office[0]->Female;
				$arr['totalMale']=$office[0]->Male ;
				$arr['totalemployee']=$office[0]->totalEmployee;
			    $arr['success']='Check Office Details';
                $arr['status']=201;

		}elseif($this->level===6){//SDO
				$subdivision_id=substr($this->userID,-4);
				$sql="SELECT count(*)id  FROM `offices` WHERE `subdivision_id` = '".$subdivision_id."' and district_id='".$this->district."'";
				$office = DB::select($sql);
				$arr['totalOffice']=$office[0]->id;
				$sql="SELECT count(DISTINCT(personnel.office_id)) as office_count,
				count(personnel.id) as totalEmployee,
				sum(CASE WHEN personnel.gender = 'M' THEN 1  END) AS Male, 
				sum(CASE WHEN personnel.gender = 'F' THEN 1  END) AS Female 
				FROM `personnel` inner join offices on personnel.office_id=offices.id 
				and offices.agree=1 and offices.subdivision_id='".$subdivision_id."'  
				where offices.district_id='".$this->district."'";
				$office = DB::select($sql);
				$arr['distinct_office']=$office[0]->office_count;
                $arr['totalfemale']=$office[0]->Female;
				$arr['totalMale']=$office[0]->Male ;
				$arr['totalemployee']=$office[0]->totalEmployee;
			    $arr['success']='Check Office Details';
                $arr['status']=201;

		}elseif($this->level===7){//BDO
			
			    $block_munis=substr($this->userID,-6);
			   
			    $sql="SELECT count(*)id  FROM `offices` WHERE `block_muni_id` = '".$block_munis."' and district_id='".$this->district."'";
				$office = DB::select($sql);
				$arr['totalOffice']=$office[0]->id;
				$sql="SELECT count(DISTINCT(personnel.office_id)) as office_count,
				count(personnel.id) as totalEmployee,
				sum(CASE WHEN personnel.gender = 'M' THEN 1  END) AS Male, 
				sum(CASE WHEN personnel.gender = 'F' THEN 1  END) AS Female 
				FROM `personnel` inner join offices on personnel.office_id=offices.id 
				and offices.agree=1 and offices.block_muni_id='".$block_munis."'  
				where offices.district_id='".$this->district."'";
			
			   $office = DB::select($sql);
				$arr['distinct_office']=$office[0]->office_count;
                $arr['totalfemale']=$office[0]->Female;
				$arr['totalMale']=$office[0]->Male ;
				$arr['totalemployee']=$office[0]->totalEmployee;
			    $arr['success']='Check Office Details';
                $arr['status']=201;
            
		}elseif($this->level===8){
            
				 $sdo=substr($this->userID,4,3);
				 $deo=substr($this->userID,11,3);
				if($sdo=='SDO' && $deo=='DEO'){ //SDO PPCELL DEO
				$subdivision_id=substr($this->userID,7,4); 
				 $sql="SELECT count(*)id  FROM `offices` WHERE `subdivision_id` = '".$subdivision_id."' and district_id='".$this->district."'";
				$office = DB::select($sql);
				$arr['totalOffice']=$office[0]->id;

				$sql="SELECT count(DISTINCT(personnel.office_id)) as office_count,
				count(personnel.id) as totalEmployee,
				sum(CASE WHEN personnel.gender = 'M' THEN 1  END) AS Male, 
				sum(CASE WHEN personnel.gender = 'F' THEN 1  END) AS Female 
				FROM `personnel` inner join offices on personnel.office_id=offices.id 
				and offices.agree=1 and offices.subdivision_id='".$subdivision_id."'  
				where offices.district_id='".$this->district."'";
				$office = DB::select($sql);
				$arr['distinct_office']=$office[0]->office_count;
				$arr['totalfemale']=$office[0]->Female;
				$arr['totalMale']=$office[0]->Male ;
				$arr['totalemployee']=$office[0]->totalEmployee;
				$arr['success']='Check Office Details';
				$arr['status']=201;
					
				}elseif($sdo=='SDO' && $deo=='OC0'){ //SDO PPCELL OC

					$subdivision_id=substr($this->userID,7,4);
			
					$sql="SELECT count(*)id  FROM `offices` WHERE `subdivision_id` = '".$subdivision_id."' and district_id='".$this->district."'";
					$office = DB::select($sql);
					$arr['totalOffice']=$office[0]->id;
					$sql="SELECT count(DISTINCT(personnel.office_id)) as office_count,
				count(personnel.id) as totalEmployee,
				sum(CASE WHEN personnel.gender = 'M' THEN 1  END) AS Male, 
				sum(CASE WHEN personnel.gender = 'F' THEN 1  END) AS Female 
				FROM `personnel` inner join offices on personnel.office_id=offices.id 
				and offices.agree=1 and offices.subdivision_id='".$subdivision_id."'  
				where offices.district_id='".$this->district."'";
						$office = DB::select($sql);
						$arr['distinct_office']=$office[0]->office_count;	
						$arr['totalfemale']=$office[0]->Female;
						$arr['totalMale']=$office[0]->Male ;
						$arr['totalemployee']=$office[0]->totalEmployee;
						$arr['success']='Check Office Details';
						$arr['status']=201;
               	}else{
				  
				$sql="SELECT count(*)id from offices where district_id='".$this->district."'";
				$office = DB::select($sql);
			if(!empty($office[0]->id)){
				$arr['totalOffice']=$office[0]->id;
				$sql='SELECT count(DISTINCT(personnel.office_id)) as office_count,
				count(personnel.id) as totalEmployee,
				sum(CASE WHEN personnel.gender = "M" THEN 1  END) AS Male, 
				sum(CASE WHEN personnel.gender = "F" THEN 1  END) AS Female 
				FROM `personnel` inner join offices on personnel.office_id=offices.id and offices.agree=1  where offices.district_id="'.$this->district.'"';	
				$office = DB::select($sql);
				$arr['distinct_office']=$office[0]->office_count;
				$arr['totalfemale']=$office[0]->Female;
				$arr['totalMale']=$office[0]->Male ;
				$arr['totalemployee']=$office[0]->totalEmployee;

	            DB::update("update district_pp_data set pp_data='".$arr['totalOffice']."', distinct_office='".$arr['distinct_office']."'  , total_register_male='".$arr['totalMale']."',total_register_female='".$arr['totalfemale']."',total_register_emp='".$arr['totalemployee']."' where district_id='".$this->district."'");
           
				$arr['success']='Check Office Details';
				$arr['status']=201;
				}
		}
	}elseif($this->userID=='WBCEO'){

			$sql="select sum(pp_data) as office_count,sum(total_register_male) as Male,sum(total_register_female) as Female,sum(total_register_emp) as totalEmployee,sum(distinct_office) as distinct_office from district_pp_data ";
			$office = DB::select($sql);
			
			    $arr['distinct_office']=$office[0]->distinct_office;
				 $arr['totalOffice']=$office[0]->office_count;
				 $arr['totalfemale']=$office[0]->Female;
				 $arr['totalMale']=$office[0]->Male;
				 $arr['totalemployee']=$office[0]->totalEmployee;
		}else{
			return response()->json($this->userID,422);
		}
		return $arr;

	}


}
