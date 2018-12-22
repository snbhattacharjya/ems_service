<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class userDeleteRestore extends Model
{
    //
    protected $table = 'users_deleted';

    public $incrementing = false;

    protected $hidden = ['created_at','updated_at'];
}
