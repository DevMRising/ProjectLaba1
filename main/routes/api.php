<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // These endpoints use the project's custom token scheme (bearer tokens).
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/out', [AuthController::class, 'out']);
    Route::get('/tokens', [AuthController::class, 'tokens']);
    Route::post('/out_all', [AuthController::class, 'outAll']);

    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
});
