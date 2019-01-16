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
        $from_district=$request->from_district;
        $to_district=$request->to_district;
        $category=$request->category;
        $res=$this->getRequirement($from_district,$category);
        return response()->json($res,201);

    }
    public function getRequirement($from_district,$category){
      $arr=array();
      $requirement=AssemblyConstituency::select(\DB::raw('sum(assembly_party.male_party_count) as MalePartyRequirement ,sum(assembly_party.female_party_count) as FemalePartyRequirement'))
                         ->join('assembly_party','assembly_party.assembly_id','=','assembly_constituencies.id')
                         ->where('district_id',$from_district)
                         ->get();
                         $arr['requirement']= collect($requirement)->toArray();
      $available=Personnel::select(\DB::raw('sum(gender) as available'))
                         ->where('post_stat',$category)
                         ->where('district_id',$from_district)
                         ->get();
    $arr['available']= collect($available)->toArray();

        return $arr;
    }
    public function instructForDataShare(Request $request){
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
     }
     public function getInstructionForDataShare(Request $request){
       return DataSharing::get();
     }

}
