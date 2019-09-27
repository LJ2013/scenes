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
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', 'GeoController@index')->name('index');
Route::get('/album', 'GeoController@album');
Route::get('/test', 'GeoController@test');
Route::match(['GET', 'POST'], 'upload', 'GeoController@upload')->name('upload');
Route::get('/list', 'GeoController@list')->name('list');
Route::match(['GET', 'POST'], '/photo/{id}/edit', 'GeoController@edit')->name('edit');


Route::get('testjob', 'GeoController@testjob');