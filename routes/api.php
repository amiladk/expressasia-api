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
    *  API Client Signup - Register new clients via API
    *
    *  Required Header: 
    *          - X-Master-API-Key: Master API key for authorization
    *
    *  Required params:
    *          - name: Business name (unique)
    *          - username: Username for login (unique)
    *          - password: Account password (min 6 characters)
    *          - pickup_address: Pickup address
    *          - pickup_city: City name (will be converted to city ID)
    *          - email: Admin email (unique)
    *          - email_two: Delivery notification email
    *          - phone_one: Admin phone number
    *          - phone_two: Delivery phone number
    *          - payment_cycle: Payment cycle ID (1=7Days, 2=30Days, 3=Daily, 4=14Days)
    *          - account_name: Bank account holder name
    *          - account_number: Bank account number
    *          - bank_id: Bank ID from bank table
    *          - bank_branch: Bank branch name
    *          - branch_code: Bank branch code
    *
    *  Rate Limiting: Maximum 10 signups per IP address per day
    *
    *  Response: Returns client_id, username, and generated api_key
    *
    *  Usage: Register new clients programmatically for multi-account scenarios
    /****************************************************************************************/
    Route::post('/client-signup', 'ApiController@apiClientSignup');


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
    *  Public Package Tracking - No API key required.
    *
    *  Required param - waybill
    *                 - phone (10 digits starting with 0)
    *
    *  Usage  - Used for public package tracking from expressasia.lk website
    /****************************************************************************************/
    Route::get('/track-package', 'ApiController@getPackagePublic');



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


    Route::get('/get-package-list'     , 'ApiController@getPackageList');

    /****************************************************************************************
    *  Get Client Settlement Batches
    *
    *  Required param - api_key
    *
    *  Optional param :
    *          - from_date    : Filter by start date (YYYY-MM-DD)
    *          - to_date      : Filter by end date (YYYY-MM-DD)
    *          - page         : Page number for pagination
    *          - per_page     : Items per page (max 100, default 20)
    *
    *  Usage - Retrieve paginated list of settlement batches for the client
    /****************************************************************************************/
    Route::get('/get-client-invoices', 'ApiController@getClientSettlementBatches');


    /****************************************************************************************
    *  Get Client Settlement Batch Items
    *
    *  Required param - api_key
    *                 - search_code
    *
    *  Usage - Retrieve detailed items of a specific settlement batch
    /****************************************************************************************/
    Route::get('/get-client-invoice-items', 'ApiController@getClientSettlementBatchItems');

    /****************************************************************************************
    *  Create Pickup Request
    *
    *  Required param - api_key
    *                 - remarks
    *
    *  Usage - Create a pickup request for the client. Only one pickup request allowed per day.
    /****************************************************************************************/
    Route::post('/create-pickup-request', 'ApiController@createPickupRequest');

    
});