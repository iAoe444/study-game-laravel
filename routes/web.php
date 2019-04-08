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
   return view('welcome');
});
Route::group(['prefix' => 'user'],function (){
    Route::any('userOrCreate',['as'=>'userOrCreate','uses'=>'UserController@userOrCreate']);
    Route::any('updateUser',['as'=>'updateUser','uses'=>'UserController@updateUser']);
});
Route::group(['prefix' => 'studyTime'],function (){
    Route::any('ranking',['as'=>'ranking','uses'=>'StudyTimeController@ranking']);
});
Route::group(['prefix' => 'weChat'],function (){
    Route::any('getOpenId',['as'=>'getOpenId','uses'=>'WeChatController@getOpenId']);
});