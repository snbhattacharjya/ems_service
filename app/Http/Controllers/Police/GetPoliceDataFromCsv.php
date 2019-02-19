<?php

namespace App\Http\Controllers\Police;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class GetPoliceDataFromCsv extends Controller
{
    //
    public function get_data() {
      //  var_dump(ini_get('allow_url_fopen'));exit;
         $file_n = Storage::path('data.csv');
         $file = fopen($file_n, "r") or die ("Nothing");
         $all_data = array();
        while(($data=fgetcsv($file,200,","))!==FALSE){
          
       
         }
         print_r($array);
         fclose($file);
      
        
       }  



}
