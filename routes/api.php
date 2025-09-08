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
    'middleware' => 'api',
    'namespace'  => 'App\Http\Controllers',
    'prefix'     => 'v1'

], function ($router) { 

    /****************************************************************************************
    *  User Create Package.
    *
    *  Required param - NO
    *
    *  Optional param :
    *          = sort         - The algorithm by which the list should be sorted (ASC or DESC)
    *
    *  Usage  - commonly used for customer logout.
    /****************************************************************************************/
    Route::post('/create-package'     , 'ApiController@createPackge');



    /****************************************************************************************
    *  User Create Package.
    *
    *  Required param - waybill
    *                 - api_key
    *
    *  Optional param :
    *          = sort         - The algorithm by which the list should be sorted (ASC or DESC)
    *
    *  Usage  - commonly used for customer logout.
    /****************************************************************************************/
    Route::get('/get-package'     , 'ApiController@getPackge');



    /****************************************************************************************
    *  User Create Package Return Recieved.
    *
    *  Required param - waybill
    *                 - api_key
    *
    *  Optional param :
    *          = sort         - The algorithm by which the list should be sorted (ASC or DESC)
    *
    *  Usage  - commonly used for customer logout.
    /****************************************************************************************/
    Route::post('/get-package-return-recieved'     , 'ApiController@returnRecieved');

    
});

