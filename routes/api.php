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

/**
 * V1 Routes
 */
Route::group(['namespace' => 'V1'], function () {

    /**
     * Auth Routes
     */
    Route::group(['namespace' => 'Auth'], function () {
        Route::post('register', 'RegisterController@index');
        Route::post('login', 'LoginController@index');
        Route::post('logout', 'LoginController@logout')->middleware('auth:api');

        Route::group(['prefix' => 'verify'], function () {
            Route::post('{email}', 'VerificationController@index');
            Route::put('resend/{email}', 'VerificationController@resend');
        });

        Route::group(['prefix' => 'password'], function () {
            Route::post('forgot', 'PasswordResetController@create');
            Route::put('reset/{user}', 'PasswordResetController@reset');
        });
    });


    /**
     * User Routes
     */
    Route::group(['prefix' => 'user', 'middleware' => ['auth:api', 'status']], function (){
        Route::get('', 'UserController@profile');
        Route::put('', 'UserController@update');
        Route::post('/', 'UserController@create');
        Route::get('/{id}', 'UserController@get');
        Route::delete('/{id}', 'UserController@delete');
        Route::patch('', 'UserController@updatePassword');
    });

    /**
     * Users Route
     */
    Route::group(['prefix' => 'users', 'middleware' => ['auth:api', 'status']], function (){
        Route::get('', 'UserController@all');
        Route::get('/staff', 'UserController@staff');
        Route::get('/customers', 'UserController@customers');
        Route::get('/riders', 'UserController@riders');
    });

    /**
     * Transaction Routes
     */
    Route::group(['prefix' => 'report', 'middleware' => ['auth:api', 'status']], function (){
        Route::get('', 'TransactionRequestController@adminDashboard');
        Route::get('customer', 'TransactionRequestController@customerDashboard');
    });

});


Route::fallback(function (){
    return response()->json([
        'message' => 'Resource not found. If error persists, contact Administrator.'
    ], 404);
});