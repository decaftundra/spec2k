<?php

use App\Notification;
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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

// example.com/api/v1/notification/create
Route::prefix('v1')->group(function () {
    
    // Test oauth2 authentication.
    Route::get('/oauth2-test', function(Request $request) {
        return response()->json(['success' => true], 201);
    })->middleware(['client']);
    
    Route::post('notification/create', 'ApiController@create')
        ->name('api.create-notification')
        ->middleware(['client']);
    
});