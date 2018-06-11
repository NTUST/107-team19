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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/home', 'HomeController@index')->name('home');
//Route::get('/manager', 'MController@index')->name('manager_index');
Route::group(['prefix'=>'manager'], function(){

    Route::group(['middleware'=>'auth'], function() {

        Route::get('/', 'ManagerController@index')->name('manager_index');
        Route::group(['prefix'=>'check'], function(){
//
            Route::get('{id}/', 'ManagerController@check')->name('manager_check');
//
        });
        Route::get('info/{id}', 'ManagerController@info')->name('manager_info');
        Route::get('edit/{id}', 'ManagerController@edit')->name('manager_edit');
        Route::post('update', 'ManagerController@update')->name('manager_update');
        Route::post('checkAPI/', 'ManagerController@checkAPI')->name('manager_check_API');

        Route::get('create', 'ManagerController@create_show')->name('manager_create_view');
        Route::post('create', 'ManagerController@create')->name('manager_create');
        Route::get('delete/{id}', 'ManagerController@delete')->name('manager_delete');
        Route::get('namelist/{id}', 'MController@namelist_show')->name('namelist_show');
    });
//    Route::get('test', 'ManagerController@test');
    Auth::routes();
});