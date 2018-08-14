<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subdivision extends Model
{
    protected $table = 'subdivisions';

    public $incrementing = 'false';

    protected $hidden = ['created_at','updated_at'];

    public function offices()
    {
        return $this->hasMany('App\Office');
    }
}
