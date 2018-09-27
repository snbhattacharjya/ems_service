<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'offices';

    public $incrementing = false;

    protected $fillable = [ 'id', 'name', 'identification_code', 'subdivision_id', 'updated_at',

    ];

    protected $hidden = [ 'created_at',

    ];

    public function subdivision()
    {
        return $this->belongsTo('App\Subdivision');
    }

    public function personnel()
    {
        return $this->hasMany('App\Personnel');
    }
}
