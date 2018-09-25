<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\PersonnelController;
use App\User;
use App\Office;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    public function __construct()
    {	$this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
    }

	public function getOfficeData(){//get Dashboard Content
	        $arr=array();
			//echo $this->level;
	    if($this->level===3 || $this->level===5 || $this->level===4){
			
			
				  
			$sql="SELECT count(*)id from offices where district_id='".$this->district."'";
			$office = DB::select($sql);
			if(!empty($office[0]->id)){
				$arr['totalOffice']=$office[0]->id;
					$sql='SELECT  count(o.id) as totalEmployee,
						  sum(CASE WHEN p.gender = "M" THEN 1 ELSE 0 END) AS Male, 
						  sum(CASE WHEN p.gender = "F" THEN 1 ELSE 0 END) AS Female 
						  FROM personnel p
						  inner join offices o on (o.id=p.office_id )
						  where o.district_id="'.$this->district.'"';	
				$office = DB::select($sql);
				$arr['totalfemale']=$office[0]->Female;
				$arr['totalMale']=$office[0]->Male ;
				$arr['totalemployee']=$office[0]->totalEmployee;
				$arr['success']='Check Office Details';
				$arr['status']=201;
				}else{
				$arr['error']='Office Details Does Not Exists';
				$arr['status']=401;
		        }

		}elseif($this->level===10){
			
            $sql="SELECT count(*)id from offices where district_id='".$this->district."' and id='".$this->userID."'";
            $office = DB::select($sql);
            $arr['totalOffice']=$office[0]->id;
            $sql='SELECT  count(o.id) as totalEmployee,
						  sum(CASE WHEN p.gender = "M" THEN 1 ELSE 0 END) AS Male, 
						  sum(CASE WHEN p.gender = "F" THEN 1 ELSE 0 END) AS Female 
						  FROM personnel p
						  inner join offices o on (o.id=p.office_id )
						  where o.district_id="'.$this->district.'" and p.office_id="'.$this->userID.'"';	
						 // echo $sql;exit;
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
				
				$sql="SELECT  count(o.id) as totalEmployee,
                      sum(CASE WHEN p.gender = 'M' THEN 1 ELSE 0 END) AS Male, 
                      sum(CASE WHEN p.gender = 'F' THEN 1 ELSE 0 END) AS Female 
                      FROM personnel p
                      inner join offices o on (o.id=p.office_id ) 
					   and o.subdivision_id='". $subdivision_id."'
                      where o.district_id='".$this->district."'";
				$office = DB::select($sql);
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
				//************
				$sql="SELECT  count(o.id) as totalEmployee,
                      sum(CASE WHEN p.gender = 'M' THEN 1 ELSE 0 END) AS Male, 
                      sum(CASE WHEN p.gender = 'F' THEN 1 ELSE 0 END) AS Female 
                      FROM personnel p
                      inner join offices o on (o.id=p.office_id ) 
					  and o.block_muni_id='". $block_munis."'
                      where o.district_id='".$this->district."'";
				$office = DB::select($sql);
                $arr['totalfemale']=$office[0]->Female;
				$arr['totalMale']=$office[0]->Male ;
				$arr['totalemployee']=$office[0]->totalEmployee;
			    $arr['success']='Check Office Details';
                $arr['status']=201;

		}else if($this->userID=='WBCEO'){
				 $arr['totalOffice']=9178;
				 $arr['totalfemale']=23803;
				 $arr['totalMale']=97619;
				 $arr['totalemployee']=121422;
		}else{
			return response()->json($this->userID,422);
		}





		return $arr;

	}

}
