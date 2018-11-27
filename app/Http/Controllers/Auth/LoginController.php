<?php

namespace App\Http\Controllers\Auth;

use DB;
use Auth;
use Cookie;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
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
    |
    | Ref- https://gist.github.com/vielhuber/f5467684da8a75071e18add9884dfef9
    */
    public function login(Request $request)
    {
        return $this->proxy([
            'grant_type' => 'password',
            'username' =>base64_decode(base64_decode($request->username)),
            'password' =>base64_decode(base64_decode( $request->password))
        ]);
    }
    public function refresh(Request $request)
    {
        return $this->proxy([
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->cookie('refreshToken')
        ]);
    }
    public function proxy($params)
    {
        $http = new Client();

        $response = $http->post(config('services.passport.login_endpoint'), [
            'form_params' => array_merge($params, [
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'scope' => '*',
            ]),
            'http_errors' => false
        ]);
        if ($response->getStatusCode() == 200){
                $data = json_decode((string)$response->getBody());
              
              
                // attach a refresh token to the response via HttpOnly cookie
                return response([
                    'access_token' => $data->access_token,
                    'expires_in' => $data->expires_in
                ])->cookie(
                    'refreshToken',
                    $data->refresh_token,
                    (24*60*60*10), // 10 days
                    null,
                    null,
                    false,
                    true // HttpOnly
                );
      

        }
        else if ($response->getStatusCode() === 400) {
            return response()->json('Invalid Request. Please enter a username or a password.', $response->getStatusCode());
        } else if ($response->getStatusCode() === 401) {
            return response()->json('Your credentials are incorrect. Please try again', $response->getStatusCode());
        }else{
            return response()->json('Something went wrong on the server.', $response->getStatusCode());
        }
    }
    public function logout(Request $request)
    {
        //return response()->json(auth('api')->user());
        $accessToken = auth('api')->user()->token();
        DB::table('oauth_refresh_tokens')->where('access_token_id', $accessToken->id)->delete();
        $accessToken->delete();
        return response()->json('Succesfully loged out',204)->cookie(Cookie::forget('refreshToken'));
    }
}
