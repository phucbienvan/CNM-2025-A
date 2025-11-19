<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify-code', [AuthController::class, 'verifyCode']);
Route::post('resend-code', [AuthController::class, 'resendCode']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('users', [AuthController::class, 'getProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::group(['prefix' => 'products'], function () {
    Route::post('', [ProductController::class, 'store']);
    Route::get('', [ProductController::class, 'index']);
    Route::put('{product}', [ProductController::class, 'update']);
    Route::get('{product}', [ProductController::class, 'show']);
    Route::delete('{product}', [ProductController::class, 'destroy']);
});
