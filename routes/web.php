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

Route::get('/', 'IndexController@index');
Route::get('/image', 'IndexController@image');
Route::get('/yiju', 'IndexController@yiju');


Route::group(['prefix' => 'overnight'], function () {
    Route::get('/', 'OvernightController@index')->name('overnight.index');
    Route::post('/', 'OvernightController@store')->name('overnight.store');
    Route::get('/show', 'OvernightController@show')->name('overnight.show');
    Route::post('/update', 'OvernightController@update')->name('overnight.update');
    Route::any('/export', 'OvernightController@export')->name('overnight.export');
    Route::any('/import', 'OvernightController@import')->name('overnight.import');
});
