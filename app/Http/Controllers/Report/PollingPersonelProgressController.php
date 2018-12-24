<?php

namespace App\Http\Controllers\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PollingPersonelProgressController extends Controller
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


    public function pollingPersonelProgressReport(){
        if($this->district!='' & ($this->level===3 || $this->level===4 || $this->level===12 || $this->level===5)){
        $qr="SELECT subdivisions.id as subdivision_id,
        subdivisions.name as subdivision,
        block_munis.id as block_munis_id,
        block_munis.name as block,
        count(offices.id) as totalOffice,
        sum(case when  offices.agree=1  then 1 ELSE 0 END) as updateOffice,
        sum(case when  offices.agree=1  then offices.total_staff ELSE 0 END) as totalStuff,
        sum(case when  offices.agree=1  then offices.male_staff ELSE 0 END) as totalMale,
        sum(case when  offices.agree=1  then offices.female_staff ELSE 0 END) as female_staff,
        cast(0 as UNSIGNED) as malepp2,cast(0 as UNSIGNED) as femalepp2,cast(0 as UNSIGNED) as pp2started
        FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id)
        INNER JOIN offices ON offices.block_muni_id = block_munis.id where offices.district_id='".$this->district."'
        GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";
        $result['pp1']= DB::select($qr);

        $pp2="SELECT subdivisions.id as subdivision_id,
        subdivisions.name as subdivision,
        block_munis.id as block_munis_id,
        block_munis.name as block,
        count(distinct(personnel.office_id)) as officepp2,
        sum(case when personnel.gender='M' and offices.agree=1 then 1 else 0 end) as pp2M,
        sum(case when personnel.gender='F' and offices.agree=1 then 1 else 0 end) as pp2F
        FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id)
        INNER JOIN offices ON offices.block_muni_id = block_munis.id
        INNER JOIN  personnel on offices.id=personnel.office_id   where offices.district_id='".$this->district."'
        GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";
       $result['pp2']= DB::select($pp2);

     foreach($result['pp1'] as  $pp1){

         foreach($result['pp2'] as $pp2){
         if($pp1->block_munis_id==$pp2->block_munis_id){

            $pp1->malepp2= $pp2->pp2M;
            $pp1->femalepp2= $pp2->pp2F ;
            $pp1->pp2started=$pp2->officepp2;

         }
         }

     }
    // print_r($result['pp1']);
    return response()->json($result['pp1'],201);
        }elseif($this->level===6){
            $subdivision_id=substr($this->userID,7,4);
            $qr="SELECT subdivisions.id as subdivision_id,
            subdivisions.name as subdivision,
            block_munis.id as block_munis_id,
            block_munis.name as block,
            count(offices.id) as totalOffice,
            sum(case when  offices.agree=1  then 1 ELSE 0 END) as updateOffice,
            sum(case when  offices.agree=1  then offices.total_staff ELSE 0 END) as totalStuff,
            sum(case when  offices.agree=1  then offices.male_staff ELSE 0 END) as totalMale,
            sum(case when  offices.agree=1  then offices.female_staff ELSE 0 END) as female_staff,
            cast(0 as UNSIGNED) as malepp2,cast(0 as UNSIGNED) as femalepp2,cast(0 as UNSIGNED) as pp2started
            FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id)
            INNER JOIN offices ON offices.block_muni_id = block_munis.id where offices.district_id='".$this->district."'
            and subdivisions.id='".$subdivision_id."'
            GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";
           $result['pp1']= DB::select($qr);

           $pp2="SELECT block_munis.id as block_munis_id,
        count(distinct(personnel.office_id)) as officepp2,
        sum(case when personnel.gender='M' and offices.agree=1 then 1 else 0 end) as pp2M,
        sum(case when personnel.gender='F' and offices.agree=1 then 1 else 0 end) as pp2F
        FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id)
        INNER JOIN offices ON offices.block_muni_id = block_munis.id
        INNER JOIN  personnel on offices.id=personnel.office_id   where offices.district_id='".$this->district."'
        and subdivisions.id='".$subdivision_id."'
     GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";
       $result['pp2']= DB::select($pp2);


       foreach($result['pp1'] as  $pp1){

        foreach($result['pp2'] as $pp2){
        if($pp1->block_munis_id==$pp2->block_munis_id){

           $pp1->malepp2= $pp2->pp2M  ;
           $pp1->femalepp2= $pp2->pp2F;
           $pp1->pp2started=$pp2->officepp2;
        }

        }

    }
   // print_r($result['pp1']);
   return response()->json($result['pp1'],201);
        }elseif($this->level===8){
            $usertype=substr($this->userID,4,4);

            if($usertype=="DTOC"){

                $qr="SELECT subdivisions.id as subdivision_id,
                subdivisions.name as subdivision,
                block_munis.id as block_munis_id,
                block_munis.name as block,
                count(offices.id) as totalOffice,
                sum(case when  offices.agree=1  then 1 ELSE 0 END) as updateOffice,
                sum(case when  offices.agree=1  then offices.total_staff ELSE 0 END) as totalStuff,
                sum(case when  offices.agree=1  then offices.male_staff ELSE 0 END) as totalMale,
                sum(case when  offices.agree=1  then offices.female_staff ELSE 0 END) as female_staff,
                cast(0 as UNSIGNED) as malepp2,cast(0 as UNSIGNED) as femalepp2,cast(0 as UNSIGNED) as pp2started
                FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id)
                INNER JOIN offices ON offices.block_muni_id = block_munis.id where offices.district_id='".$this->district."'
                GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";

          $result['pp1']= DB::select($qr);
            $pp2="SELECT block_munis.id as block_munis_id,
        count(distinct(personnel.office_id)) as officepp2,
        sum(case when personnel.gender='M' and offices.agree=1 then 1 else 0 end) as pp2M,
        sum(case when personnel.gender='F' and offices.agree=1 then 1 else 0 end) as pp2F
        FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id)
        INNER JOIN offices ON offices.block_muni_id = block_munis.id
        INNER JOIN  personnel on offices.id=personnel.office_id where offices.district_id='".$this->district."'
        GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";
       $result['pp2']= DB::select($pp2);

     foreach($result['pp1'] as  $pp1){
         foreach($result['pp2'] as $pp2){
         if($pp1->block_munis_id==$pp2->block_munis_id){
            $pp1->malepp2=$pp2->pp2M;
            $pp1->femalepp2=$pp2->pp2F;
            $pp1->pp2started=$pp2->officepp2;
         }

         }

     }

               return response()->json($result['pp1'],201);
 }elseif($this->level===6){
                    $subdivision_id=substr($this->userID,7,4);
                    $qr="SELECT subdivisions.id as subdivision_id,
                    subdivisions.name as subdivision,
                    block_munis.id as block_munis_id,
                    block_munis.name as block,
                    count(offices.id) as totalOffice,
                    sum(case when offices.agree=1  then 1 ELSE 0 END) as updateOffice,
                   sum(case when  offices.agree=1  then offices.total_staff ELSE 0 END) as totalStuff,
                   sum(case when  offices.agree=1  then offices.male_staff ELSE 0 END) as totalMale,
                   sum(case when  offices.agree=1  then offices.female_staff ELSE 0 END) as female_staff,
                   cast(0 as UNSIGNED) as malepp2,cast(0 as UNSIGNED) as femalepp2,cast(0 as UNSIGNED) as pp2started
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
            sum(case when  offices.agree=1  then 1 ELSE 0 END) as updateOffice,
            sum(case when  offices.agree=1  then offices.total_staff ELSE 0 END) as totalStuff,
            sum(case when  offices.agree=1  then offices.male_staff ELSE 0 END) as totalMale,
            sum(case when  offices.agree=1  then offices.female_staff ELSE 0 END) as female_staff,
            cast(0 as UNSIGNED) as malepp2,cast(0 as UNSIGNED) as femalepp2,cast(0 as UNSIGNED) as pp2started
             FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id)
            INNER JOIN offices ON offices.block_muni_id = block_munis.id where offices.district_id='".$this->district."'
            and subdivisions.id='".$subdivision_id."'
             GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";


           $result['pp1']= DB::select($qr);


           $pp2="SELECT block_munis.id as block_munis_id,
           count(distinct(personnel.office_id)) as officepp2,
           sum(case when personnel.gender='M' and offices.agree=1 then 1 else 0 end) as pp2M,
           sum(case when personnel.gender='F' and offices.agree=1 then 1 else 0 end) as pp2F
           FROM (subdivisions INNER JOIN block_munis ON subdivisions.id=block_munis.subdivision_id)
           INNER JOIN offices ON offices.block_muni_id = block_munis.id
           INNER JOIN  personnel on offices.id=personnel.office_id where offices.district_id='".$this->district."'
           and subdivisions.id='".$subdivision_id."'
           GROUP BY subdivisions.id, subdivisions.name, block_munis.id, block_munis.name ORDER BY subdivisions.id";
          $result['pp2']= DB::select($pp2);

        foreach($result['pp1'] as  $pp1){
            foreach($result['pp2'] as $pp2){
            if($pp1->block_munis_id==$pp2->block_munis_id){
               $pp1->malepp2=$pp2->pp2M;
               $pp1->femalepp2=$pp2->pp2F;
               $pp1->pp2started=$pp2->officepp2;
            }

            }

        }




           return response()->json($result['pp1'],201);
            }
        }else{

        }
       }

 public function districtWisePPstatistic(){
     if($this->level==2){
   $sql_pp1="SELECT districts.name, COUNT(CASE WHEN offices.agree = 0 THEN 1 END) AS PP1_Not_Updated,
   COUNT(CASE WHEN offices.agree = 1 THEN 1 else 0 END) AS PP1_Updated, COUNT(*) AS Total_Offices,
   SUM(CASE WHEN offices.agree = 1 THEN offices.male_staff else 0 END) AS Male_PP_Declared,
   SUM(CASE WHEN offices.agree = 1 THEN offices.female_staff else 0 END) AS Female_PP_Declared,
   SUM(CASE WHEN offices.agree = 1 THEN offices.total_staff else 0  END) AS Total_PP_Declared
   FROM districts INNER JOIN offices ON districts.id = offices.district_id
   GROUP BY districts.name order by districts.id";
     }else if($this->level===3 || $this->level===4 || $this->level===12 || $this->level===5){
        $sql_pp1="SELECT districts.name, COUNT(CASE WHEN offices.agree = 0 THEN 1 END) AS PP1_Not_Updated,
        COUNT(CASE WHEN offices.agree = 1 THEN 1 else 0 END) AS PP1_Updated, COUNT(*) AS Total_Offices,
        SUM(CASE WHEN offices.agree = 1 THEN offices.male_staff else 0 END) AS Male_PP_Declared,
        SUM(CASE WHEN offices.agree = 1 THEN offices.female_staff else 0 END) AS Female_PP_Declared,
        SUM(CASE WHEN offices.agree = 1 THEN offices.total_staff else 0  END) AS Total_PP_Declared
        FROM districts INNER JOIN offices ON districts.id = offices.district_id where districts.id='$this->district'";
     }else{
   //
     }
   $results['pp1']=DB::select($sql_pp1);
   if($this->level==2){
   $sql_pp2="SELECT districts.name, COUNT(CASE WHEN personnel.gender = 'M' THEN 1 END) AS Male_PP_Added,
   COUNT(CASE WHEN personnel.gender = 'F' THEN 1 else 0 END) AS Female_PP_Added,
   COUNT(*) AS Total_PP_Added
   FROM (districts INNER JOIN offices ON districts.id = offices.district_id) INNER JOIN
   personnel ON offices.id = personnel.office_id GROUP BY districts.name order by districts.id";
   }else if($this->level===3 || $this->level===4 || $this->level===12 || $this->level===5){
    $sql_pp2="SELECT districts.name, COUNT(CASE WHEN personnel.gender = 'M' THEN 1 END) AS Male_PP_Added,
    COUNT(CASE WHEN personnel.gender = 'F' THEN 1 else 0 END) AS Female_PP_Added,
    COUNT(*) AS Total_PP_Added
    FROM (districts INNER JOIN offices ON districts.id = offices.district_id) INNER JOIN
    personnel ON offices.id = personnel.office_id where districts.id='$this->district'";
   }else{

   }
   $results['pp2']=DB::select($sql_pp2);


   foreach($results['pp1'] as  $pp1){
    foreach($results['pp2'] as $pp2){
    if($pp1->name==$pp2->name){
       $pp1->Male_PP_Added=$pp2->Male_PP_Added;
       $pp1->Female_PP_Added=$pp2->Female_PP_Added;
       $pp1->Total_PP_Added=$pp2->Total_PP_Added;
    }
    else{
       $pp1->Male_PP_Added=0;
       $pp1->Female_PP_Added=0;
       $pp1->Total_PP_Added=0;
    }

    }
 }
 return response()->json($results['pp1'],201);

   }



}
