<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\DistrictPPdata;
use Illuminate\Support\Facades\DB;
class ProcessDistrictPPdata implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
       //$this->userID=auth('api')->user()->user_id;
       // $this->level=auth('api')->user()->level;
       // $this->district=auth('api')->user()->area;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
		  $this->district=auth('api')->user()->area;
		    $sql="SELECT count(*)id from offices where district_id='".$this->district."'";
			$totalOfficeQuery=DB::select($sql);
			$totalOffice=$totalOfficeQuery[0]->id;
		 if(!empty($totalOfficeQuery[0]->id)){
			$getFmalesql="SELECT count(*)gender from personnel INNER JOIN offices on offices.id=personnel.office_id where personnel.district_id='".$this->district."' and personnel.gender='F'";
			$officef = DB::select($getFmalesql);
			$totalFemale=$officef[0]->gender ;
			$getMalesql="SELECT count(*)gender from personnel INNER JOIN offices on offices.id=personnel.office_id where personnel.district_id='".$this->district."' and personnel.gender='M'";
			$officem = DB::select($getMalesql);
		    $totalMale=$officem[0]->gender;
		    $totalemployee=$totalMale +$totalFemale ;
			
			if(DistrictPPdata::where('district_id', '=',$this->district)->exists()){
			 DB::table('district_pp_data')
			->where('district_id',$this->district)
			->update(['pp_data'=>$totalOffice,'total_register_male'=>$totalMale,'total_register_female'=>$totalFemale,'total_register_emp'=>$totalemployee]);
		   //echo 'hi';
		   }else{
			   	
			 DB::table('district_pp_data')->insert(
                ['district_id'=>$this->district,'pp_data'=>$totalOffice,'total_register_male'=>$totalMale,'total_register_female'=>$totalFemale,'total_register_emp'=>$totalemployee]);
			}
    }
}

}