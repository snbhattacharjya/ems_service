<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Office Routes

Route::get('/offices', 'OfficeController@getAllOffices');
Route::post('/office', 'OfficeController@store');

//Personnel Routes

Route::post('/personnel', 'PersonnelController@store');

//Subdivision Routes
Route::get('/subdivisions', 'SubdivisionController@getSubdivisions');
Route::get('/test','Auth\RegisterController@test');
Route::post('/register','Auth\RegisterController@register');
Route::post('/login','Auth\LoginController@login');
