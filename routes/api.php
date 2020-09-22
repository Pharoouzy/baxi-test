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
     * DSTV Subscription Routes
     */
    Route::group(['prefix' => 'multichoice', 'middleware' => ['auth:api', 'status']], function (){
        Route::get('providers', 'BaxiController@getProviderBouquets');
        Route::get('addons', 'BaxiController@getBouquetAddons');
        Route::post('subscribe', 'BaxiController@subscribe');
    });

    /**
     * Electricity Bill Routes
     */
    Route::group(['prefix' => 'electricity', 'middleware' => ['auth:api', 'status']], function (){
        Route::get('billers', 'BaxiController@getElectricityBillers');
        Route::post('verify', 'BaxiController@verify');
        Route::post('recharge/{reference}', 'BaxiController@rechargeElectricityBill');
    });

    /**
     * Transactions Routes
     */
    Route::group(['middleware' => ['auth:api', 'status']], function (){
        Route::get('transactions', 'TransactionController@index');
        Route::get('transaction/{reference}', 'TransactionController@get');
        Route::post('transaction/requery/{reference}', 'BaxiController@requeryTransaction');
    });

});


Route::fallback(function (){
    return response()->json([
        'message' => 'Resource not found. If error persists, contact Administrator.'
    ], 404);
});