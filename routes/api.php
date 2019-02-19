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
  Route::post('/edituser', 'UserController@editUser');
//Report Routes
Route::get('/getdistrict', 'ReportassembleController@getAllDistrict');
Route::get('/assemblyreport/{district_id}', 'ReportassembleController@getAssmblyReport');
Route::post('/updateassemblyreport', 'ReportassembleController@updateAssmblyByReport');
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

Route::get('/resetpassword/{officeId}', 'OfficeController@resetPassword');
//Office Routes

Route::get('/offices/{subdivision_id}', 'OfficeController@getAllofficeBysubdivision');
Route::get('/offices', 'OfficeController@getAllOffices');
Route::get('/office/{id}', 'OfficeController@getOfficeById');
Route::post('/office', 'OfficeController@store');
Route::post('/office/update', 'OfficeController@update');
Route::get('/officetype/{officeId}', 'OfficeController@getOfficeType');
Route::post('/officesearch', 'OfficeController@searchOffice');
Route::post('/ppagree', 'OfficeController@ppAgree');
Route::get('/getppagree', 'OfficeController@getppAgree');
//Personnel Routes

Route::get('/personnelbyoffice/{officeid}', 'PersonnelController@getAllPersonnelbyoffice');
Route::post('/personnel', 'PersonnelController@store');
Route::get('/personnel/{id}', 'PersonnelController@getPersonnelById');
Route::get('/personnel', 'PersonnelController@getAllPersonnel');
Route::post('/personnel/update', 'PersonnelController@update');
Route::get('/accountcheck/{bankNumber}', 'PersonnelController@duplicateBankAccount');

//Subdivision Routes
Route::get('/subdivisions', 'SubdivisionController@getSubdivisions');

//BlockMuni Routes
Route::get('/blockmunis', 'BlockMuniController@getBlockMunis');
Route::get('/getblockbysubdission/{subdivision_id}', 'SubdivisionController@getBlockmuniBysubdivision');

//Police Station Routes
Route::get('/policestations/{subdivision_id}', 'PoliceStationController@getPoliceStations');


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
Route::get('/officeentrystatus', 'Report\ReportOfficeEntryStatusController@getOfficeEntryStatus');
Route::get('/personelProgressReport', 'Report\PollingPersonelProgressController@pollingPersonelProgressReport');
Route::get('/subdivisionwiseassemblyreport', 'SudivreportController@subdivisionWiseAssemblyReport');
Route::get('/officepartialentrystatus', 'Report\ReportOfficeEntryStatusController@getOfficePartialEntryStatus');
Route::get('/officecompletestatus', 'Report\ReportOfficeEntryStatusController@getOfficeEntryComplete');
Route::get('/groupwisepp', 'Report\ReportController@groupWisePP');
//29122018
Route::get('/personnelExport', 'Export\UserExport@personnelExport');
Route::get('/officeCategoryWise_pp2', 'Report\ReportController@officeCategopryWisePPadded');

//31122018
Route::get('/exportoffice', 'Export\UserExport@officeExport');

//02012018
Route::get('/poststatusWise_pp', 'Report\ReportController@officeCategopryWisePostStatus');
Route::get('/macrolevel_statistics', 'Report\ReportController@macroLevelStatictis');
Route::get('/allpoststatus', 'categorization\PoststatController@PollingPost');

Route::get('/officenotstarted', 'Report\ReportOfficeEntryStatusController@officeNotStarted');
Route::get('/remarkwise_report', 'Report\RemarksWiseController@RemarksWisePersonnelStatus');
Route::get('/personelProgressstatus', 'Report\PollingPersonelProgressController@districtWisePPstatistic');

//PP Category
Route::post('/setrule', 'categorization\PoststatController@saveRule');
Route::post('/officebysubdivision', 'categorization\PoststatController@getOfficeBySubCat');
Route::get('/subdivisioncat', 'categorization\PoststatController@getSubdivisionCat');
Route::get('/postStat', 'categorization\PoststatController@loadPostStat');
Route::post('/fetch_qualification_by_oficecode', 'categorization\PoststatController@fetch_qualification_by_oficecode');
Route::post('/fetch_designation_of_pp', 'categorization\PoststatController@fetch_designation_of_pp');
Route::post('/fetch_remarks_by_condition', 'categorization\PoststatController@fetch_remarks_by_condition');
//Rule Listing
Route::get('/rules', 'categorization\PoststatController@ruleList');
Route::get('/grantrules/{RuleID}', 'categorization\PoststatController@grantRule');
Route::get('/revokerule/{RuleID}', 'categorization\PoststatController@revokeRule');
Route::get('/queryrule/{RuleID}', 'categorization\PoststatController@queryRule');
Route::post('/setpoststat', 'categorization\ManualPoststatSetController@GetPersonnelByOfficeAndPoststat');
Route::post('/savepoststatmanual', 'categorization\ManualPoststatSetController@postStatManualSave');
Route::get('/deleterule/{id}', 'categorization\PoststatController@deleteRule');
//Analytics

