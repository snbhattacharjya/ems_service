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
	       public function getSubdivisionCat(){
			  $arr=array();
			  $arr['subdivision']=(new SubdivisionController)->getSubdivisions();
			  $arr['office_category']=Category::all();
			  $arr['office_category'][]=array('id'=>'ALL','name'=>'All');
			  return response()->json($arr,200);
			
			}
	        public function getOfficeBySubCat(Request $request){
			$district=$this->district;
			$subdivision_id=$request->subdivision_id; 
			$category_id[]=$request->category_id;
			$category_clause='';
			if(!empty($subdivision_id) and !empty($category_id)){
				for($i = 0; $i < count($category_id); $i++){
					if($category_id[$i] != 'ALL'){
					$category_clause.=$category_id[$i].",";
					}else{
						$category_clause='ALL';
						break;
					}
				}
			

				$category_clause=rtrim($category_clause,',');
			
				if($category_clause == 'ALL'){

					$sql="select id AS officecode,name AS officename from offices where subdivision_id='$subdivision_id' AND district_id='$district'";
					
					$arr['office']=DB::select($sql);
					return response()->json($arr,200);
				}else if($category_clause!='ALL'){
					$sql="select id AS officecode,name AS officename from offices where category_id IN ($category_clause) AND subdivision_id='$subdivision_id'AND district_id='$district'  ";
				  
				
					$arr['office']=DB::select($sql);
					return response()->json($arr,200);

				}else
				{
					$arr['erorr']="Please select all option";	 
					return response()->json($arr,200);	

				}

		

			}


		
		  }

		  //*************************** fetch qualification of PP using office code  *********************************//
		  public function fetch_qualification_by_oficecode (Request $request)
		  
		  {
			  $district=$this->district;                           ///////////////////data pass through URL: district, subdivision_id, category _id, office_id  ///////////////////
			  $subdivision_id=$request->subdivision_id;
			  $category_id[]=$request->category_id;
			  $office_id[]=$request->office_id;

			  $category_clause='';
			  if(!empty($subdivision_id) and !empty($category_id)){
				  for($i = 0; $i < count($category_id); $i++){
					  if($category_id[$i] != 'ALL')
					  $category_clause.=$category_id[$i].",";
			  
					  else{
						  $category_clause='ALL';
						  break;
					  }
				  }
				}
			  
				  $category_clause=rtrim($category_clause,',');

				  $office_clause='';
               for($i = 0; $i < count($office_id); $i++){
	             if($office_id[$i] != 'ALL')
		         $office_clause.=$office_id[$i].",";
	          else{
		            $office_clause='ALL';
		        break;
			   }
			   
				}
				$office_clause=rtrim($office_clause,',');
				$clause="";

				if($district != 'ALL')
	            $clause=$clause."personnel.district_id='".$district."'";
				if($subdivision_id != 'ALL')
	            $clause=$clause."AND personnel.subdivision_id='".$subdivision_id."'";
                if($category_clause != 'ALL')
	            $clause=$clause."AND offices.category_id IN ($category_clause)";
                if($office_clause != 'ALL')
	            $clause=$clause."AND offices.id IN ($office_clause)";

				$sql="select id AS QualificationCode, name AS QualificationName FROM qualifications Where qualifications.id In(SELECT DISTINCT (qualification_id)FROM personnel INNER JOIN offices ON personnel.office_id=offices.id WHERE $clause)ORDER BY qualifications.id";		
				//echo('<pre>');
				//dd($sql);
				$arr['office']=DB::select($sql);
				return response()->json($arr,200);



			  
		  }

		//************************************* Fetch Designation of office perssonel by clause   *********************/
		
		public function fetch_designation_of_pp(Request $request)
		{

			$district=$this->district;                  //********data pass through URL: district, subdivision_id, category _id, office_id ,basicpay,gradepay,qualification,not qualification ********////////////////////
			$subdivision_id=$request->subdivision_id;
			//$category_id=explode(",",$request->category_id);
			$category_id=explode(",",$request->category_id);
			//dd($category_id);
			$office_id[]=$request->office_id;
			$qualification_id[]=$request->qualification_id;
			$grade_pay[]=$request->grade_pay;
			$basic_pay[]=$request->basic_pay;
		//	dd($basic_pay
		//);
			
			$basic_pay1=$basic_pay[0];
			$grade_pay1=$grade_pay[0];
			$actual_grade_pay=explode(",",$grade_pay1);
			//dd($actual_grade_pay[1]);
			$actual_basic_pay=explode(",",$basic_pay1);
			
			$not_qualification=$request->not_qualification;


			
			$category_clause='';
			if(!empty($subdivision_id) and !empty($category_id)){
				for($i = 0; $i < count($category_id); $i++){
					if($category_id[$i] != 'ALL')
					$category_clause.=$category_id[$i].",";
			
					else{
						$category_clause='ALL';
						break;
					}
				}
			  }

			  
			  $category_clause=rtrim($category_clause,',');

			  $office_clause='';
		   for($i = 0; $i < count($office_id); $i++){
			 if($office_id[$i] != 'ALL')
			 $office_clause.=$office_id[$i].",";
			else
			{
				$office_clause='ALL';
			break;
		      }
		   
			}
			$office_clause=rtrim($office_clause,',');


			$qualification_clause='';
           for($i = 0; $i < count($qualification_id); $i++){
	       if($qualification_id[$i] != 'ALL')
		   $qualification_clause.=$qualification_id[$i].",";
		  else
		  {
		         $qualification_clause='ALL';
		         break;
		  }
			  
         }

		$qualification_clause=rtrim($qualification_clause,',');
		
		$clause="";
		$clause.=" personnel.basic_pay BETWEEN $actual_basic_pay[0] AND $actual_basic_pay[1]";
		

		$clause.=" AND personnel.grade_pay BETWEEN $actual_grade_pay[0] AND $actual_grade_pay[1]";

		if($qualification_clause != 'ALL' && $not_qualification == 0){
			$clause.=" AND personnel.qualification_id IN ($qualification_clause)";
		}
		else if($qualification_clause != 'ALL' && $not_qualification == 1){
			$clause.=" AND personnel.qualification_id NOT IN ($qualification_clause)";
		}

		if($district != 'ALL')
	     $clause=$clause." AND personnel.district_id='".$district."'";
		if($subdivision_id != 'ALL')
	    $clause=$clause." AND personnel.subdivision_id='".$subdivision_id."'";
        if($category_clause != 'ALL')
	    $clause=$clause." AND offices.category_id IN ($category_clause)";
        if($office_clause != 'ALL')
	    $clause=$clause." AND offices.id IN ($office_clause)";


	   $sql="SELECT DISTINCT(designation) AS Designation FROM personnel INNER JOIN offices ON personnel.office_id=offices.id WHERE $clause ORDER BY offices.officer_designation";


	   $arr['designation_pp']=DB::select($sql);
	   return response()->json($arr,200);


		}


	//************************************* fetch remarks of PP by condions *****************************************//	

	 public function fetch_remarks_by_condition(Request $request)
	 {

		$district=$this->district;                  //********data pass through URL: district, subdivision_id, category _id, office_id ,basicpay,gradepay,qualification,not qualification ********////////////////////
		$subdivision_id=$request->subdivision_id;
		$category_id=explode(",",$request->category_id);

		$office_id=explode(",",$request->office_id);
		//dd(count($office_id));
		$qualification_id=explode(",",$request->qualification_id);
		//dd(count($qualification_id));
		$grade_pay[]=$request->grade_pay;
		$basic_pay[]=$request->basic_pay;
		$not_qualification=$request->not_qualification;
		$gender=$request->gender;
		$age=$request->age;
		$designation=explode(",",$request->designation);
		//dd(count($designation));
		$not_designation=$request->not_designation;
		$basic_pay1=$basic_pay[0];
		$grade_pay1=$grade_pay[0];
		
		
		$actual_grade_pay=explode(",",$grade_pay1);
		//dd($actual_grade_pay[1]);
		$actual_basic_pay=explode(",",$basic_pay1);

		    $category_clause='';
			if(!empty($subdivision_id) and !empty($category_id)){
				for($i = 0; $i < count($category_id); $i++){
					if($category_id[$i] != 'ALL')
					$category_clause.=$category_id[$i].",";
			
					else{
						$category_clause='ALL';
						break;
					}
				}
			  }
			  $category_clause=rtrim($category_clause,',');

			  $office_clause='';
			  for($i = 0; $i < count($office_id); $i++){
				if($office_id[$i] != 'ALL')
				$office_clause.=$office_id[$i].",";
			 else{
				   $office_clause='ALL';
			   break;
			  }
			  
			   }
			   $office_clause=rtrim($office_clause,',');

			   
			$qualification_clause='';
           for($i = 0; $i < count($qualification_id); $i++){
	       if($qualification_id[$i] != 'ALL')
		   $qualification_clause.=$qualification_id[$i].",";
	      else{
		         $qualification_clause='ALL';
		         break;
			  }
			  
         }

		$qualification_clause=rtrim($qualification_clause,',');

		if(strlen($qualification_clause) > 50)
		{
		$arr['erorr']="Qualification length can not be greater than 50";	 
		return response()->json($arr,200);	
		}


		$designation_clause='';
      for($i = 0; $i < count($designation); $i++){
	if($designation[$i] != 'ALL')
		$designation_clause.="'".$designation[$i]."',";
	else{
		$designation_clause='ALL';
		break;
	}
}

     $designation_clause=rtrim($designation_clause,',');


       if(strlen($designation_clause) > 200)
      {
       $arr['erorr']="designation length can not be greater than 200";	 
       return response()->json($arr,200);	
      }
        $clause="";
		$clause.=" personnel.basic_pay BETWEEN $actual_basic_pay[0] AND $actual_basic_pay[1]";
		

		$clause.=" AND personnel.grade_pay BETWEEN $actual_grade_pay[0] AND $actual_grade_pay[1]";
		if($qualification_clause != 'ALL' && $not_qualification == 0){
			$clause.=" AND personnel.qualification_id IN ($qualification_clause)";
		}
		else if($qualification_clause != 'ALL' && $not_qualification == 1){
			$clause.=" AND personnel.qualification_id NOT IN ($qualification_clause)";
		}

		
	if($designation_clause != 'ALL' && $not_designation == 0)
	 {
	$clause.=" AND personnel.designation IN ($designation_clause)";

	 }

	else if($designation_clause != 'ALL' && $not_designation == 1)
	{
	$clause.=" AND personnel.designation NOT IN ($desg_clause)";

	}
	if($gender !='ALL')
	$clause.=" AND personnel.gender='".$gender."'";

	if($district != 'ALL')
	  $clause=$clause." AND personnel.district_id='".$district."'";
    if($subdivision_id != 'ALL')
      $clause=$clause." AND personnel.subdivision_id='".$subdivision_id."'";
    if($category_clause != 'ALL')
      $clause=$clause." AND offices.category_id IN ($category_clause)";
    if($office_clause != 'ALL')
     $clause=$clause." AND offices.id IN ($office_clause)";

	$sql="SELECT remarks.id AS RemarksCode, remarks.name AS RemarksName, COUNT(*) AS PPCount FROM (personnel INNER JOIN offices ON personnel.office_id=offices.id) INNER JOIN remarks ON personnel.remark_id=remarks.id WHERE $clause GROUP BY remarks.id, remarks.name ORDER BY remarks.id";
	//dd($sql);
	$arr['remarks']=DB::select($sql);
	return response()->json($arr,200);

	 }
  //Load PP Post Status
   public function loadPostStat(){
   
   $post_stat_query="SELECT post_stat AS PostCode, poststatus AS PostName FROM pp_poststat ORDER BY post_stat";
   $arr['poststatus']=DB::select($post_stat_query);
   return response()->json($arr,200);
   

}















 }
