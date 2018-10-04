<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Passwordgeneration extends Model
{
    //
    protected $table = 'user_random_password';
    protected $fillable = [
        'rand_id','rand_password',
    ];

    protected $hidden = [
       'created_at', 
    ];




}