Route::get('/analytics', 'AnalyticsController@totalUsers');
//Office delete
Route::get('/issearch/{s}', 'officeDeletionRestoreController@searchOffice');
Route::get('/isdelete/{id}', 'officeDeletionRestoreController@deleteOffice');
Route::get('/isrestore/{id}', 'officeDeletionRestoreController@restoreDeletedOffice');
Route::get('/gettrashedOffice', 'officeDeletionRestoreController@trashedOffice');
Route::get('/pollingPost', 'categorization\PoststatController@PollingPost');
//Transfer Personnel
Route::get('/transferlist', 'PersonneltransferController@getTransferList');
Route::post('/dotransfer', 'PersonneltransferController@doTransfer');
//Excemption
Route::post('/getpersonnnelforexcemption', 'ExcemptionController@SearchForExemption');
Route::post('/doexception', 'ExcemptionController@doExcemption');
Route::get('/remarkforexcemption', 'ExcemptionController@getRemarks');


//Data Sharing
Route::post('/getsharerequiremnet', 'DatasharingController@queryForDataShare');
Route::post('/instructforshare', 'DatasharingController@instructForDataShare');
Route::get('/instructlist', 'DatasharingController@getInstructionForDataShare');
Route::get('/getdeoshareinstruction', 'DatasharingController@getShareRequest');
//Route::get('/getcategorywisedistrictrequirement/{category}', 'DatasharingController@getRequirementAvailability');
Route::post('/processtosharedata', 'DatasharingController@doDataShare');

//

Route::get('/officenotstartedbydistrict/{id}', 'Report\ReportOfficeEntryStatusController@officeNotStartedbydistrict');
Route::get('/deleterule/{id}', 'categorization\PoststatController@deleteRule');
Route::post('/getpersonnelbypoststat', 'categorization\ManualPoststatSetController@GetPersonnelByPoststat');
//
Route::post('/revokeexcemption', 'ExcemptionController@revokeExcemption');
Route::post('/getPPListByDistinctDesignation', 'categorization\ManualPoststatSetController@getPPListByDistinctDesignation');
Route::post('/adhokeppstatus', 'categorization\ManualPoststatSetController@createAdhocRule');
Route::get('/institutewisepp', 'Report\ReportController@instituteWisePP');
//
Route::post('/mismatchreport', 'Report\ReportController@groupwiseDesignationMismatchReport');
Route::get('/getmismatchlist/{designation}/{emp_group}','Report\ReportController@getMisMatchList');
//

Route::get('/revokepoststat/{post_stat}', 'categorization\PoststatController@revokePostStat');
//
Route::post('/prequeryrule', 'categorization\PoststatController@prequeryrule');
//
Route::get('/getexcemptionbyage', 'ExcemptionController@getExcemptionByAge');
Route::post('/getexcemptionbydesignation', 'ExcemptionController@getExemptionListByDesignation');
//
Route::get('/blockwiseofficepersonel/{office_blockmuni}', 'Report\ReportController@blockwiseOfficepersonel');

Route::post('/bulkupdatebypoststattype', 'categorization\ManualPoststatSetController@bulkUpdateByPostStatType');
///
Route::post('/updateshareinstruction', 'DatasharingController@updateInstructForDataShare');
Route::get('/getcategorywisedistrictrequirement/{category}/{gender}', 'DatasharingController@getRequirementAvailability');

///
Route::post('/deleteshareinstruction', 'DatasharingController@deleteInstructForDataShare');
Route::get('/getcontact', 'PersonnelController@getContact');
//
Route::get('/getExcemptedOfficeList', 'ExcemptionController@getExcemptedOfficeList');
Route::post('/getexemptedlist', 'ExcemptionController@getExemptedList');
Route::post('/revokeexcemptionbytype', 'ExcemptionController@revokeExemptionByType');
//
Route::get('/getnoepic', 'Report\RemarksWiseController@getNoEpic');

});
Route::get('/getPolice', 'Police\GetPoliceDataFromCsv@get_data');

//Route::post('/deo', 'UserController@diocreation');
//Route::get('/print/{report}/{officeId}', 'Report\ReportController@report');
 // Route::get('/createpassword', 'UserController@createPassword');
//Route::get('/passwordinsert', 'UserController@passwordInsert');
//Route::get('/print/{report}/{officeId}', 'Report\ReportController@report');
//Route::get('/generateletter', 'GenerateLetterController@generateLetter');
//Route::get('/allassemblies', 'AssemblyConstituencyController@getAssembliesAll');
//  Route::get('/export/{mode}/{token}', 'Export\UserExport@userexport');
//  Route::get('/checkuser/{mode}/{token}', 'Export\UserExport@checkAuth');
//  Route::get('/exportpersonnel', 'Export\UserExport@export');
//  Route::get('/permission', 'UserController@getDefaultMenuPermission_To_assignPermission');
