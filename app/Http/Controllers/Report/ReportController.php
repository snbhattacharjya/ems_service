<?php

namespace App\Http\Controllers\Report;

use \Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Personnel;
use Illuminate\Support\Facades\DB;
class ReportController extends Controller
{
            


            public function report(Request $request){
            
             $reportMode=$request->report;
            
            
            if($reportMode=='pp1'){
              $office_id=$request->officeId;
            $result= DB::select('select f.id as officeID,f.name,f.identification_code,f.officer_designation,
              f.address,f.post_office,f.pin,f.email,f.mobile,f.fax,f.total_staff,
              f.male_staff,f.female_staff,sdv.name as subdivision,blm.name as block,
              ps.name as police,ac.name as acname,pc.name as pcname,ds.name as district,
              cat.name as category,ins.name as institute
              from offices f inner join subdivisions sdv on f.subdivision_id=sdv.id 
              inner join block_munis blm on f.block_muni_id=blm.id 
              inner join police_stations ps on f.police_station_id=ps.id
              inner join assembly_constituencies ac on f.ac_id=ac.id
              inner join parliamentary_constituencies pc on f.pc_id=pc.id
              inner join districts ds on f.district_id=ds.id
              inner join categories cat on f.category_id=cat.id
              inner join institutes ins on f.institute_id=ins.id
              
              where f.id='.$office_id.'');
              return response()->json($result,201);
            }else if($reportMode=='pp2'){

                $officeid=$request->officeId;
                $result= DB::select('SELECT p.office_id,p.name as officename,p.designation,p.present_address,p.permanent_address,p.dob,p.gender,p.scale,
                p.basic_pay,p.grade_pay,p.emp_group,p.working_status,p.email,p.phone,p.mobile,p.epic,p.part_no,p.sl_no,p.post_stat,
                p.branch_ifsc,p.bank_account_no,q.name as qualification,ln.name as languagename,blmtemp.name as blomtemp,
                sdv.name as subdivision,actemp.name as actemp,acperm.name as acpermanent,acoffice.name as acofficename,blmoffice.name as blmoffice,
                blmpermanent.name as blmpermanent,rmrks.name as remark
                from  personnel p inner join qualifications q on p.qualification_id=q.id 
                inner join languages ln on p.language_id=ln.id
                inner join subdivisions sdv on p.subdivision_id=sdv.id
                inner join assembly_constituencies actemp on  p.assembly_temp_id=actemp.id
                inner join assembly_constituencies acperm on  p.assembly_perm_id=acperm.id
                inner join assembly_constituencies acoffice on  p.assembly_off_id=acoffice.id
                inner join block_munis blmoffice on  p.block_muni_off_id=blmoffice.id
                inner join block_munis blmtemp on  p.block_muni_temp_id=blmtemp.id
                inner join block_munis blmpermanent on  p.block_muni_perm_id=blmpermanent.id
                inner join remarks rmrks on  p.remark_id=rmrks.id
                where p.office_id='.$officeid.'');
              return response()->json($result,201);
            }

         } 


}
