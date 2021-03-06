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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('auth/login', "AuthController@store");
Route::post('/staff/auth/login', "StaffAuthenticationController@store");

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('account/me', "AuthController@show");
    Route::delete('auth/logout', "AuthController@destroy");
});


Route::group(['middleware' => 'auth:staff-api'], function () {
    Route::get('staff/me', "StaffAuthenticationController@show");
    Route::delete('staff/auth/logout', "StaffAuthenticationController@destroy");
});
