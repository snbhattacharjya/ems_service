<?php
namespace App\Http\Controllers\categorization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SubdivisionController;
use App\Http\Controllers\OfficeController;
use App\Category;
use Illuminate\Support\Facades\DB;
class PoststatController extends Controller
{
          public function __construct()
				{	$this->userID=auth('api')->user()->user_id;
					$this->level=auth('api')->user()->level;
					$this->district=auth('api')->user()->area;
				}
	       public function getSubdivision(){
			  $arr=array();
			  $arr['subdivision']=(new SubdivisionController)->getSubdivisions();
			  $arr['category']=Category::all();
			  $arr['category'][]=array('id'=>'ALL','name'=>'All');
			  return response()->json($arr,200);
			
			}
	       public function getOfficeBySubCat(Request $request){
			 $district=$this->district;
             $subdivision_id=$request->subdivision_id;
             $category_id=$request->category_id; 
			 if(!empty($subdivision_id) and !empty($category_id)){
			 $sql="select id,name from offices where district_id='$district' and subdivision_id='$subdivision_id' and category_id='$category_id'";  
			  $arr['office']=DB::select($sql); 
              return response()->json($arr,200);			 
			 }else{
			  $arr['erorr']="Please select all option";	 
			  return response()->json($arr,200);	 
			 }   
		   }
  
 }
