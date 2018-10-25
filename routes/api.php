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
Route::get('/userauth', function (Request $request) {
    return auth('api')->user();
});
//Register Routes
Route::get('/a', 'PersonnelController@is_personnel');

Route::post('/register', 'Auth\RegisterController@register');
//Login Routes
Route::post('/login', 'Auth\LoginController@login');
//Logout Routes
Route::get('/logout', 'Auth\LoginController@logout')->middleware('auth:api');

Route::group([ 'middleware' => 'auth:api'], function()
{
//Report Routes
Route::get('/getdistrict', 'ReportassembleController@getAllDistrict');
Route::get('/assemblyreport/{district_id}', 'ReportassembleController@getAssmblyReport');
Route::get('/report', 'ReportController@getReport');
Route::get('/subdivisionreport/{district_id}', 'SudivreportController@reportOnSubdivsion');
//Add User
Route::get('/alluser', 'UserController@getallUsers');
Route::get('/creationlevel', 'UserController@getUserCreation');
Route::get('/sublevel/{id}', 'UserController@getUsercreationSubLevel');
Route::post('/createuser', 'UserController@createUser');//For Save TO data
Route::get('/levelsublevel', 'UserController@getUserLevelSublevel');
Route::get('/getbdo/{id}', 'UserController@getBDO');
Route::post('/changepassword', 'UserController@changePassword');
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
Route::get('/allassemblies', 'AssemblyConstituencyController@getAssembliesAll');

//PC Routes
Route::get('/pcs', 'ParliamentaryConstituencyController@getPcs');

//Qualification Routes
Route::get('/qualifications', 'QualificationController@getQualifications');

//Language Routes
Route::get('/languages', 'LanguageController@getLanguages');
Route::get('/remarks', 'PersonnelController@getRemarks');

//REPORT
Route::get('/print/{report}/{officeId}', 'Report\ReportController@report');
Route::get('/ifsc/{branch_ifsc}', 'PersonnelController@getIfsc');
Route::get('/export/{mode}', 'Export\UserExport@userexport');
//PP Category
Route::post('/setrule', 'categorization\PoststatController@saveRule');
Route::post('/officebysubdivision', 'categorization\PoststatController@getOfficeBySubCat');
Route::get('/subdivisioncat', 'categorization\PoststatController@getSubdivisionCat');
Route::get('/postStat', 'categorization\PoststatController@loadPostStat');
Route::post('/fetch_qualification_by_oficecode', 'categorization\PoststatController@fetch_qualification_by_oficecode');
Route::post('/fetch_designation_of_pp', 'categorization\PoststatController@fetch_designation_of_pp');
Route::post('/fetch_remarks_by_condition', 'categorization\PoststatController@fetch_remarks_by_condition');
//Rule Listing
Route::post('/rules', 'categorization\PoststatController@ruleList');
Route::get('/grantrules', 'categorization\PoststatController@grantRule');
});

//Route::post('/deo', 'UserController@diocreation');
//Route::get('/print/{report}/{officeId}', 'Report\ReportController@report');
//Route::get('/createpassword', 'UserController@createPassword');
//Route::get('/passwordinsert', 'UserController@passwordInsert');
//Route::get('/print/{report}/{officeId}', 'Report\ReportController@report');
//Route::get('/generateletter', 'GenerateLetterController@generateLetter');
//Route::get('/allassemblies', 'AssemblyConstituencyController@getAssembliesAll');
 Route::get('/export/{mode}/{token}', 'Export\UserExport@userexport');
 Route::get('/checkuser/{mode}/{token}', 'Export\UserExport@checkAuth');
