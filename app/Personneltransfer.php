<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personneltransfer extends Model
{
    //

    protected $table = 'personnel_transfer';

    protected $fillable = ['id','name','designation','office_id','updated_at'];

    protected $hidden = ['created_at'];

    public $incrementing = false;
}
