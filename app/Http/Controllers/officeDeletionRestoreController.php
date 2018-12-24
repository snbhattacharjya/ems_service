<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Office;
use App\officeDeleteRestore;
use App\userDeleteRestore;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class officeDeletionRestoreController extends Controller
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

    public function searchOffice(Request $request){
        if($this->level===12 ){
        return Office::where([
            ['district_id', $this->district],
            ['id', $request->s]
        ])
        ->Orwhere([
            ['name','like','%'.$request->s.'%'],
            ['district_id', $this->district],
        ])
        ->Orwhere([
            ['mobile','like','%'.$request->s.'%'],
            ['district_id', $this->district],
        ])->get();
        }

    }
    public function trashedOffice(){
        return DB::table('offices_deleted')->where('district_id',$this->district)->get();

    }

   public function  deleteOffice(Request $request){


    if(Office::where('id',$request->id )->where('district_id',$this->district)->exists() && $this->level===12 ){

    $officeDeleted= Office::where('id',$request->id)->get();
    $office=new officeDeleteRestore;
    $office->id =  strip_tags($officeDeleted[0]->id,'');
    $office->name =  strip_tags($officeDeleted[0]->name,'');
    $office->identification_code =  strip_tags($officeDeleted[0]->identification_code,'');
    $office->officer_designation =   strip_tags($officeDeleted[0]->officer_designation,'');
    $office->address =   strip_tags($officeDeleted[0]->address,'');
    $office->post_office =   strip_tags($officeDeleted[0]->post_office,'');
    $office->pin =   strip_tags($officeDeleted[0]->pin,'');
    $office->subdivision_id =  strip_tags($officeDeleted[0]->subdivision_id,'');
    $office->district_id =  strip_tags($officeDeleted[0]->district_id,'');
    $office->block_muni_id =   strip_tags($officeDeleted[0]->block_muni_id,'');
    $office->police_station_id =   strip_tags($officeDeleted[0]->police_station_id,'');
    $office->ac_id =   strip_tags($officeDeleted[0]->ac_id,'');
    $office->pc_id =   strip_tags($officeDeleted[0]->pc_id,'');
    $office->category_id =   strip_tags($officeDeleted[0]->category_id,'');
    $office->institute_id =   strip_tags($officeDeleted[0]->institute_id,'');
    $office->identification_code =   strip_tags($officeDeleted[0]->identification_code,'');
    $office->email =   strip_tags($officeDeleted[0]->email,'');
    $office->phone =   strip_tags($officeDeleted[0]->phone,'');
    $office->mobile =   strip_tags($officeDeleted[0]->mobile,'');
    $office->fax =   strip_tags($officeDeleted[0]->fax,'');
    $office->total_staff =   strip_tags($officeDeleted[0]->total_staff,'');
    $office->male_staff =   strip_tags($officeDeleted[0]->male_staff,'');
    $office->female_staff =   strip_tags($officeDeleted[0]->female_staff,'');
    $office->created_at = date('Y-m-d H:i:s');
    $office->agree = $officeDeleted[0]->agree;
    $office->save();
    $office_id= $office->id;

    $UserDeleted=User::where('user_id',$office_id)->get();
    $userDeleteStore=new userDeleteRestore;
    $userDeleteStore->id = $UserDeleted[0]->id;
    $userDeleteStore->name = $UserDeleted[0]->name;
    $userDeleteStore->email = $UserDeleted[0]->email;
    $userDeleteStore->mobile = $UserDeleted[0]->mobile;
    $userDeleteStore->aadhaar = $UserDeleted[0]->aadhaar;
    $userDeleteStore->designation = $UserDeleted[0]->designation;
    $userDeleteStore->level = $UserDeleted[0]->level;
    $userDeleteStore->sublevel = $UserDeleted[0]->sublevel;
    $userDeleteStore->area = $UserDeleted[0]->area;
    $userDeleteStore->is_active =$UserDeleted[0]->is_active;
    $userDeleteStore->created_at = $UserDeleted[0]->created_at;
    $userDeleteStore->updated_at = now()->timestamp;
    $userDeleteStore->user_id = $UserDeleted[0]->user_id;
    $userDeleteStore->password =$UserDeleted[0]->password;
    $userDeleteStore->change_password =$UserDeleted[0]->change_password ;
    $userDeleteStore->save();
    $lastInsertedId=$userDeleteStore->id;
   Office::where('id',$request->id)->delete();
   User::where('user_id',$request->id)->delete();
    $arr=array('msg'=>'Deleted Completed','id'=>$request->id);
    return response()->json($arr);
    }
  }

  public function  restoreDeletedOffice(Request $request){
    if(officeDeleteRestore::where('id',$request->id )->where('district_id',$this->district)->exists() && $this->level===12){
    $officeDeleted= officeDeleteRestore::where('id',$request->id)->get();
    $office=new Office;
    $office->id =  strip_tags($officeDeleted[0]->id,'');
    $office->name =  strip_tags($officeDeleted[0]->name,'');
    $office->identification_code =  strip_tags($officeDeleted[0]->identification_code,'');
    $office->officer_designation =   strip_tags($officeDeleted[0]->officer_designation,'');
    $office->address =   strip_tags($officeDeleted[0]->address,'');
    $office->post_office =   strip_tags($officeDeleted[0]->post_office,'');
    $office->pin =   strip_tags($officeDeleted[0]->pin,'');
    $office->subdivision_id =  strip_tags($officeDeleted[0]->subdivision_id,'');
    $office->district_id =  strip_tags($officeDeleted[0]->district_id,'');
    $office->block_muni_id =   strip_tags($officeDeleted[0]->block_muni_id,'');
    $office->police_station_id =   strip_tags($officeDeleted[0]->police_station_id,'');
    $office->ac_id =   strip_tags($officeDeleted[0]->ac_id,'');
    $office->pc_id =   strip_tags($officeDeleted[0]->pc_id,'');
    $office->category_id =   strip_tags($officeDeleted[0]->category_id,'');
    $office->institute_id =   strip_tags($officeDeleted[0]->institute_id,'');
    $office->identification_code =   strip_tags($officeDeleted[0]->identification_code,'');
    $office->email =   strip_tags($officeDeleted[0]->email,'');
    $office->phone =   strip_tags($officeDeleted[0]->phone,'');
    $office->mobile =   strip_tags($officeDeleted[0]->mobile,'');
    $office->fax =   strip_tags($officeDeleted[0]->fax,'');
    $office->total_staff =   strip_tags($officeDeleted[0]->total_staff,'');
    $office->male_staff =   strip_tags($officeDeleted[0]->male_staff,'');
    $office->female_staff =   strip_tags($officeDeleted[0]->female_staff,'');
    $office->created_at = date('Y-m-d H:i:s');
    $office->agree = $officeDeleted[0]->agree;
    $office->save();
    $office_id= $office->id;

    $UserDeleted=userDeleteRestore::where('user_id',$office_id)->get();
    $userDeleteStore=new User;
    $userDeleteStore->id = $UserDeleted[0]->id;
    $userDeleteStore->name = $UserDeleted[0]->name;
    $userDeleteStore->email = $UserDeleted[0]->email;
    $userDeleteStore->mobile = $UserDeleted[0]->mobile;
    $userDeleteStore->aadhaar = $UserDeleted[0]->aadhaar;
    $userDeleteStore->designation = $UserDeleted[0]->designation;
    $userDeleteStore->level = $UserDeleted[0]->level;
    $userDeleteStore->sublevel = $UserDeleted[0]->sublevel;
    $userDeleteStore->area = $UserDeleted[0]->area;
    $userDeleteStore->is_active =$UserDeleted[0]->is_active;
    $userDeleteStore->created_at = $UserDeleted[0]->created_at;
    $userDeleteStore->updated_at = now()->timestamp;
    $userDeleteStore->user_id = $UserDeleted[0]->user_id;
    $userDeleteStore->password =$UserDeleted[0]->password;
    $userDeleteStore->change_password =$UserDeleted[0]->change_password ;
    $userDeleteStore->save();
    $lastInsertedId=$userDeleteStore->id;

    officeDeleteRestore::where('id',$request->id)->delete();
    userDeleteRestore::where('user_id',$request->id)->delete();
    $arr=array('msg'=>'Restore Completed','id'=>$request->id);
    return response()->json($arr);
    }
  }



}
