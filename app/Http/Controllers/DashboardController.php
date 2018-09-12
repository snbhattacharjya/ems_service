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
	    if($this->level===3){
			$sql="SELECT count(*)id from offices where district_id='".$this->district."'";
			$office = DB::select($sql);
			if(!empty($office[0]->id)){
				$arr['totalOffice']=$office[0]->id;
				$sql="SELECT count(*)gender from personnel INNER JOIN offices on offices.id=personnel.office_id where personnel.district_id='".$this->district."' and personnel.gender='F'";
				$officef = DB::select($sql);
				$arr['totalfemale']=$officef[0]->gender ;
				$sql="SELECT count(*)gender from personnel INNER JOIN offices on offices.id=personnel.office_id where personnel.district_id='".$this->district."' and personnel.gender='M'";
				$officem = DB::select($sql);
				$arr['totalMale']=$officem[0]->gender ;
				$arr['totalemployee']=$officem[0]->gender +$officef[0]->gender ;
				$arr['success']='Check Office Details';
				$arr['status']=201;
				}else{
				$arr['error']='Office Details Does Not Exists';
				$arr['status']=401;
		        }

		}elseif($this->level===10){
            $sql="SELECT total_staff from offices where district_id='".$this->district."' and id='".$this->userID."'";
            $office = DB::select($sql);
            $arr['totalOffice']=$office[0]->total_staff;
            $sql="SELECT count(*)gender from personnel where district_id='".$this->district."' and personnel.gender='F' and office_id='".$this->userID."'";
            $officef = DB::select($sql);
            $arr['totalfemale']=$officef[0]->gender ;
            $sql="SELECT count(*)gender from personnel where district_id='".$this->district."' and personnel.gender='M' and office_id='".$this->userID."'";
            $officem = DB::select($sql);
            $arr['totalMale']=$officem[0]->gender ;
            $arr['totalemployee']=$officem[0]->gender +$officef[0]->gender ;
            $arr['success']='Check Office Details';
            $arr['status']=201;

		}else{
			//$sql="SELECT count(*)id from offices where district_id='".$this->district."'";
			//$office = DB::select($sql);


			//
		}





		return $arr;

	}

}
