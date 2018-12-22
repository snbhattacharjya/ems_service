<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class officeDeleteRestore extends Model
{
    //
    protected $table = 'offices_deleted';

    public $incrementing = false;

    protected $fillable = [ 'id', 'name', 'identification_code', 'subdivision_id', 'updated_at',

    ];

    protected $hidden = [ 'created_at',

    ];


}
