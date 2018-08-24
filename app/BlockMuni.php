<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockMuni extends Model
{
    protected $table = 'block_munis';

    public $incrementing = false;

    protected $hidden = ['created_at','updated_at'];
}
