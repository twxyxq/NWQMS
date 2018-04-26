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

Route::get('/', function () {
    return view('index');
});

//Route::get('test', 'test');

//Route::get('wj', 'wj@show');
Route::resource('setting','setting');

Route::resource('pp','pp');

Route::resource('panel','panel');

Route::resource('console','console');

Route::resource('alternation','alternation');

Route::resource('statistic','statistic');

Route::resource('lock_ai','lock_ai');

Route::resource('lock_remote','lock_remote');

Route::resource('gps','gps');

Route::resource('radiation_gps','radiation_gps');

Route::resource('wechat','wechat');

Route::resource('interior_management','interior_management');

Route::resource('start_up','start_up');



//Route::get('/panel/{panel}', function ($panel) {
	//return view("panel/".$panel);
//})->middleware('auth');

//Route::get('wj/wj_list', 'wj@wj_list');

Route::get('test', 'test@index');

/*
Route::get('console/datatables', 'console@datatables');

Route::post('console/model_ajax', 'console@model_ajax');

Route::post('console/get_model_data', 'console@get_model_data');

Route::post('console/get_bind', 'console@get_bind');

Route::post('console/is_bind', 'console@is_bind');

Route::post('console/version', 'console@version');

Route::get('console/dt_edit', 'console@dt_edit');
*/

Auth::routes();

Route::get('/home', 'panel@index');
