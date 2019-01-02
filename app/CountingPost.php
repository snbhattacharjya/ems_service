<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountingPost extends Model
{
    //
    protected $table = 'counting_posts';

    public $incrementing = false;
}
