<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Roles
0 : superadmin, all privileges
1 : A-Club
2 : MRG
3 : CAT
4 : UOB
5 : Sales
*/

Route::get('/', 'Auth\LoginController@index')->middleware('auth');

Auth::routes();

Route::get('/adduser', [
	'as' => 'adduser',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0'],
	function(){ 
		return view('auth/register');
	},
	]);
/* to be uncommented
Route::get('/register',
	function(){
		return redirect()->route('adduser');
	}
	);
*/
Route::get('/home', 'HomeController@index');

Route::get('/dashboard', [
    'uses' => 'Auth\LoginController@index',
    'as' => 'dashboard'
    ]);

Route::get('/dashboard1', [
    'uses' => 'Auth\LoginController@index1',
    'as' => 'dashboard1'
    ]);
	
Route::get('/dashboard2', [
    'uses' => 'HomeController@index2',
    'as' => 'dashboard2',
	'middleware' => ['auth', 'ashop'],
    ]);
		
Route::get('list', [
	'uses' => 'RolelistController@index',
	'as' => 'rolelist',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0'],
	]);
	
Route::post('roleAssign', [
	'uses' => 'RolelistController@postAssignRoles',
	'as' => 'admin.assign',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0'],
	]);
	
Route::get('/sales', [
	'uses' => 'SalesController@getTable',
	'as' => 'sales',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '5'],
	]);

// CAT ROUTES
Route::get('/CAT', [
    'uses' => 'CATController@getTable',
    'as' => 'CAT',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '3'],
    ]);

Route::get('/CAT/{id}', [
    'uses' => 'CATController@clientDetail',
    'as' => 'CAT.detail',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '3'],
    ]);

Route::post('/CAT/insert', [
    'uses' => 'CATController@addClient',
    'as' => 'CAT.insert',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '3'],
    ]);

Route::post('/CAT/import', [
    'uses' => 'CATController@importExcel',
    'as' => 'CAT.import',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '3'],
    ]);

Route::post('/CAT/edit', [
    'uses' => 'CATController@editClient',
    'as' => 'CAT.edit',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '3'],
    ]);

Route::post('/CAT/inserttrans', [
    'uses' => 'CATController@addTrans',
    'as' => 'CAT.inserttrans',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '3'],
    ]);

Route::get('/CAT/deleteclient/{id}', [
    'uses' => 'CATController@deleteClient',
    'as' => 'CAT.deleteclient',
    'middleware' => ['auth', 'roles'],
    'roles' => ['0', '3'],
    ]);

//route delete masih ngaco
Route::get('/CAT/trans/{id}', [
    'uses' => 'CATController@detailTrans',
    'as' => 'CAT.transdetail'
    ]);

Route::get('/CAT/deletetrans/{id1}/{id2}', [
    'uses' => 'CATController@deleteTrans',
    'as' => 'CAT/trans.deletetrans'
    ]);

// MRG ROUTES
Route::get('/MRG', [
    'uses' => 'MRGController@getTable',
    'as' => 'MRG',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '2'],
    ]);

Route::get('/MRG/{id}', [
    'uses' => 'MRGController@clientDetail',
    'as' => 'MRG.detail',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '2'],
    ]);

Route::post('/MRG/insert', [
    'uses' => 'MRGController@addClient',
    'as' => 'MRG.insert',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '2'],
    ]);

Route::post('/MRG/import', [
    'uses' => 'MRGController@importExcel',
    'as' => 'MRG.import',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '2'],
    ]);

Route::post('/MRG/edit', [
    'uses' => 'MRGController@editClient',
    'as' => 'MRG.edit',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '2'],
    ]);

Route::get('/MRGexport', [
    'uses' => 'MRGController@exportExcel',
    'as' => 'MRG.export',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '2'],
    ]);

Route::get('/MRG/deleteclient/{id}', [
    'uses' => 'MRGController@deleteClient',
    'as' => 'MRG.deleteclient',
    'middleware' => ['auth', 'roles'],
    'roles' => ['0', '2'],
    ]);

//UOB ROUTES
Route::get('/UOB', [
    'uses' => 'UOBController@getTable',
    'as' => 'UOB',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '4'],
    ]);

Route::get('/UOB/{id}', [
    'uses' => 'UOBController@clientDetail',
    'as' => 'UOB.detail',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '4'],
    ]);

Route::post('/UOB/insert', [
    'uses' => 'UOBController@addClient',
    'as' => 'UOB.insert',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '4'],
    ]);

