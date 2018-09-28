<?php

namespace App\categorization;

use Illuminate\Database\Eloquent\Model;

class Poststat extends Model
{
    //
	
	protected $table = 'pp_poststat';
    protected $fillable = [
	'post_stat','poststatus',
            ];
    public $incrementing = false;

    protected $hidden = ['user_code','posted_date'];

}
