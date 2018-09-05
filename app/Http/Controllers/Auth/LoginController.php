<?php

namespace App\Http\Controllers\Auth;

use DB;
use Auth;
use Cookie;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.

    */
    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client;

         try
         {
             $response = $http->post(config('services.passport.login_endpoint'), [
                 'form_params' => [
                     'grant_type' => 'password',
                     'client_id' => config('services.passport.client_id'),
                     'client_secret' => config('services.passport.client_secret'),
                     'username' => $request->username,
                     'password' => $request->password,
                     'scope' => '',
                 ],
             ]);

             return $response->getBody();
         } catch (\GuzzleHttp\Exception\BadResponseException $e) {
             if ($e->getCode() === 400) {
                 return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
             } else if ($e->getCode() === 401) {
                 return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
             }
            return response()->json('Something went wrong on the server.', $e->getCode());
        }
    }

    public function logout(Request $request){
        auth('api')->user()->tokens->each(function ($token, $key) {
            $token->delete();
            DB::table('oauth_refresh_tokens')->where('access_token_id', $token->id)->delete();
        });

        return response()->json('Logged out successfully', 200);
    }


}
