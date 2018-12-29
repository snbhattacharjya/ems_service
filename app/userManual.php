<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class userManual extends Model
{
    //

    protected $table = 'users_kolkata_south';

    public $incrementing = false;

    protected $hidden = ['created_at','updated_at'];
}
