<?php

namespace App\Http\Controllers\Export;
use App\User;
use App\Passwordgeneration;
use App\Office;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
class UserExport extends Controller
{    
    private $user;

    
    public function __construct(Request $request)
    {	
        $this->user=$this->checkAuth($request);
        // $this->userID=auth('api')->user()->user_id;
        // $this->level=auth('api')->user()->level;
        // $this->district=auth('api')->user()->area; 
    }
    
   public function checkAuth(Request $request){
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
    if ($response->getStatusCode() == 200){return json_decode((string)$response->getBody());}
    else{dd('Unauthorize Access');}
   

   }

   public function userexport(Request $request) 
    {
        
        if(isset($this->user))
            {
                if($request->mode=="office" && ($this->user->level===3 || $this->user->level===4|| $this->user->level===12 || $this->user->level===10)){
                $data=User::select('offices.name','rand_id','address','post_office','pin','offices.mobile','rand_password')
                ->join('user_random_password', 'rand_id', '=', 'users.user_id')
                ->join('offices', 'offices.id', '=', 'user_random_password.rand_id')
                ->where('level','10')
                ->where('area',$this->user->area)
                ->get(); 
                $csvExporter = new \Laracsv\Export();
                $file='user'.date('Y-m-d-H-i-s').'-'.'13';
                $csvExporter->build($data, ['name'=>'Name', 'rand_id'=>'UserId','address'=>'Address','post_office'=>'Post Office','pin'=>'Pin Code','mobile'=>'Mobile Number','rand_password'=>'Password'])->download( $file.'.csv');

                }elseif($request->mode=="user" && ($this->user->level===3 || $this->user->level===4|| $this->user->level===12 || $this->user->level===10)){
                
                    $data=User::select('user_id','name','email','mobile')
                    ->whereNotIn('level',[1,2,3,4,11,12,10])
                    ->where('area',$this->user->area)
                    ->get(); 


                $csvExporter = new \Laracsv\Export();
                $file='user'.date('Y-m-d-H-i-s').'-'.'13';
                $csvExporter->build($data, ['name'=>'Name', 'user_id'=>'UserId','email'=>'Email Address','mobile'=>'Mobile Number'])->download( $file.'.csv');
                
                }else{
                    return  response()->json('Error',401);
                }
            }
            else{
                return response()->json('Unauthorised Access',401);
            }

    }

   public function export(){
    $data=User::select('offices.name','rand_id','address','post_office','pin','offices.mobile','rand_password')
    ->join('user_random_password', 'rand_id', '=', 'users.user_id')
    ->join('offices', 'offices.id', '=', 'user_random_password.rand_id')
    ->where('level','10')
    ->where('area','13')
    ->get(); 
    $csvExporter = new \Laracsv\Export();
    $file='user'.date('Y-m-d-H-i-s').'-'.'13';
    $csvExporter->build($data, ['name'=>'Name', 'rand_id'=>'UserId','address'=>'Address','post_office'=>'Post Office','pin'=>'Pin Code','mobile'=>'Mobile Number','rand_password'=>'Password'])->download( $file.'.csv');

   }



}
