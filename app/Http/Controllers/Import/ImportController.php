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
 if($user_id=='WB14DEO'){
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

  return Personnel::where([
    ['epic', $request->epic],
])
->Orwhere([
  ['mobile', $request->mobile],
])
->Orwhere([
  ['bank_account_no', $request->bank_account_no],
])->get();


}

}
