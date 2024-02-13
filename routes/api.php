<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'namespace' => 'App\Http\Controllers',
    'prefix'    => 'file'
], function(){
    Route::get('', 'FileController@index');
    Route::post('store', 'FileController@store');
    Route::get('{id}', 'FileController@single');
});

Route::group([
    'namespace' =>  'App\Http\Controllers',
    'prefix'    =>  'arguments'
], function(){
    Route::get('', 'ArgumentController@index');
    Route::post('store', 'ArgumentController@store');
    Route::get('{id}', 'ArgumentController@single');
});