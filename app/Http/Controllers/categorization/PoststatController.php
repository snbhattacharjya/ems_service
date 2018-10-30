<?php
namespace App\Http\Controllers\categorization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SubdivisionController;
use App\Http\Controllers\OfficeController;
use App\Category;
use App\Pppostrules;
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
			$subdivision_id='ALL';
            $category_id=$request->category_id;

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

					$sql="select id AS officecode,name AS officename from offices where  district_id='$district'";

                    $office=DB::select($sql);
                    $arr['office']=collect($office)->toArray();
					return response()->json($arr,200);
				}else if($category_clause!='ALL'){
					$sql="select id AS officecode,name AS officename from offices where category_id IN ($category_clause) AND district_id='$district'  ";
                    $office=DB::select($sql);
                    $arr['office']=collect($office)->toArray();
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
			  $subdivision_id='ALL';
			  $category_id=$request->category_id;
			  $office_id=$request->office_id;

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
				// if($subdivision_id != 'ALL')
	            // $clause=$clause."AND personnel.subdivision_id='".$subdivision_id."'";
                if($category_clause != 'ALL')
	            $clause=$clause."AND offices.category_id IN ($category_clause)";
                if($office_clause != 'ALL')
	            $clause=$clause."AND offices.id IN ($office_clause)";

				$sql="select id AS QualificationCode, name AS QualificationName FROM qualifications Where qualifications.id In(SELECT DISTINCT (qualification_id)FROM personnel INNER JOIN offices ON personnel.office_id=offices.id WHERE $clause)ORDER BY qualifications.id";
				//echo('<pre>');
				//dd($sql);
				$arr['qualification']=collect(DB::select($sql))->toArray();
				return response()->json($arr,200);
            }

		//************************************* Fetch Designation of office perssonel by clause   *********************/

		public function fetch_designation_of_pp(Request $request)
		{

			$district=$this->district;                  //********data pass through URL: district, subdivision_id, category _id, office_id ,basicpay,gradepay,qualification,not qualification ********////////////////////
			$subdivision_id='ALL';

			$category_id=$request->category_id;

			$office_id=$request->office_id;
			$qualification_id=$request->qualification_id;
			$actual_grade_pay=$grade_pay=$request->grade_pay;
			$actual_basic_pay=$basic_pay=$request->basic_pay;
		    $not_qualification=$request->not_qualification;
			$pay_level=$request->pay_level;
            

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

        if($grade_pay!=0){
		$clause.=" AND personnel.grade_pay BETWEEN $actual_grade_pay[0] AND $actual_grade_pay[1]";
		}
		
		if($pay_level!=0){
		$clause.=" AND personnel.pay_level BETWEEN $pay_level[0] AND $pay_level[1]";
		}

		if($qualification_clause != 'ALL' && $not_qualification == 0){
		$clause.=" AND personnel.qualification_id IN ($qualification_clause)";
		}
		else if($qualification_clause != 'ALL' && $not_qualification == 1){
			$clause.=" AND personnel.qualification_id NOT IN ($qualification_clause)";
		}

		if($district != 'ALL')
	     $clause=$clause." AND personnel.district_id='".$district."'";
		// if($subdivision_id != 'ALL')
	    // $clause=$clause." AND personnel.subdivision_id='".$subdivision_id."'";
        if($category_clause != 'ALL')
	    $clause=$clause." AND offices.category_id IN ($category_clause)";
        if($office_clause != 'ALL')
	    $clause=$clause." AND offices.id IN ($office_clause)";


	   $sql="SELECT DISTINCT(designation) AS Designation FROM personnel INNER JOIN offices ON personnel.office_id=offices.id WHERE $clause ORDER BY offices.officer_designation";
    //echo $sql;

	   $arr['designation']=collect(DB::select($sql))->toArray();
	   return response()->json($arr,200);


		}


	//************************************* fetch remarks of PP by condions *****************************************//

	 public function fetch_remarks_by_condition(Request $request)
	 {

		$district=$this->district;                  //********data pass through URL: district, subdivision_id, category _id, office_id ,basicpay,gradepay,qualification,not qualification ********////////////////////
		$subdivision_id='ALL';
		$category_id=$request->category_id;

		$office_id=$request->office_id;
		//dd(count($office_id));
		$qualification_id=$request->qualification_id;
		//dd(count($qualification_id));
		$actual_grade_pay=$grade_pay=$request->grade_pay;
		$actual_basic_pay=$basic_pay=$request->basic_pay;
		$not_qualification=$request->not_qualification;
		$gender=$request->gender;
		$age=$request->age;
		$designation=$request->designation;
		//dd(count($designation));
		$not_designation=$request->not_designation;
		$pay_level=$request->pay_level;
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

		if($grade_pay!=0){
			$clause.=" AND personnel.grade_pay BETWEEN $actual_grade_pay[0] AND $actual_grade_pay[1]";
			}
			
			if($pay_level!=0){
			$clause.=" AND personnel.pay_level BETWEEN $pay_level[0] AND $pay_level[1]";
			}
	

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
	$clause.=" AND personnel.designation NOT IN ($designation_clause)";

	}
	if($gender !='ALL')
	$clause.=" AND personnel.gender='".$gender."'";

	if($district != 'ALL')
	  $clause=$clause." AND personnel.district_id='".$district."'";
    // if($subdivision_id != 'ALL')
    //   $clause=$clause." AND personnel.subdivision_id='".$subdivision_id."'";
    if($category_clause != 'ALL')
      $clause=$clause." AND offices.category_id IN ($category_clause)";
    if($office_clause != 'ALL')
     $clause=$clause." AND offices.id IN ($office_clause)";

	$sql="SELECT remarks.id AS RemarksCode, remarks.name AS RemarksName, COUNT(*) AS PPCount FROM (personnel INNER JOIN offices ON personnel.office_id=offices.id) INNER JOIN remarks ON personnel.remark_id=remarks.id WHERE $clause GROUP BY remarks.id, remarks.name ORDER BY remarks.id";
	//dd($sql);
	$arr['remarks']=collect(DB::select($sql))->toArray();
	return response()->json($arr,200);

	 }
  //Load PP Post Status
   public function loadPostStat(){

   $post_stat_query="SELECT post_stat AS PostCode, poststatus AS PostName FROM pp_poststat ORDER BY post_stat";
   $arr['poststatus']=DB::select($post_stat_query);
   return response()->json($arr,200);


 }

 public function saveRule(Request $request){
    $subdiv='ALL';
    $govt=$request->category_id;
    $officecd=$request->office_id;
    $basic_pay=$request->basic_pay;
    $grade_pay=$request->grade_pay;
    $qualification=$request->qualification_id;
    $not_qualification=$request->not_qualification;
    $desg=$request->designation;
    $not_designation=$request->not_designation;
    $gender=$request->gender;
    $age=$request->age;
    $remarks=$request->remarks;
    $not_remarks=$request->not_remarks;
    $post_stat_from=$request->post_stat_from;
    $post_stat_to=$request->post_stat_to;
	$pay_level=$request->pay_level;
	


    $govt_clause='';
    for($i = 0; $i < count($govt); $i++){
        if($govt[$i] != 'ALL')
            $govt_clause.="'".$govt[$i]."',";
        else{
            $govt_clause='ALL';
            break;
        }
    }

$govt_clause=rtrim($govt_clause,',');


$officecd_clause='';
for($i = 0; $i < count($officecd); $i++){
if($officecd[$i] != 'ALL')
    $officecd_clause.="'".$officecd[$i]."',";
else{
    $officecd_clause='ALL';
    break;
}
}

$officecd_clause=rtrim($officecd_clause,',');

if(strlen($officecd_clause) > 200){
$arr['erorr']="Error in Saving Rule !!! Maximum Fifteen (15) Offices can be selected at One Time";
return response()->json($arr,401);
}
$qualification_clause='';
for($i = 0; $i < count($qualification); $i++){
if($qualification[$i] != 'ALL')
    $qualification_clause.="'".$qualification[$i]."',";
else{
    $qualification_clause='ALL';
    break;
}
}

$qualification_clause=rtrim($qualification_clause,',');
if(strlen($qualification_clause) > 50){
$arr['erorr']="Error in Saving Rule !!! Qualification Selection is too long";
return response()->json($arr,401);

}
if($not_qualification == 1 && $qualification_clause == 'ALL'){
$arr['erorr']="Error in Qulification Selection!!!";
return response()->json($arr,401);
}


$desg_clause='';
for($i = 0; $i < count($desg); $i++){
if($desg[$i] != 'ALL')
    $desg_clause.="'".$desg[$i]."',";
else{
    $desg_clause='ALL';
    break;
}
}

$desg_clause=rtrim($desg_clause,',');
if(strlen($desg_clause) > 200){
$arr['erorr']="Error in Saving Rule !!! Designation Selection is too long";
return response()->json($arr,401);
}

if($not_designation == 1 && $desg_clause == 'ALL'){
$arr['erorr']="Error in Designation Selection!!!";
return response()->json($arr,401);
}

$remarks_clause='';
if($remarks=='ALL' || in_array('ALL',$remarks)){
$remarks_clause="ALL";
}else{
for($i = 0; $i < count($remarks); $i++){
    $remarks_clause.="'".$remarks[$i]."',";

}
}
$remarks_clause=rtrim($remarks_clause,',');
if(strlen($remarks_clause) > 50){
$arr['erorr']="Error in Saving Rule !!! Remarks Selection is too long";
return response()->json($arr,401);
}
if($not_remarks == 1 && $remarks_clause == 'ALL'){
$arr['erorr']="Error in Remarks Selection!!!";
return response()->json($arr,401);
}




$basic_pay_clause=$basic_pay[0].'-'.$basic_pay[1];
if($grade_pay!=0){
   $grade_pay_clause=$grade_pay[0].'-'.$grade_pay[1];
}else{
	$grade_pay_clause=0;	
}
if($pay_level!=0){
    $pay_level_clause=$pay_level[0].'-'.$pay_level[1];
}else{
	$pay_level_clause=0;	
}
$id = DB::select('SELECT MAX(RuleID) AS MaxID FROM pp_post_rules');

$id = $id[0]->MaxID;
if(is_null($id)){
$rule_id=1;
}else{
 $rule_id = $id + 1;
}
DB::table('pp_post_rules')->insert(
['RuleID' =>$rule_id,
 'District' =>$this->district,
 'OfficeCategory' =>$govt_clause,
 'Office' => $officecd_clause,
 'BasicPay' =>$basic_pay_clause,
 'GradePay' =>$grade_pay_clause,
 'PayLevel' =>$pay_level_clause,
 'Qualification' =>$qualification_clause,
 'NotQualification' =>$not_qualification,
 'Designation' =>$desg_clause,
 'NotDesignation' =>$not_designation,
 'Remarks' =>$remarks_clause,
 'NotRemarks' =>$not_remarks,
 'Gender' =>$gender,
 'Age' =>$age,
 'PostStatFrom' =>$post_stat_from,
 'PostStatTo' =>$post_stat_to]
);

return response()->json('Save Successfully',201);
}

