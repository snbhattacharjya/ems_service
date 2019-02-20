<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApitokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','user_id', 'email', 'password', 'mobile', 'designation', 'aadhaar', 'level', 'area', 'is_active', 'change_password','user_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at'
    ];
    //By Sumit 01-09-2018
    public function findForPassport($identifier) {
        return $this->orWhere('email', $identifier)->orWhere('user_id', $identifier)->first();
    }
    public function passwgen()
    {
        return $this->belongsToMany('App\passwordgeneration');
    }


}
