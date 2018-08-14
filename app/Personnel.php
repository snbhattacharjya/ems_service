<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    protected $table = 'personnel';

    protected $fillable = ['id','name','designation','office_id'];

    protected $hidden = ['created_at', 'updated_at'];

    public $incrementing = 'false';

    public function office()
    {
        return $this->belongsTo('App\Office');
    }
}
