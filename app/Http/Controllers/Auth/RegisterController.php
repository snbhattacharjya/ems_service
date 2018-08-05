<?php

namespace App\Http\Controllers\Auth;

use App\User;
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
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:50|unique:users',
            'mobiile' => 'required|string|max:10|unique:users',
            'password' => 'required|string|min:6',
            'designation' => 'required|string|max:50',
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'aadhaar' => $request->aadhaar,
            'designation' => $request->designation,
            'level' => $request->level,
            'area' => $request->area,
            'is_active' => $request->is_active,
            'change_password' => $request->change_password,
        ]);
    }
}
