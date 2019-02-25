<?php

namespace App\Http\Controllers\Report;
use App\Personnel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RemarksWiseController extends Controller
{
    //
    public function __construct()
    {
        if(Auth::guard('api')->check()){
        $this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
        }
    }
    public function RemarksWisePersonnelStatus(Request $request){
        if($this->level==3 || $this->level==4 || $this->level==5 || $this->level==12 ||$this->level==8 ){
    $sql='SELECT r.name,
    SUM(CASE WHEN p.remark_id = r.id and p.gender="M" and exempted is NULL and to_district is NULL THEN 1 ELSE 0 END) AS male,
    SUM(CASE WHEN p.remark_id = r.id and p.gender="F" and exempted is NULL and to_district is NULL THEN 1 ELSE 0 END) AS female,
    SUM(CASE WHEN p.remark_id = r.id  THEN 1 ELSE 0 END) AS total
    from personnel p join remarks r on  r.id=p.remark_id where p.district_id="'.$this->district.'" group by r.name order by r.id';

  (array)$reportRemark['available']=DB::select($sql);
  return response()->json($reportRemark,200);
    }else if($this->level==2){

        $sql='SELECT r.name,
    SUM(CASE WHEN p.remark_id = r.id and p.gender="M" and exempted is NULL and to_district is NULL THEN 1 ELSE 0 END) AS male,
    SUM(CASE WHEN p.remark_id = r.id and p.gender="F" and exempted is NULL and to_district is NULL THEN 1 ELSE 0 END) AS female,
    SUM(CASE WHEN p.remark_id = r.id  THEN 1 ELSE 0 END) AS total
    from personnel p join remarks r on  r.id=p.remark_id where p.district_id="'.$request->district.'" group by r.name order by r.id';

  (array)$reportRemark['available']=DB::select($sql);
  $arr['district']=$this->getDistrictName($request->district);
  return response()->json($reportRemark,200);

    }else{

       return response()->json($reportRemark,200);
    }





    }
    public function getDistrictName($district){
        $stateCode=DB::table('districts')->where('id',$district)->pluck('name');
        return $stateCode[0];
    }
 public function getNoEpic(Request $request){
if($this->level==12 || $this->level==8 || $this->level==8){
   return Personnel::select('personnel.district_id as personelDistrict','personnel.id as personnelId','personnel.name as personnel','personnel.epic as epic','personnel.designation as designation',
                           'personnel.email as email','personnel.mobile as mobile','personnel.phone as phone','offices.name as office','offices.id as officeId',
                           'offices.address as officeAddress','offices.phone as officePhone','offices.post_office as officePost','offices.pin as officePin',
                           'offices.mobile as officeMobile','offices.email as officeEmail')
                    ->leftjoin('offices','offices.id','=','personnel.office_id')
                    ->where([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','noepic']
                        ])
                   ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','na']
                        ])
                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','naa']
                        ])
                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','naaa']
                        ])
                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','a']
                        ])
                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','aa']
                        ])
                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','aaa']
                        ])
                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','aaaa']
                        ])
                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','0']
                        ])
                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','00']
                        ])
                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','000']
                        ])
                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','0000']
                        ])

                    ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','BX']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','xx']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','xxx']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','xxxx']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','c']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','cc']
                        ])
                         ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','ccc']
                        ])
                         ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','cccc']
                        ])

                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','b']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','bb']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','bbb']
                        ])

                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','v']
                        ])

                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','vv']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','vvv']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','vvvv']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','no']
                        ])
                        ->orwhere([
                            ['offices.district_id',$this->district],
                            ['personnel.epic','=','pg']
                        ])
                    ->get();
}elseif($this->level==2){
    return Personnel::select('personnel.district_id as personelDistrict','personnel.id as personnelId','personnel.name as personnel','personnel.epic as epic','personnel.designation as designation',
                           'personnel.email as email','personnel.mobile as mobile','personnel.phone as phone','offices.name as office','offices.id as officeId',
                           'offices.address as officeAddress','offices.phone as officePhone','offices.post_office as officePost','offices.pin as officePin',
                           'offices.mobile as officeMobile','offices.email as officeEmail')
                    ->leftjoin('offices','offices.id','=','personnel.office_id')
                    ->where([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','noepic']
                    ])
               ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','na']
                    ])
                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','naa']
                    ])
                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','naaa']
                    ])
                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','a']
                    ])
                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','aa']
                    ])
                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','aaa']
                    ])
                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','aaaa']
                    ])
                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','0']
                    ])
                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','00']
                    ])
                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','000']
                    ])
                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','0000']
                    ])

                ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','BX']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','xx']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','xxx']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','xxxx']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','c']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','cc']
                    ])
                     ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','ccc']
                    ])
                     ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','cccc']
                    ])

                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','b']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','bb']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','bbb']
                    ])

                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','v']
                    ])

                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','vv']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','vvv']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','vvvv']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','no']
                    ])
                    ->orwhere([
                        ['offices.district_id',$request->district],
                        ['personnel.epic','=','pg']
                    ])

                    ->get();
}

 }


 public function getWrongEpic(Request $request){

    if($this->level==12 || $this->level==5 || $this->level==8){
    return Personnel::select('personnel.district_id as personelDistrict','personnel.id as personnelId','personnel.name as personnel','personnel.epic as epic','personnel.designation as designation',
    'personnel.email as email','personnel.mobile as mobile','personnel.phone as phone','offices.name as office','offices.id as officeId',
    'offices.address as officeAddress','offices.phone as officePhone','offices.post_office as officePost','offices.pin as officePin',
    'offices.mobile as officeMobile','offices.email as officeEmail')
                    ->leftjoin('offices','offices.id','=','personnel.office_id')
                    ->leftjoin('personnel_epic','personnel_epic.id','!=','personnel.epic')
                    ->where('personnel.verified',0)
                    ->where('personnel.district_id',$this->district);
    }else if($this->level==2){
        return Personnel::select('personnel.district_id as personelDistrict','personnel.id as personnelId','personnel.name as personnel','personnel.epic as epic','personnel.designation as designation',
    'personnel.email as email','personnel.mobile as mobile','personnel.phone as phone','offices.name as office','offices.id as officeId',
    'offices.address as officeAddress','offices.phone as officePhone','offices.post_office as officePost','offices.pin as officePin',
    'offices.mobile as officeMobile','offices.email as officeEmail')
                    ->leftjoin('offices','offices.id','=','personnel.office_id')
                    ->leftjoin('personnel_epic','personnel_epic.id','!=','personnel.epic')
                    ->where('personnel.verified',0)
                    ->where('personnel.district_id',$request->district);


    }else{
        return 'Not Allowed';
    }


   }





   public function getUnverifiedMatchedEpic(){
    return Personnel::select('personnel.district_id as personelDistrict','personnel.id as personnelId','personnel.name as personnel','personnel.epic as epic','personnel.designation as designation',
    'personnel.email as email','personnel.mobile as mobile','personnel.phone as phone','offices.name as office','offices.id as officeId',
    'offices.address as officeAddress','offices.phone as officePhone','offices.post_office as officePost','offices.pin as officePin',
    'offices.mobile as officeMobile','offices.email as officeEmail')
                    ->leftjoin('offices','offices.id','=','personnel.office_id')
                    ->join('personnel_epic','personnel_epic.id','=','personnel.epic')
                    ->where('personnel.verified',0)
                    ->where('personnel.district_id',$this->district);

   }


  public function doVerifyEpic(){
    Personnel::join('personnel_epic','personnel_epic.id','=','personnel.epic')
             ->where('personnel.verified',0)
             ->where('personnel.district_id', $this->district)
             ->update([ 'personnel.part_no' => DB::raw("personnel_epic.part_no"),'personnel.sl_no' => DB::raw("personnel_epic.sl_no") ]);

  }



}
