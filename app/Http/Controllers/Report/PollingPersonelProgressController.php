<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PollingPersonelProgressController extends Controller
{
    //
    public function __construct()
    {	$this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
    }


    public function pollingPersonelProgressReport(){
        if($this->district!='' & ($this->level===3 || $this->level===4 || $this->level===12 || $this->level===5)){
        $qr="SELECT subdivisions.id as subdivision_id, 
        subdivisions.name as subdivision, 
        block_munis.id as block_munis_id, 
        block_munis.name as block,
        count(offices.id) as totalOffice,
        Sum(case when offices.agree=1 then 1 ELSE 0 END) as updateOffice,
        sum(offices.total_staff) as totalStuff, 
        sum(offices.male_staff) as totalMale,
        sum(offices.female_staff) as female_staff
        FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id) 
        INNER JOIN offices ON offices.block_muni_id = block_munis.id where offices.district_id='".$this->district."'
        GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";


       $result= DB::select($qr);
       return response()->json($result,201);
        }elseif($this->level===6){
            $subdivision_id=substr($this->userID,7,4);
            $qr="SELECT subdivisions.id as subdivision_id, 
            subdivisions.name as subdivision, 
            block_munis.id as block_munis_id, 
            block_munis.name as block,
            count(offices.id) as totalOffice,
            Sum(case when offices.agree=1 then 1 ELSE 0 END) as updateOffice,
            sum(offices.total_staff) as totalStuff, 
            sum(offices.male_staff) as totalMale,
            sum(offices.female_staff) as female_staff
            FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id) 
            INNER JOIN offices ON offices.block_muni_id = block_munis.id where offices.district_id='".$this->district."'
            and subdivisions.id='".$subdivision_id."'
            GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";
    
    
           $result= DB::select($qr);
           return response()->json($result,201); 
        }elseif($this->level===8){
            $usertype=substr($this->userID,4,4);
 
            if($usertype=="DTOC"){

                $qr="SELECT subdivisions.id as subdivision_id, 
                subdivisions.name as subdivision, 
                block_munis.id as block_munis_id, 
                block_munis.name as block,
                count(offices.id) as totalOffice,
                Sum(case when offices.agree=1 then 1 ELSE 0 END) as updateOffice,
                sum(offices.total_staff) as totalStuff, 
                sum(offices.male_staff) as totalMale,
                sum(offices.female_staff) as female_staff
                FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id) 
                INNER JOIN offices ON offices.block_muni_id = block_munis.id where offices.district_id='".$this->district."'
                GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";
        
        
               $result= DB::select($qr);
               return response()->json($result,201);
                }elseif($this->level===6){
                    $subdivision_id=substr($this->userID,7,4);
                    $qr="SELECT subdivisions.id as subdivision_id, 
                    subdivisions.name as subdivision, 
                    block_munis.id as block_munis_id, 
                    block_munis.name as block,
                    count(offices.id) as totalOffice,
                    Sum(case when offices.agree=1 then 1 ELSE 0 END) as updateOffice,
                    sum(offices.total_staff) as totalStuff, 
                    sum(offices.male_staff) as totalMale,
                    sum(offices.female_staff) as female_staff
                    FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id) 
                    INNER JOIN offices ON offices.block_muni_id = block_munis.id where offices.district_id='".$this->district."'
                    and subdivisions.id='".$subdivision_id."'
                    GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";
            
            
                   $result= DB::select($qr);
                   return response()->json($result,201); 

            }else{

            $subdivision_id=substr($this->userID,7,4);
            $qr="SELECT subdivisions.id as subdivision_id, 
            subdivisions.name as subdivision, 
            block_munis.id as block_munis_id, 
            block_munis.name as block,
            count(offices.id) as totalOffice,
            Sum(case when offices.agree=1 then 1 ELSE 0 END) as updateOffice,
            sum(offices.total_staff) as totalStuff, 
            sum(offices.male_staff) as totalMale,
            sum(offices.female_staff) as female_staff
            FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id) 
            INNER JOIN offices ON offices.block_muni_id = block_munis.id where offices.district_id='".$this->district."'
            and subdivisions.id='".$subdivision_id."'
            GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";
    
    
           $result= DB::select($qr);
           return response()->json($result,201); 
            }
        }else{

        }
       }




}