public function ruleList(){
	$district=$this->district;
	$sql="SELECT RuleID, PostStatFrom, PostStatTo, District, OfficeCategory, Office, BasicPay, GradePay, Qualification, NotQualification, Designation, NotDesignation, Remarks, NotRemarks, Gender, Age, RecordsAffected, AppliedDate, RecordsRevoked, RevokedDate FROM pp_post_rules where District='$district'  ORDER BY RuleID";
    $arr['rules']=collect(DB::select($sql))->toArray();
    return response()->json($arr,200);
}

public function grantRule(Request $request){
	$ruleId=$request->RuleID;
	$district=$this->district;
	$grantRule=Pppostrules::where('RuleID',$ruleId)
				->where('District',$district)
				->get();
if(!empty($grantRule)){  			
		$ruleSet=collect($grantRule)->toArray();
		$basic_pay=$ruleSet[0]['BasicPay'];
		$pay_level=$ruleSet[0]['PayLevel'];
		$grade_pay=$ruleSet[0]['GradePay'];
		$qualification=$ruleSet[0]['Qualification'];
		$not_qualification=$ruleSet[0]['NotQualification'];
		$desg=$ruleSet[0]['Designation'];
		$not_designation=$ruleSet[0]['NotDesignation'];
		$remarks=$ruleSet[0]['Remarks'];
		$not_remarks=$ruleSet[0]['NotRemarks'];
		$gender=$ruleSet[0]['Gender'];
		$District=$ruleSet[0]['District'];
		$govt=$ruleSet[0]['OfficeCategory'];
		$officecd=$ruleSet[0]['Office'];
		$post_stat_from=$ruleSet[0]['PostStatFrom'];
		$post_stat_to=$ruleSet[0]['PostStatTo'];
		 $basic_pay=explode("-",$basic_pay);
		 if($grade_pay!=0){
		   $grade_pay=explode("-",$grade_pay);
		 }
		 if($pay_level!=0){
			$pay_level=explode("-",$pay_level);
		}
		$clause="personnel.id != ''";
		$clause.=" AND personnel.basic_pay BETWEEN $basic_pay[0] AND $basic_pay[1]";
		if($grade_pay!=0){
		$clause.=" AND personnel.grade_pay BETWEEN $grade_pay[0] AND $grade_pay[1]";
		}
		if($pay_level!=0){
			$clause.=" AND personnel.pay_level BETWEEN $pay_level[0] AND $pay_level[1]";
		}
		if($qualification != 'ALL' && $not_qualification == 0)
			$clause.=" AND personnel.qualification_id IN ($qualification)";
		if($qualification != 'ALL' && $not_qualification == 1)
			$clause.=" AND personnel.qualification_id NOT IN ($qualification)";
		if($desg != 'ALL' && $not_designation == 0)
			$clause.=" AND personnel.designation IN ($desg)";
		if($desg != 'ALL' && $not_designation == 1)
			$clause.=" AND personnel.designation NOT IN ($desg)";
		if($remarks != 'ALL' && $not_remarks == 0)
			$clause.=" AND personnel.remark_id IN ($remarks)";
		if($remarks != 'ALL' && $not_remarks == 1)
			$clause.=" AND personnel.remark_id NOT IN ($remarks)";
		if($gender !='ALL')
			$clause.=" AND personnel.gender='".$gender."'";
		if($district==$District)
			$clause.=" AND personnel.district_id='".$District."'";
		if($govt != 'ALL')
			$clause.=" AND offices.category_id IN ($govt)";
		if($officecd != 'ALL')
			$clause.=" AND offices.id IN ($officecd)";
		if($post_stat_from != 'NA')
			$clause.="AND personnel.post_stat='".$post_stat_from."'";
		else
			$clause.=" AND personnel.post_stat=''";
			
		$clause.=" AND DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(personnel.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(personnel.dob, '00-%m-%d')) < 60";

		$today = date("Y-m-d H:i:s");
		 $grant_rule_query="UPDATE personnel INNER JOIN offices ON personnel.office_id=offices.id SET personnel.post_stat='$post_stat_to' WHERE $clause";


		$affected =DB::update($grant_rule_query); 
		$arr=array('GrantRecordsaffected'=>$affected,'GrantAppliedDate'=>$today);
				if($affected>0){
				$updateRule="UPDATE pp_post_rules SET Recordsaffected=$affected, AppliedDate='$today' WHERE RuleID=$ruleId";
				$updateRule =DB::update($updateRule);
				return response()->json($arr,200);
				}else{
					$arr=array('msg'=>'Can not Effected any row');

				}
         }
}

