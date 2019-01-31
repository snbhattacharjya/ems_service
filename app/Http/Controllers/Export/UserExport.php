<?php

namespace App\Http\Controllers\Export;
use App\User;
use App\Passwordgeneration;
use App\Office;
use App\Personnel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class UserExport extends Controller
{
    private $user;
  public function __construct(Request $request)
    {
     // $this->user=json_decode($this->checkAuth($request));
        // echo '<pre>';
        if(Auth::guard('api')->check()){
            $this->userID=auth('api')->user()->user_id;
            $this->level=auth('api')->user()->level;
             $this->district=auth('api')->user()->area;
            $area=auth('api')->user()->area;
            }
    }

   public function checkAuth($request){
    $office=$request->mode;
    $token=$request->token;


    $client = new Client();
    $headers =[
        'Authorization' => 'Bearer '.$token,
        'Accept'        => 'application/json',
    ];

    $response = $client->request('GET', 'http://service.ems.test/api/userauth',[
        'headers' => $headers
    ]);

    if ($response->getStatusCode() == 200){

        return (string)$response->getBody();


        } else{dd('Unauthorize Access');}


   }

   public function userexport(Request $request){

    if($request->mode=="office" && ($this->user->level===3 || $this->user->level===4|| $this->user->level===12 )){


                    $data=User::select('offices.name','offices.email','rand_id','subdivisions.name as subdiv','block_munis.name as blk','police_stations.name as ps','address','post_office','pin','offices.mobile','rand_password')

                ->join('user_random_password', 'user_random_password.rand_id', '=', 'users.user_id')
                ->join('offices', 'offices.id', '=', 'user_random_password.rand_id')
                ->join('subdivisions', 'subdivisions.id', '=', 'offices.subdivision_id')
                ->join('block_munis', 'block_munis.id', '=', 'offices.block_muni_id')
                ->join('police_stations', 'police_stations.id', '=', 'offices.police_station_id')
                ->where('level','10')
                ->where('area',$this->user->area)
                ->get();


               // print_r($data);
                $csvExporter = new \Laracsv\Export();
                $file='offices_'.date('Ymd_H_i_s').'_'.$this->user->area;
                $csvExporter->build($data, ['name'=>'Name','email'=>'Email', 'rand_id'=>'UserId','address'=>'Address','post_office'=>'Post Office','ps'=>'Police Station','blk'=>'Block Muni','subdiv'=>'Subdivision','pin'=>'Pin Code','mobile'=>'Mobile Number','rand_password'=>'Password'])->download( $file.'.csv');

                }elseif($request->mode=="user" && ($this->user->level===3 || $this->user->level===4|| $this->user->level===12)){

                    $data=User::select('user_id','name','email','mobile')
                    ->whereNotIn('level',[1,2,3,4,11,12,10])
                    ->where('area',$this->user->area)
                    ->get();


                $csvExporter = new \Laracsv\Export();
                $file='user_'.date('Ymd_H_i_s').'-'.$this->user->area;
                $csvExporter->build($data, ['name'=>'Name','email'=>'Email', 'user_id'=>'UserId','email'=>'Email Address','mobile'=>'Mobile Number'])->download( $file.'.csv');

                }elseif($request->mode=="personnel" && ($this->user->level===3 || $this->user->level===4|| $this->user->level===12)){
                $data=Personnel::select('office_id','name','designation','dob','gender','present_address','permanent_address',
                'mobile','phone','email','basic_pay','grade_pay','pay_level','emp_group','post_stat')

                ->where('district_id',$this->user->area)
                ->orderBy('office_id')
                ->get();

                $csvExporter = new \Laracsv\Export();
                $file='personnel_'.date('Y-m-d-H-i-s').'-'.$this->user->area;
                $csvExporter->build($data, ['office_id'=>'Office Code', 'name'=>'Name','designation'=>'Designation',
                'dob'=>'DOB','gender'=>'Gender','mobile'=>'Mobile Number','phone'=>'Phone','present_address'=>'Present Address',
                'permanent_address'=>'Permanent Address','email'=>'Email','basic_pay'=>'Basic Pay','emp_group'=>'Group','post_stat'=>'post Status'])->download( $file.'.csv');


                }else{
                    return  response()->json('Error',401);
                }
            }


public function personnelExport(){
    if($this->level===3 || $this->level===4|| $this->level===12){
    $data=Personnel::select('id','office_id','name','designation','dob','gender','present_address','permanent_address',
    'mobile','phone','email','basic_pay','grade_pay','pay_level','emp_group','post_stat','qualification_id','language_id','epic','part_no','sl_no','assembly_temp_id','assembly_perm_id','assembly_off_id','block_muni_temp_id','block_muni_perm_id','block_muni_off_id','subdivision_id','branch_ifsc','bank_account_no','remark_id','remark_reason','exempted','exemp_reason','exemp_date')

    ->where('district_id',$this->district)
    ->orderBy('office_id')
    //->limit(5)
    ->get();


 return  response()->json($data,201);
    }
}
public function officeExport(){
    if($this->level===3 || $this->level===4|| $this->level===12){
        $data=Office::select('id','name','email','phone','mobile','identification_code',
        'address','post_office','pin','ac_id','pc_id','subdivision_id','block_muni_id','police_station_id',
        'category_id','institute_id')
        ->where('district_id',$this->district)
        ->orderBy('id')
        //->limit(5)
        ->get();
     return  response()->json($data,201);
        }
    }


}
