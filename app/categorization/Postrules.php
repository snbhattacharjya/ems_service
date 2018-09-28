<?php

namespace App\categorization;

use Illuminate\Database\Eloquent\Model;

class Postrules extends Model
{
   protected $table = 'pp_post_rules';
    protected $fillable = [
	'RuleID','Subdivision','OfficeCategory','Office','BasicPay','GradePay','Qualification','NotQualification',
	 'Remarks','NotRemarks','Gender','Age','PostStatFrom','PostStatTo','RecordsAffected','AppliedDate','RecordsRevoked',
	 'RevokedDate',
            ];
    public $incrementing = false;

   // protected $hidden = ['user_code','posted_date'];
}