public function revokeRule(Request $request){
	$ruleId=$request->RuleID;
	$district=$this->district;
	$grantRule=Pppostrules::where('RuleID',$ruleId)
				->where('District',$district)
				->get();
			if(!empty($grantRule)){  			
			$ruleSet=collect($grantRule)->toArray();
			$basic_pay=$ruleSet[0]['BasicPay'];
			$grade_pay=$ruleSet[0]['GradePay'];
			$pay_level=$ruleSet[0]['PayLevel'];
			$qualification=$ruleSet[0]['Qualification'];
			$not_qualification=$ruleSet[0]['NotQualification'];
			$desg=$ruleSet[0]['Designation'];
			$not_designation=$ruleSet[0]['NotDesignation'];
			$remarks=$ruleSet[0]['Remarks'];
			$not_remarks=$ruleSet[0]['NotRemarks'];
			$gender=$ruleSet[0]['Gender'];
			$District=$ruleSet[0]['District'];
			$govt=$ruleSet[0]['OfficeCategory'];
			$officecd=$ruleSet[0]['Office'];
			 $post_stat_from=$ruleSet[0]['PostStatFrom'];
			 $post_stat_to=$ruleSet[0]['PostStatTo'];

			$basic_pay=explode("-",$basic_pay);
			
				if($grade_pay!=0){
				$grade_pay=explode("-",$grade_pay);
				}
				if($pay_level!=0){
					$pay_level=explode("-",$pay_level);
				}
			$clause="personnel.id != ''";
			$clause.=" AND personnel.basic_pay BETWEEN $basic_pay[0] AND $basic_pay[1]";
			    if($grade_pay!=0){
				$clause.=" AND personnel.grade_pay BETWEEN $grade_pay[0] AND $grade_pay[1]";
				}
				if($pay_level!=0){
					$clause.=" AND personnel.pay_level BETWEEN $pay_level[0] AND $pay_level[1]";
				}

			if($qualification != 'ALL' && $not_qualification == 0)
				$clause.=" AND personnel.qualification_id IN ($qualification)";
			if($qualification != 'ALL' && $not_qualification == 1)
				$clause.=" AND personnel.qualification_id NOT IN ($qualification)";
			if($desg != 'ALL' && $not_designation == 0)
				$clause.=" AND personnel.designation IN ($desg)";
			if($desg != 'ALL' && $not_designation == 1)
				$clause.=" AND personnel.designation NOT IN ($desg)";
			if($remarks != 'ALL' && $not_remarks == 0)
				$clause.=" AND personnel.remark_id IN ($remarks)";
			if($remarks != 'ALL' && $not_remarks == 1)
				$clause.=" AND personnel.remark_id NOT IN ($remarks)";
			if($gender !='ALL')
				$clause.=" AND personnel.gender='".$gender."'";
			if($district==$District)
				$clause.=" AND personnel.district_id='".$District."'";
			if($govt != 'ALL')
				$clause.=" AND offices.category_id IN ($govt)";
			if($officecd != 'ALL')
				$clause.=" AND offices.id IN ($officecd)";
			if($post_stat_from != 'NA')
			$clause.="AND personnel.post_stat='".$post_stat_to."'";
			else
			$clause.=" AND personnel.post_stat='$post_stat_to'";
	
			if($post_stat_from == 'NA')
	        $post_stat_from='';
			$clause.=" AND DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(personnel.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(personnel.dob, '00-%m-%d')) < 60";

			$today = date("Y-m-d H:i:s");
		    $grant_rule_query="UPDATE personnel INNER JOIN offices ON personnel.office_id=offices.id SET personnel.post_stat='$post_stat_from' WHERE $clause";
			$affected =DB::update($grant_rule_query); 
			$arr=array('RecordsRevoked'=>$affected,'RevokedDate'=>$today);
				
						$updateRule="UPDATE pp_post_rules SET RecordsRevoked=$affected, RevokedDate='$today' WHERE RuleID=$ruleId";
						$updateRule =DB::update($updateRule);
						return response()->json($arr,200);
	
                

			}

	 }
	 

	 public function queryRule(Request $request){
		$ruleId=$request->RuleID;
		$district=$this->district;
		$grantRule=Pppostrules::where('RuleID',$ruleId)
					->where('District',$district)
					->get();
	if(!empty($grantRule)){  			
			$ruleSet=collect($grantRule)->toArray();
			$basic_pay=$ruleSet[0]['BasicPay'];
			$grade_pay=$ruleSet[0]['GradePay'];
			$pay_level=$ruleSet[0]['PayLevel'];
			$qualification=$ruleSet[0]['Qualification'];
			$not_qualification=$ruleSet[0]['NotQualification'];
			$desg=$ruleSet[0]['Designation'];
			$not_designation=$ruleSet[0]['NotDesignation'];
			$remarks=$ruleSet[0]['Remarks'];
			$not_remarks=$ruleSet[0]['NotRemarks'];
			$gender=$ruleSet[0]['Gender'];
			$District=$ruleSet[0]['District'];
			$govt=$ruleSet[0]['OfficeCategory'];
			$officecd=$ruleSet[0]['Office'];
			$post_stat_from=$ruleSet[0]['PostStatFrom'];
			$post_stat_to=$ruleSet[0]['PostStatTo'];
	
			$basic_pay=explode("-",$basic_pay);
			
			if($grade_pay!=0){
				$grade_pay=explode("-",$grade_pay);
				}
				if($pay_level!=0){
					$pay_level=explode("-",$pay_level);
				}
	
			$clause="personnel.id != ''";
			$clause.=" AND personnel.basic_pay BETWEEN $basic_pay[0] AND $basic_pay[1]";
			if($grade_pay!=0){
				$clause.=" AND personnel.grade_pay BETWEEN $grade_pay[0] AND $grade_pay[1]";
				}
				if($pay_level!=0){
					$clause.=" AND personnel.pay_level BETWEEN $pay_level[0] AND $pay_level[1]";
				}
	
			if($qualification != 'ALL' && $not_qualification == 0)
				$clause.=" AND personnel.qualification_id IN ($qualification)";
			if($qualification != 'ALL' && $not_qualification == 1)
				$clause.=" AND personnel.qualification_id NOT IN ($qualification)";
			if($desg != 'ALL' && $not_designation == 0)
				$clause.=" AND personnel.designation IN ($desg)";
			if($desg != 'ALL' && $not_designation == 1)
				$clause.=" AND personnel.designation NOT IN ($desg)";
			if($remarks != 'ALL' && $not_remarks == 0)
				$clause.=" AND personnel.remark_id IN ($remarks)";
			if($remarks != 'ALL' && $not_remarks == 1)
				$clause.=" AND personnel.remark_id NOT IN ($remarks)";
			if($gender !='ALL')
				$clause.=" AND personnel.gender='".$gender."'";
			if($district==$District)
				$clause.=" AND personnel.district_id='".$District."'";
			if($govt != 'ALL')
				$clause.=" AND offices.category_id IN ($govt)";
			if($officecd != 'ALL')
				$clause.=" AND offices.id IN ($officecd)";
			if($post_stat_from != 'NA')
				$clause.="AND personnel.post_stat='".$post_stat_from."'";
			else
				$clause.=" AND personnel.post_stat=''";
				
			$clause.=" AND DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(personnel.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(personnel.dob, '00-%m-%d')) < 60";
	
			$today = date("Y-m-d H:i:s");
		$grant_rule_query="SELECT COUNT(personnel.id) AS PPCount from personnel INNER JOIN offices ON personnel.office_id=offices.id  WHERE $clause";
	
	
			$affected =collect(DB::select($grant_rule_query))->toArray(); 
			$arr['query']=array('queryval'=>$affected[0]->PPCount,'querydate'=>$today);
			return response()->json($arr,200);		
			 }
	}




 }
