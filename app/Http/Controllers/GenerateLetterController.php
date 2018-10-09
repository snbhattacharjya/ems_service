<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\DB;
class GenerateLetterController extends Controller
{
    //
 Public function generateLetter($lowerlimit=0,$upperlimit=10){
      $rand=uniqid();
      $data=array();
     //$lowerlimit=0;
      //$upperlimit=10;
       $data['userinfo']=DB::Select("select ofc.name,ofc.identification_code,ofc.address,rand.rand_id as userId,rand.rand_password as userPassword from user_random_password rand join offices ofc on ofc.id=rand.rand_id where ofc.district_id='13' limit ". $lowerlimit.",". $upperlimit);
    
       $pdf = PDF::loadView('pdfview',$data)->setPaper('a4', 'landscape');
       $pdf->save(storage_path().'/letter/'.$rand.'.pdf')->stream('download.pdf')->header('Content-Type','application/pdf');;
      
      
       // return storage_path().'/letter/'.$rand.'.pdf';

   // return view('pdfview',$data);



   }
   Public function generateLettertest(){
    ini_set('max_execution_time', 3000);
    ini_set('memory_limit','16M');
    $rand=uniqid();
    $data=array();
   //$lowerlimit=0;
    //$upperlimit=10;
     $data['userinfo']=DB::Select("select ofc.name,ofc.name,ofc.identification_code,ofc.address,rand.rand_id as userId,rand.rand_password as userPassword from user_random_password rand join offices ofc on ofc.id=rand.rand_id where ofc.district_id='13' limit 0,400");
  
     $pdf = PDF::loadView('pdfview',$data)->setPaper('a4', 'landscape');
     $pdf->save(storage_path().'/letter/'.$rand.'.pdf')->stream('download.pdf')->header('Content-Type','application/pdf');;
    
    
     return 'Success';

 // return view('pdfview',$data);



 }

   public function setLimit(){
    
        $ll=0;
        $ul=0;
        $i=0;
        $conter=DB::Select("select COUNT(id) as count from offices where offices.district_id='13'  ");

        for($i=0;$i<$counter;$i++){
            if($ll===0 && $ul===0){
                $ll=0;$ul=10;  
            }else{
                $ll=$ul+1; $ul=10;
            }
            $this-> generateLetter($ll,$ul);
        }
        
   }

}
