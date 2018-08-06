<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\UserLevel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */



    public function register(Request $request)
    {
        /*$request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:50|unique:users',
            'mobiile' => 'required|string|max:10|unique:users',
            'password' => 'required|string|min:6',
            'designation' => 'required|string|max:50',
        ]);*/

        return User::create([
            'name' => 'Saikat',
            'email' => 'snbhattacharjya@gmail.com',
            'password' => Hash::make('secret'),
            'mobile' => '9831818461',
            'aadhaar' => '397568560883',
            'designation' => 'ADIO NIC Hooghly',
            'level' => 1,
            'area' => 1,
            'is_active' => 1,
            'change_password' => 1,
        ]);
    }

    public function test(){

        $levels = UserLevel::all();
        return response()->json($levels,200);
    }
}
