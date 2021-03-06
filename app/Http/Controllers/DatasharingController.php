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
        $gender=$request->gender;
        $res=$this->getRequirement($from_district,$category,$gender);
        return response()->json($res,201);
        }else{
            return response()->json('Not Allowed',401);
        }

    }
    public function getRequirement($from_district,$category,$gender){
        if($this->level==2){
      $arr=array();
      $requirement=AssemblyConstituency::select(\DB::raw('sum(assembly_party.male_party_count) as MalePartyRequirement ,
                          sum(assembly_party.female_party_count) as FemalePartyRequirement
                          ,sum(assembly_party.male_aeo_count) as MaleAeoRequirement
                          ,sum(assembly_party.female_aeo_count) as FemaleAeoRequirement
                          ,sum(assembly_party.female_mo_count) as MaleMoRequirement
                          ,sum(assembly_party.male_mo_count) as FemaleMoRequirement'))
                         ->join('assembly_party','assembly_party.assembly_id','=','assembly_constituencies.id')
                         ->where('district_id',$from_district)
                         ->get();
                         $arr['requirement']= collect($requirement)->toArray();
      $available=Personnel::select(\DB::raw('count(gender) as available'))
                         ->where('post_stat',$category)
                         ->where('district_id',$from_district)
                         ->where('gender',$gender)
                         ->where('to_district',NULL)
                         ->where('exempted',NULL)
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
        $gender=$request->gender;

        $dataShare= new DataSharing;
        $dataShare->from_district=$from_district;
        $dataShare->to_district=$to_district;
        $dataShare->category=$category;
        $dataShare->gender=$gender;
        $dataShare->no_of_personnel=$assign_polling_personnel;
        $dataShare->save();
        if($dataShare->id!=''){
            return response()->json('Successfully Saved',201);
          }
      }else{
        return response()->json('Not Allowed',401);
       }
     }

     public function updateInstructForDataShare(Request $request){
        // $from_district=$request->from_district;
        // $to_district=$request->to_district;
        // $category=$request->category;
        $assign_polling_personnel=$request->assign_polling_personnel;
        // $gender=$request->gender;
        $dataShare= DataSharing::find($request->sharing_id);
        $dataShare->id=$request->sharing_id;
        // $dataShare->from_district=$from_district;
        // $dataShare->to_district=$to_district;
        // $dataShare->category=$category;
        // $dataShare->gender=$gender;
        $dataShare->no_of_personnel=$assign_polling_personnel;
        $dataShare->save();
        return response()->json('Successfully Updated',201);
     }

   public function deleteInstructForDataShare(){
    DataSharing::where('id',$request->sharing_id)->delete();
    return response()->json('Successfully Deleted',201);
   }


     public function getInstructionForDataShare(Request $request){
      if($this->level==2){
       return DataSharing::select('data_sharing.id as id','from_districts.name as from_district','to_districts.name as to_district','data_sharing.category as category','data_sharing.no_of_personnel as no_of_personnel','data_sharing.no_of_personnel_shared as no_of_personnel_shared','data_sharing.gender as gender')
                          ->join('districts as from_districts','from_districts.id','=','data_sharing.from_district')
                          ->join('districts as to_districts','to_districts.id','=','data_sharing.to_district')
                          ->get();
      }else{
        return response()->json('Not Allowed',401);
      }
     }

     public function getShareRequest(){ //GET Method
      if($this->level==12){
            return DataSharing::select('data_sharing.id as id','from_districts.name as from_district','to_districts.name as to_district','data_sharing.category as category','data_sharing.no_of_personnel as no_of_personnel','data_sharing.no_of_personnel_shared as no_of_personnel_shared','data_sharing.gender as gender','data_sharing.created_at as created_at','data_sharing.updated_at as updated_at')
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
        $gender=$request->gender;
        //$transfer_category=$getCeoRequest[0]->category;

        $requirement=AssemblyConstituency::select(\DB::raw('sum(assembly_party.male_party_count) as MalePartyRequirement,
                            sum(assembly_party.female_party_count) as FemalePartyRequirement
                            ,sum(assembly_party.male_aeo_count) as MaleAeoRequirement
                            ,sum(assembly_party.female_aeo_count) as FemaleAeoRequirement
                            ,sum(assembly_party.female_mo_count) as MaleMoRequirement
                            ,sum(assembly_party.male_mo_count) as FemaleMoRequirement'))
                           ->join('assembly_party','assembly_party.assembly_id','=','assembly_constituencies.id')
                           ->where('district_id',$this->district)
                           ->get();
                           $arr['requirement']= collect($requirement)->toArray();
        $available=Personnel::select(\DB::raw('count(gender) as available'))
                           ->where('post_stat',$transfer_category)
                           ->where('district_id',$this->district)
                           ->where('to_district',NULL)
                           ->where('gender',$gender)
                           ->where('exempted',NULL)
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
            $gender=$getCeoRequest[0]->gender;

            if($personnel_assigned< $transfer_personnel +  $shared_personnel){
                return response()->json('Number can not be greater than personnel assigned by CEO !',401);
                die();
            }
            $available=Personnel::select(\DB::raw('count(gender) as available'))
            ->where('post_stat',$transfer_category)
            ->where('district_id',$this->district)
            ->where('gender',$gender)
            ->where('to_district',NULL)
            ->where('exempted',NULL)
            ->get();
            $requirement=AssemblyConstituency::select(\DB::raw('sum(assembly_party.male_party_count) as MalePartyRequirement ,
                   sum(assembly_party.female_party_count) as FemalePartyRequirement
                   ,sum(assembly_party.male_aeo_count) as MaleAeoRequirement
                   ,sum(assembly_party.female_aeo_count) as FemaleAeoRequirement
                   ,sum(assembly_party.female_mo_count) as MaleMoRequirement
                   ,sum(assembly_party.male_mo_count) as FemaleMoRequirement'))
            ->join('assembly_party','assembly_party.assembly_id','=','assembly_constituencies.id')
            ->where('district_id',$this->district)
            ->get();

          if($transfer_category=='MO'){
            $available=$available[0]['available']-($requirement[0]['MaleMoRequirement']+$requirement[0]['FemaleMoRequirement']);
          }else if($transfer_category=='AEO'){
            $available=$available[0]['available']-($requirement[0]['MaleAeoRequirement']+$requirement[0]['FemaleAeoRequirement']);
          }else{
            $available=$available[0]['available']-($requirement[0]['MalePartyRequirement']+$requirement[0]['FemalePartyRequirement']);
          }



           if($available>$transfer_personnel){
            Personnel::where('post_stat',$transfer_category)
            ->where('district_id',$this->district)
            ->where('gender',$gender)
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
