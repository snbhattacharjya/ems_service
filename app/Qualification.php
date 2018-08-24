<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    protected $table = 'qualifications';

    protected $hidden = ['created_at','updated_at'];

    public $incrementing = false;
}
