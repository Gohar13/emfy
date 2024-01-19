<?php

use App\Http\Controllers\Webhook\Amo\AmoController;
use App\Http\Controllers\Webhook\Amo\ContactController;
use App\Http\Controllers\Webhook\Amo\DealController;
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

Route::group(['prefix' => 'webhooks'], function () {
    Route::group(['prefix' => 'amo'], function () {
        Route::get('get-token', [AmoController::class, 'getToken']);
        Route::group(['prefix' => 'contact'], function () {
            Route::post('added', [ContactController::class, 'added']);
            Route::post('updated', [ContactController::class, 'updated']);
        });
        Route::group(['prefix' => 'deal'], function () {
            Route::post('added', [DealController::class, 'added']);
            Route::post('updated', [DealController::class, 'updated']);
        });
    });
});
