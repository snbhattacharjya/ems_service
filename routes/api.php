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
Route::middleware('auth:api')->get('/user','PermissionController@getPermission');
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return auth('api')->user();
});*/
//Register Routes
Route::post('/register', 'Auth\RegisterController@register');
//Login Routes
Route::post('/login', 'Auth\LoginController@login');
//Logout Routes
Route::get('/logout', 'Auth\LoginController@logout')->middleware('auth:api');

//Route::get('/createPassword', 'UserController@createPassword');
Route::group([ 'middleware' => 'auth:api'], function()
{
//Report Routes
Route::get('/report', 'ReportController@getReport');
Route::get('/subdivisionreport/{district_id}', 'SudivreportController@reportOnSubdivsion');
//Add User
Route::get('/alluser', 'UserController@getallUsers');
Route::get('/creationlevel', 'UserController@getUserCreation');
Route::get('/sublevel/{id}', 'UserController@getUsercreationSubLevel');
Route::post('/createuser', 'UserController@createUser');//For Save TO data
Route::get('/levelsublevel', 'UserController@getUserLevelSublevel');
Route::get('/getbdo/{id}', 'UserController@getBDO');
//Office Routes

Route::get('/offices/{subdivision_id}', 'OfficeController@getAllofficeBysubdivision');
Route::get('/offices', 'OfficeController@getAllOffices');
Route::get('/office/{id}', 'OfficeController@getOfficeById');
Route::post('/office', 'OfficeController@store');
Route::post('/office/update', 'OfficeController@update');

//Personnel Routes

Route::get('/personnelbyoffice/{officeid}', 'PersonnelController@getAllPersonnelbyoffice');
Route::post('/personnel', 'PersonnelController@store');
Route::get('/personnel/{id}', 'PersonnelController@getPersonnelById');
Route::get('/personnel', 'PersonnelController@getAllPersonnel');
Route::post('/personnel/update', 'PersonnelController@update');

//Subdivision Routes
Route::get('/subdivisions', 'SubdivisionController@getSubdivisions');

//BlockMuni Routes
Route::get('/blockmunis', 'BlockMuniController@getBlockMunis');

//Police Station Routes
Route::get('/policestations', 'PoliceStationController@getPoliceStations');

//Category Routes
Route::get('/categories', 'CategoryController@getCategories');

//Institute Routes
Route::get('/institutes', 'InstituteController@getInstitutes');

//Assembly Routes
Route::get('/assemblies', 'AssemblyConstituencyController@getAssemblies');

//PC Routes
Route::get('/pcs', 'ParliamentaryConstituencyController@getPcs');

//Qualification Routes
Route::get('/qualifications', 'QualificationController@getQualifications');

//Language Routes
Route::get('/languages', 'LanguageController@getLanguages');

});

Route::post('/deo', 'UserController@diocreation');
