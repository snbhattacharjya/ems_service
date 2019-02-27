<?php

namespace App\Http\Controllers\Import;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Personnel;

class ImportController extends Controller
{
    //
public function validateUser(Request $request){
 $user_id=$request->user_id;
 $password=$request->password;
 $district=$request->district;
 if($user_id=='WB18DEO'){
  $UserPassword=DB::table('users')->where('user_id',$user_id)->pluck('password');
	if(Hash::check($password,$UserPassword[0])){
          if(!empty($district)){
           $res=Personnel::where('district_id',$district)->get();
           return array($res,200);
          }
         }else{
           return array('Invalid Password',401);
        }
     }else{

           return array('Invalid User Id ',401);

    }
  }

public function getPersonnelData(Request $request){

  return Personnel::select('personnel.id','office_id','offices.name as officename','offices.mobile as officemobile','offices.phone as officephone','offices.email as officeemail','personnel.name','designation','present_address','permanent_address','dob','basic_pay','grade_pay',
  'emp_group','personnel.email','personnel.phone','personnel.mobile','epic','assembly_constituencies.name as assembly_perm_id','block_munis.name as block_muni_perm_id',
  'districts.name as district_id','subdivisions.name as subdivision_id','qualifications.name as qualification_id',
  'bank_account_no','remarks.name as remark_id','languages.name as language_id','part_no','sl_no')
  ->LeftJoin('offices','offices.id','=','personnel.office_id')
  ->LeftJoin('assembly_constituencies','assembly_constituencies.id','=','personnel.assembly_perm_id')
  ->LeftJoin('block_munis','block_munis.id','=','personnel.block_muni_perm_id')
  ->LeftJoin('districts','districts.id','=','personnel.district_id')
  ->LeftJoin('subdivisions','subdivisions.id','=','personnel.subdivision_id')
  ->LeftJoin('qualifications','qualifications.id','=','personnel.qualification_id')
  ->LeftJoin('remarks','remarks.id','=','personnel.remark_id')
  ->LeftJoin('languages','languages.id','=','personnel.language_id')
  ->where([
    ['personnel.epic', $request->s],
  ])
  ->Orwhere([
  ['personnel.mobile', $request->s],
   ])
  ->Orwhere([
  ['personnel.bank_account_no', $request->s],
  ])->get();


}

}
