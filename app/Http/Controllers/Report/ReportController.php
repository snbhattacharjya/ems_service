<?php

namespace App\Http\Controllers\Report;

use \Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Personnel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{

              public function __construct()
              {
                  if(Auth::guard('api')->check()){
                  $this->userID=auth('api')->user()->user_id;
                  $this->level=auth('api')->user()->level;
                  $this->district=auth('api')->user()->area;
                  }
              }

            public function report(Request $request){
              $arr=array();
             $reportMode=$request->report;


            if($reportMode=='pp1'){
              $officeid=$request->officeId;

             $arr['result']= DB::select("select f.id as officeID,f.name,
            f.identification_code,f.officer_designation,
            f.address,f.post_office,f.pin,f.email,f.mobile,f.phone,f.fax,f.total_staff,
            f.male_staff,f.female_staff,sdv.name as subdivision,blm.name as block,
            ps.name as police,ac.name as acname,pc.name as pcname,ds.name as district,
            cat.name as category,ins.name as institute,f.updated_at as updated_at from offices f
            inner join subdivisions sdv on f.subdivision_id=sdv.id
            inner join block_munis blm on f.block_muni_id=blm.id
            inner join police_stations ps on f.police_station_id=ps.id
            inner join assembly_constituencies ac on f.ac_id=ac.id
            inner join parliamentary_constituencies pc on f.pc_id=pc.id
            inner join districts ds on f.district_id=ds.id
            inner join categories cat on f.category_id=cat.id
            inner join institutes ins on f.institute_id=ins.id
            where f.id='".$officeid."'");

            $arr['actualEMpEntry']= DB::select('select  SUM(CASE WHEN p.gender="M" THEN 1 ELSE 0 END) AS maleEntry,
             SUM(CASE WHEN p.gender="F" THEN 1 ELSE 0 END) AS femaleEntry
             from offices f join personnel p on f.id=p.office_id
             where f.id="'.$officeid.'"');



              return response()->json($arr,201);
            }else if($reportMode=='pp2'){
                $time = Carbon::now();
                $morning = Carbon::create($time->year, $time->month, $time->day, 10, 00, 0); //set time to 06:30
                $evening = Carbon::create($time->year, $time->month, $time->day, 17, 00, 0); //set time to 05:30
                if($time->between($evening, $morning, true)) {
                                //current time is between evening and morning
                                return response()->json('Checklist will be available from 5:00 pm to 10:00 am Everyday',201);
                } else {
                    //current time is earlier than evening
                    $result=array();
                    $officeid=$request->officeId;
                    $result['personel']= DB::select('SELECT p.office_id,p.id as id,p.name as empname,p.designation,p.present_address,p.permanent_address,p.dob,p.gender,p.scale,
                    p.basic_pay,p.grade_pay,p.emp_group,p.working_status,p.email,p.phone,p.mobile,p.epic,p.part_no,p.sl_no,p.post_stat,
                    p.branch_ifsc,p.bank_account_no,p.remark_id,p.qualification_id,p.subdivision_id,p.assembly_temp_id,p.assembly_perm_id,p.assembly_off_id
                    from  personnel p  left join offices o on p.office_id=o.id where p.office_id='.$officeid.'');

                    $result['qualification']= DB::select('SELECT q.name as qualification,q.id as qid,rmrks.name as remark,rmrks.id as rid
                    from  personnel p left join offices o on p.office_id=o.id
                    left join qualifications q on p.qualification_id=q.id
                    left join remarks rmrks on  p.remark_id=rmrks.id
                    where p.office_id='.$officeid.'');

                $result['assembly']= DB::select('SELECT sdv.name as subdivision, sdv.id as sdvid,actemp.name as actemp, actemp.id as actempid,acperm.name as acpermanent,acperm.id as acpermid,acoffice.name as acofficename, acoffice.id as acoffid
                from  personnel p left join offices o on p.office_id=o.id
                left join subdivisions sdv on p.subdivision_id=sdv.id
                left join assembly_constituencies actemp on  p.assembly_temp_id=actemp.id
                left join assembly_constituencies acperm on  p.assembly_perm_id=acperm.id
                left join assembly_constituencies acoffice on  p.assembly_off_id=acoffice.id
                where p.office_id='.$officeid.'');
                $arr=array();
                $arr=$result['personel'];
                $i=0;
                foreach($arr as $a){
                foreach($result['qualification'] as $q){
                    if($a->remark_id == $q->rid){$a->remark=$q->remark;}
                    if($a->qualification_id == $q->qid){$a->qualification=$q->qualification;}

                }
                }
                foreach($arr as $a){
                foreach($result['assembly'] as $as){
                    if($a->subdivision_id == $as->sdvid){$a->subdivision=$as->subdivision;}
                    if($a->assembly_temp_id == $as->actempid){$a->actemp=$as->actemp;}
                    if($a->assembly_perm_id == $as->acpermid){$a->acpermanent=$as->acpermanent;}
                    if($a->assembly_off_id == $as->acoffid){$a->acofficename=$as->acofficename;}
                }
                }
                return response()->json($arr,201);

                }

            }else{
              return response()->json('Not Allowed',401);
            }

         }



     public function officeCategopryWisePPadded(){
       if( $this->level==12 || $this->level==5){
  $arr['available']= DB::select("SELECT categories.name, COUNT(CASE WHEN personnel.gender = 'M' THEN 1 END) AS male,
        COUNT(CASE WHEN personnel.gender = 'F' THEN 1 END) AS female,
        COUNT(personnel.id) AS total
        FROM (categories INNER JOIN offices ON categories.id = offices.category_id) INNER JOIN
        personnel ON offices.id = personnel.office_id WHERE offices.district_id = '".$this->district."'
        GROUP BY categories.name");

        return response()->json($arr,201);

       }else{

        return response()->json('Not Allowed',401);
       }

    }

    public function officeCategopryWisePostStatus(){
      if($this->level==12 || $this->level===5){
        $arr['availableMale']= DB::select("SELECT categories.name,
        COUNT(CASE WHEN personnel.post_stat = 'NA' and personnel.gender='M'  THEN 1 END) AS NA,
        COUNT(CASE WHEN personnel.post_stat = 'AEO' and personnel.gender='M' THEN 1 END) AS AEO,
        COUNT(CASE WHEN personnel.post_stat = 'PR' and personnel.gender='M' THEN 1 END) AS PR,
        COUNT(CASE WHEN personnel.post_stat = 'P1' and personnel.gender='M' THEN 1 END) AS P1,
        COUNT(CASE WHEN personnel.post_stat = 'P2' and personnel.gender='M' THEN 1 END) AS P2,
        COUNT(CASE WHEN personnel.post_stat = 'P3' and personnel.gender='M' THEN 1 END) AS P3,
        COUNT(CASE WHEN personnel.post_stat = 'MO' and personnel.gender='M' THEN 1 END) AS MO
       FROM (categories INNER JOIN offices ON categories.id = offices.category_id) INNER JOIN
       personnel ON offices.id = personnel.office_id WHERE offices.district_id = '".$this->district."'
       GROUP BY categories.name");

      $arr['availableFemale']= DB::select("SELECT categories.name,
      COUNT(CASE WHEN personnel.post_stat = 'NA' and personnel.gender='F'  THEN 1 END) AS NA,
      COUNT(CASE WHEN personnel.post_stat = 'AEO' and personnel.gender='F' THEN 1 END) AS AEO,
      COUNT(CASE WHEN personnel.post_stat = 'PR' and personnel.gender='F' THEN 1 END) AS PR,
      COUNT(CASE WHEN personnel.post_stat = 'P1' and personnel.gender='F' THEN 1 END) AS P1,
      COUNT(CASE WHEN personnel.post_stat = 'P2' and personnel.gender='F' THEN 1 END) AS P2,
      COUNT(CASE WHEN personnel.post_stat = 'P3' and personnel.gender='F' THEN 1 END) AS P3,
      COUNT(CASE WHEN personnel.post_stat = 'MO' and personnel.gender='F' THEN 1 END) AS MO
      FROM (categories INNER JOIN offices ON categories.id = offices.category_id) INNER JOIN
      personnel ON offices.id = personnel.office_id WHERE offices.district_id = '".$this->district."'
      GROUP BY categories.name");


       return response()->json($arr,201);

      }else{

       return response()->json('Not Allowed',401);
      }

   }

 public function macroLevelStatictis(){
   if($this->level==12 || $this->level===5){
    $arr['available']=DB::select("SELECT personnel.post_stat, personnel.designation, qualifications.name AS qualification, remarks.name AS remarks,
    MIN(personnel.basic_pay) AS MinBasic, MAX(personnel.basic_pay) AS MaxBasic,
    MIN(personnel.grade_pay) AS MinGrade, MAX(personnel.grade_pay) AS MaxGrade,
    MIN(personnel.pay_level) AS MinPayLevel, MAX(personnel.pay_level) AS MaxPayLevel,
    MIN(DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(personnel.dob, '%Y')) AS MinAge,
    MAX(DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(personnel.dob, '%Y')) AS MaxAge,
    COUNT(personnel.id) AS TotalAvialable
    FROM (personnel INNER JOIN qualifications ON qualifications.id = personnel.qualification_id)
    INNER JOIN remarks ON remarks.id = personnel.remark_id where personnel.district_id='".$this->district."'
    GROUP BY personnel.post_stat, personnel.designation, qualifications.name, remarks.name
    ORDER BY personnel.post_stat, personnel.designation, qualifications.name, remarks.name");
    return response()->json($arr,201);
    }else{
      return response()->json('Not Allowed',401);
    }

}

public function groupWisePP(){
  if($this->level==12 || $this->level===5){
  $arr['ávailable']=DB::select("SELECT distinct(categories.name),
  count(CASE WHEN personnel.gender='M' and personnel.emp_group='A'  THEN 1 END) as A_M,
  count(CASE WHEN personnel.gender='M' and personnel.emp_group='B'  THEN 1 END) as B_M,
  count(CASE WHEN personnel.gender='M' and personnel.emp_group='C'  THEN 1 END) as C_M,
  count(CASE WHEN personnel.gender='M' and personnel.emp_group='D'  THEN 1 END) as D_M,
  count(CASE WHEN personnel.gender='F' and personnel.emp_group='A'  THEN 1 END) as A_F,
  count(CASE WHEN personnel.gender='F' and personnel.emp_group='B'  THEN 1 END) as B_F,
  count(CASE WHEN personnel.gender='F' and personnel.emp_group='C'  THEN 1 END) as C_F,
  count(CASE WHEN personnel.gender='F' and personnel.emp_group='D'  THEN 1 END) as D_F
  FROM (categories INNER JOIN offices ON categories.id = offices.category_id) INNER JOIN
  personnel ON offices.id = personnel.office_id WHERE offices.district_id = '".$this->district."'
  GROUP BY categories.name");
  return response()->json($arr,201);
  }
}

}
