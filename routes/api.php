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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'namespace' => 'App\Http\Controllers',
    'prefix'    => 'auth'
], function(){
    //Verify session
    Route::get('verify', 'AuthController@verify');

    //login routes
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
});

Route::group([
    'namespace' => 'App\Http\Controllers',
    'prefix'    => 'files'
], function(){
    Route::get('', 'FileController@index');
    Route::post('edit', 'FileController@edit');
    Route::get('paginate/{returnTotal}', 'FileController@paginate');
    Route::post('store', 'FileController@store');
    Route::get('{id}', 'FileController@single');
    Route::post('search/{returnTotal}', 'FileController@search');
});

Route::group([
    'namespace' =>  'App\Http\Controllers',
    'prefix'    =>  'events'
], function(){
    Route::get('', 'ArgumentController@index');
    Route::post('store', 'ArgumentController@store');
    Route::get('{id}', 'ArgumentController@single');
    Route::post('edit', 'ArgumentController@edit');
    Route::get('paginate/{returnTotal}', 'ArgumentController@paginate');
    Route::post('search/{returnTotal}', 'ArgumentController@search');
});

Route::group([
    'namespace' => 'App\Http\Controllers',
    'prefix'    => 'users'
], function(){
    Route::get('', 'UserController@index');
    Route::post('edit', 'UserController@editUser');
    Route::post('create', 'UserController@createUser');
    Route::get('paginate/{status}/{returnTotal}', 'UserController@paginate');
    Route::post('search/{returnTotal}', 'UserController@search');
    Route::post('update-password', 'UserController@updatePassword');
});

/** Authenication Error Response */
Route::any('auth_err_res', function () {
    return response()->json([
        'status' => 'fail',
        'data' => [
            'login' => ['Authentication required!']
        ]
    ], 401);
})->name('auth_err_res');

// Admin access required
Route::any('admin_auth_err_res', function () {
    return response()->json([
        'status' => 'fail',
        'data' => [
            'login' => ['Admin access required!']
        ]
    ], 401);
})->name('admin_auth_err_res');