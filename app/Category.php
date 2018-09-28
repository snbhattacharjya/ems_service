<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    public $incrementing = false;
	protected $hidden = ['created_at','updated_at'];
}
