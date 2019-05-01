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

Route::group(['prefix' => 'study'],function (){
    Route::any('ranking',['as'=>'ranking','uses'=>'StudyController@ranking']);
    Route::any('completeTomato',['as'=>'completeTomato','uses'=>'StudyController@completeTomato']);
    Route::any('getTomato',['as'=>'getTomato','uses'=>'StudyController@getTomato']);
    Route::post('completeStudy',['as'=>'completeStudy','uses'=>'StudyController@completeStudy']);
});

Route::group(['prefix' => 'tool'],function (){
    Route::any('getOpenId',['as'=>'getOpenId','uses'=>'ToolController@getOpenId']);
    Route::post('getText',['as'=>'getText','uses'=>'ToolController@getText']);
});

Route::group(['prefix' => 'task'],function (){
   Route::post('addTask',['as'=>'addTask','uses'=>'TaskController@addTask']);
   Route::post('updateTask',['as'=>'updateTask','uses'=>'TaskController@updateTask']);
   Route::post('deleteTask',['as'=>'deleteTask','uses'=>'TaskController@deleteTask']);
   Route::post('getTask',['as'=>'getTasks','uses'=>'TaskController@getTasks']);
});

Route::group(['prefix' => 'store'],function (){
    Route::post('addGoods',['as'=>'addGoods','uses'=>'StoreController@addGoods']);
    Route::post('updateGoods',['as'=>'updateGoods','uses'=>'StoreController@updateGoods']);
    Route::post('deleteGoods',['as'=>'deleteGoods','uses'=>'StoreController@deleteGoods']);
    // Route::post('getTask',['as'=>'getGoods','uses'=>'StoreController@getTasks']);
 });

 Route::group(['prefix' => 'page'],function (){
    Route::post('index',['as'=>'index','uses'=>'PageController@index']);
 });

 Route::post('test',['as'=>'test','uses'=>'TestController@test']);