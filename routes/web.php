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

Route::get('/', 'Auth\LoginController@index')->middleware('auth');

Auth::routes();

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
    'uses' => 'Auth\LoginController@index2',
    'as' => 'dashboard2'
    ]);

// ACLUB ROUTES
Route::get('/AClub', [
    'uses' => 'AClubController@getTable',
    'as' => 'AClub'
    ]);

// CAT ROUTES
Route::get('/CAT', [
    'uses' => 'CATController@getTable',
    'as' => 'CAT'
    ]);

Route::get('/CAT/add', [
    'uses' => 'CATController@getForm',
    'as' => 'CAT.add'
    ]);

Route::get('/CAT/{id}', [
    'uses' => 'CATController@clientDetail',
    'as' => 'CAT.detail'
    ]);

Route::post('/CAT/insert', [
    'uses' => 'CATController@addClient',
    'as' => 'CAT.insert'
    ]);

Route::post('/CAT/import', [
    'uses' => 'CATController@importExcel',
    'as' => 'CAT.import'
    ]);

// MRG ROUTES
Route::get('/MRG', [
    'uses' => 'MRGController@getTable',
    'as' => 'MRG'
    ]);

	Route::get('/MRG/add', [
    'uses' => 'MRGController@getForm',
    'as' => 'MRG.add'
    ]);

Route::get('/MRG/{id}', [
    'uses' => 'MRGController@clientDetail',
    'as' => 'MRG.detail'
    ]);

Route::post('/MRG/insert', [
    'uses' => 'MRGController@addClient',
    'as' => 'MRG.insert'
    ]);

Route::post('/MRG/import', [
    'uses' => 'MRGController@importExcel',
    'as' => 'MRG.import'
    ]);

//UOB ROUTES
Route::get('/UOB', [
    'uses' => 'UOBController@getTable',
    'as' => 'UOB'
    ]);

Route::get('/UOB/add', [
    'uses' => 'UOBController@getForm',
    'as' => 'UOB.add'
    ]);

Route::get('/UOB/{id}', [
    'uses' => 'UOBController@clientDetail',
    'as' => 'UOB.detail'
    ]);

Route::post('/UOB/insert', [
    'uses' => 'UOBController@addClient',
    'as' => 'UOB.insert'
    ]);

Route::post('/UOB/import', [
    'uses' => 'UOBController@importExcel',
    'as' => 'UOB.import'
    ]);