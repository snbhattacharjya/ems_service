<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'offices';

    public $incrementing = false;

    protected $fillable = [ 'id', 'name', 'identification_code', 'subdivision_id',

    ];

    protected $hidden = [ 'created_at', 'updated_at',

    ];
}