Route::post('/UOB/import', [
    'uses' => 'UOBController@importExcel',
    'as' => 'UOB.import',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '4'],
    ]);	

Route::post('/UOB/edit', [
    'uses' => 'UOBController@editClient',
    'as' => 'UOB.edit',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '4'],
    ]);

Route::get('/UOB/deleteclient/{id}', [
    'uses' => 'UOBController@deleteClient',
    'as' => 'UOB.deleteclient',
    'middleware' => ['auth', 'roles'],
    'roles' => ['0', '4'],
    ]);

//A-CLUB ROUTES
Route::get('/AClub', [
    'uses' => 'AClubController@getTable',
    'as' => 'AClub',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1'],
    ]);

Route::get('/AClub/{id}', [
    'uses' => 'AClubController@clientDetail',
    'as' => 'AClub.detail',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1'],
    ]);

Route::post('/AClub/insert', [
    'uses' => 'AClubController@addClient',
    'as' => 'AClub.insert',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1'],
    ]);

Route::post('/AClub/import', [
    'uses' => 'AClubController@importExcel',
    'as' => 'AClub.import',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1'],
    ]);

Route::post('/AClub/edit', [
    'uses' => 'AClubController@editClient',
    'as' => 'AClub.edit',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1'],
    ]);

Route::post('/AClub/inserttrans', [
    'uses' => 'AClubController@addTrans',
    'as' => 'AClub.inserttrans',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1'],
    ]);

Route::get('/AClub/deletetrans/{id}', [
    'uses' => 'AClubController@deleteTrans',
    'as' => 'AClub/trans.deletetrans',
    'middleware' => ['auth', 'roles'],
    'roles' => ['0', '1'],
    ]);

Route::get('/AClub/deleteclient/{id}', [
    'uses' => 'AClubController@deleteClient',
    'as' => 'AClub.deleteclient',
    'middleware' => ['auth', 'roles'],
    'roles' => ['0', '1'],
    ]);

//GREEN ROUTES
Route::get('/green', [
    'uses' => 'GreenController@getTable',
    'as' => 'green',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::get('/green/{id}', [
    'uses' => 'GreenController@clientDetail',
    'as' => 'green.detail',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::post('/green/insert', [
    'uses' => 'GreenController@addClient',
    'as' => 'green.insert',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::post('/green/import', [
    'uses' => 'GreenController@importExcel',
    'as' => 'green.import',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::post('/green/edit', [
    'uses' => 'GreenController@editClient',
    'as' => 'green.edit',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::get('/green/deleteclient/{id}', [
    'uses' => 'GreenController@deleteClient',
    'as' => 'green.deleteclient',
    'middleware' => ['auth', 'roles'],
    'roles' => ['0', '1', '2', '3', '4'],
    ]);

//Red Club Route
Route::get('/RedClub', [
    'uses' => 'RedClubController@getTable',
    'as' => 'RedClub',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::get('/RedClub/{id}', [
    'uses' => 'RedClubController@clientDetail',
    'as' => 'RedClub.detail',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::post('/RedClub/insert', [
    'uses' => 'RedClubController@addClient',
    'as' => 'RedClub.insert',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::post('/RedClub/import', [
    'uses' => 'RedClubController@importExcel',
    'as' => 'RedClub.import',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::post('/RedClub/edit', [
    'uses' => 'RedClubController@editClient',
    'as' => 'RedClub.edit',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::get('/RedClub/deleteclient/{id}', [
    'uses' => 'RedClubController@deleteClient',
    'as' => 'RedClub.deleteclient',
    'middleware' => ['auth', 'roles'],
    'roles' => ['0', '1', '2', '3', '4'],
    ]);

//Grow Route
Route::get('/grow', [
    'uses' => 'GrowController@getTable',
    'as' => 'grow',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::get('/grow/{id}', [
    'uses' => 'GrowController@clientDetail',
    'as' => 'grow.detail',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::post('/grow/insert', [
    'uses' => 'GrowController@addClient',
    'as' => 'grow.insert',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::post('/grow/import', [
    'uses' => 'GrowController@importExcel',
    'as' => 'grow.import',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::post('/grow/edit', [
    'uses' => 'GrowController@editClient',
    'as' => 'grow.edit',
	'middleware' => ['auth', 'roles'],
	'roles' => ['0', '1', '2', '3', '4'],
    ]);

Route::get('/grow/deleteclient/{id}', [
    'uses' => 'GrowController@deleteClient',
    'as' => 'grow.deleteclient',
    'middleware' => ['auth', 'roles'],
    'roles' => ['0', '1', '2', '3', '4'],
    ]);