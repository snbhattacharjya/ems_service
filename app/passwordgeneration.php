<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class passwordgeneration extends Model
{
    //
    protected $table = 'user_random_password';
    protected $fillable = [
        'rand_id','rand_password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
       'created_at', 
    ];




}
