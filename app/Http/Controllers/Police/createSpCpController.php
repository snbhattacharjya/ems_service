<?php

namespace App\Http\Controllers\Police;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\District;
use App\User;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class createSpCpController extends Controller
{
  
public function autoCreate(){
  
  District::get()->each(function($district){
      $type='SP';
      $level=13;
       $user=new User;
       $user->name = $district->name.' '.$type;
      $user->email =$district->name.$type.'@gmail.com';
      $user->mobile = '99999999';
      $user->designation = $district->name.' '.$type;
      $user->level = $level;
      $user->area =$district->id;
      $user->user_type ='police';
      $user->is_active =1;
      $user->created_at =now();
      $user->user_id ='WB'.$district->id.$type.'01';
      $user_id='WB'.$district->id.$type.'01';
      $user->password=bcrypt($user_id);
      $user->change_password=1;
     // $user->save();
		});
		echo 'Finished';
	}

}