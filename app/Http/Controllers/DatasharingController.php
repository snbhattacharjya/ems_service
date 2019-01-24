<?php
namespace App\Http\Controllers;

use Eloquent;
use Illuminate\Http\Request;
use App\Personnel;
use App\AssemblyConstituency;
use App\DataSharing;
use Illuminate\Support\Facades\DB;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class DatasharingController extends Controller
{
    //
    public function __construct(){
        if(Auth::guard('api')->check()){
        $this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
        }
    }


    public function queryForDataShare(Request $request){
        if($this->level==2){
        $from_district=$request->from_district;
        $to_district=$request->to_district;
        $category=$request->category;
        $res=$this->getRequirement($from_district,$category);
        return response()->json($res,201);
        }else{
            return response()->json('Not Allowed',401);
        }

    }
    public function getRequirement($from_district,$category){
        if($this->level==2){
      $arr=array();
      $requirement=AssemblyConstituency::select(\DB::raw('sum(assembly_party.male_party_count) as MalePartyRequirement ,sum(assembly_party.female_party_count) as FemalePartyRequirement'))
                         ->join('assembly_party','assembly_party.assembly_id','=','assembly_constituencies.id')
                         ->where('district_id',$from_district)
                         ->get();
                         $arr['requirement']= collect($requirement)->toArray();
      $available=Personnel::select(\DB::raw('count(gender) as available'))
                         ->where('post_stat',$category)
                         ->where('district_id',$from_district)
                         ->where('to_district',NULL)
                         ->get();
    $arr['available']= collect($available)->toArray();

        return $arr;
        }else{
            return response()->json('Not Allowed',401);
        }
    }
    public function instructForDataShare(Request $request){
        if($this->level==2){
        $from_district=$request->from_district;
        $to_district=$request->to_district;
        $category=$request->category;
        $assign_polling_personnel=$request->assign_polling_personnel;

        $dataShare= new DataSharing;
        $dataShare->from_district=$from_district;
        $dataShare->to_district=$to_district;
        $dataShare->category=$category;
        $dataShare->no_of_personnel=$assign_polling_personnel;
        $dataShare->save();
        if($dataShare->id!=''){
            return response()->json('Successfully Saved',201);
          }
      }else{
        return response()->json('Not Allowed',401);
       }
     }
     public function getInstructionForDataShare(Request $request){
      if($this->level==2){
       return DataSharing::select('data_sharing.id as id','from_districts.name as from_district','to_districts.name as to_district','data_sharing.category as category','data_sharing.no_of_personnel as no_of_personnel','data_sharing.no_of_personnel_shared as no_of_personnel_shared')
                          ->join('districts as from_districts','from_districts.id','=','data_sharing.from_district')
                          ->join('districts as to_districts','to_districts.id','=','data_sharing.to_district')
                          ->get();
      }else{
        return response()->json('Not Allowed',401);
      }
     }

     public function getShareRequest(){ //GET Method
      if($this->level==12){
            return DataSharing::select('data_sharing.id as id','from_districts.name as from_district','to_districts.name as to_district','data_sharing.category as category','data_sharing.no_of_personnel as no_of_personnel','data_sharing.no_of_personnel_shared as no_of_personnel_shared')
            ->join('districts as from_districts','from_districts.id','=','data_sharing.from_district')
            ->join('districts as to_districts','to_districts.id','=','data_sharing.to_district')
            ->where('data_sharing.from_district',$this->district)
            ->get();

        }else{
            return response()->json('Not Allowed',401);
        }

     }
     public function getRequirementAvailability(Request $request){
        if($this->level==12){
        $arr=array();
        $transfer_category=$request->category; //PR,MO,P1 type=POST
        //$transfer_category=$getCeoRequest[0]->category;

        $requirement=AssemblyConstituency::select(\DB::raw('sum(assembly_party.male_party_count) as MalePartyRequirement ,sum(assembly_party.female_party_count) as FemalePartyRequirement'))
                           ->join('assembly_party','assembly_party.assembly_id','=','assembly_constituencies.id')
                           ->where('district_id',$this->district)
                           ->get();
                           $arr['requirement']= collect($requirement)->toArray();
        $available=Personnel::select(\DB::raw('count(gender) as available'))
                           ->where('post_stat',$transfer_category)
                           ->where('district_id',$this->district)
                           ->where('to_district',NULL)
                           ->get();
      $arr['available']= collect($available)->toArray();

            return $arr;
        }else{
            return response()->json('Not Allowed',401);
        }
     }

     public function doDataShare(Request $request){// parameter {id}/{no_of_personnel} type=post
       // $this->level=12;
        if($this->level==12){
            $data_share_id=$request->id;
           // $this->district=13;
            $getCeoRequest=DataSharing::where('id',$data_share_id)
                        ->where('from_district',$this->district)
                        ->get();

            $transfer_to_district=$getCeoRequest[0]->to_district;
            $transfer_category=$getCeoRequest[0]->category;
            $transfer_personnel=$request->no_of_personnel;
            $shared_personnel=$getCeoRequest[0]->no_of_personnel_shared == null ? 0 :$getCeoRequest[0]->no_of_personnel_shared;
            $personnel_assigned=$getCeoRequest[0]->no_of_personnel;
            if($personnel_assigned< $transfer_personnel){
                return response()->json('Number can not be greater than personnel assigned by CEO !',401);
                die();
            }
            $available=Personnel::select(\DB::raw('count(gender) as available'))
            ->where('post_stat',$transfer_category)
            ->where('district_id',$this->district)
            ->where('to_district',NULL)
            ->get();
            $requirement=AssemblyConstituency::select(\DB::raw('sum(assembly_party.male_party_count) as MalePartyRequirement ,sum(assembly_party.female_party_count) as FemalePartyRequirement'))
            ->join('assembly_party','assembly_party.assembly_id','=','assembly_constituencies.id')
            ->where('district_id',$this->district)
            ->get();


           if(($available[0]['available']-($requirement[0]['MalePartyRequirement']+$requirement[0]['FemalePartyRequirement']))>$transfer_personnel){
            Personnel::where('post_stat',$transfer_category)
            ->where('district_id',$this->district)
            ->inRandomOrder()
            ->limit($transfer_personnel)
            ->update(['to_district' =>$transfer_to_district,'share_date'=>Now()]);

            DataSharing::where('id',$data_share_id)
            ->update(['no_of_personnel_shared' =>$shared_personnel+$transfer_personnel]);

           return response()->json('Successfully Shared',201);
           }else{
            return response()->json('Number can not be greater than available !',401);
           }

        }else{
            return response()->json('Not Allowed',401);
        }
     }


}
