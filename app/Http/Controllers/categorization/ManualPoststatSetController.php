<?php

namespace App\Http\Controllers\categorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Personnel;
use Illuminate\Support\Facades\DB;
use App\PollingPost;
class ManualPoststatSetController extends Controller
{
    public function __construct()
    {	
        if(Auth::guard('api')->check()){
        $this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
        }
    }
    public function GetPersonnelByOfficeAndPoststat(Request $request){//acccessable only district level
       $office_id=$request->office_id;
       $post_stat=$request->post_stat;
       if(!empty($office_id) && empty($post_stat) && ($this->level===3 || $this->level===4|| $this->level===12)){
                return Personnel:: where('district_id','=',$this->district)
                                    ->where('office_id' ,'=',$office_id)
                                    ->get();
       }else if(!empty($office_id) && !empty($post_stat) && ($this->level===3 || $this->level===4|| $this->level===12)){
                return Personnel:: where('district_id','=',$this->district)
                ->where('office_id' ,'=',$office_id)
                ->where('post_stat' ,'=',$post_stat)
                ->get();
       }else{
        return response()->json('Error',400);
       }
    }
  public function postStatManualSave(Request $request){

            $office_id=$request->office_id;
            $personnelId=$request->personnel_id; 
            $postStat=$request->poststat; 
        
if(!empty($personnelId)  && !empty($office_id) && ($this->level===3 || $this->level===4|| $this->level===12)){ 
                Personnel:: where('district_id','=',$this->district)
                                ->where('id' ,'=',$personnelId)
                                ->where('office_id' ,'=',$office_id)
                                ->update(['post_stat'=>$postStat]);
                return response()->json('Successfully Updated',201);                 
     }else{

                return response()->json('Error',400);
        }
          
    
        }


}
