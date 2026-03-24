<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfoController;

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('api/auth')->group(function() {
    Route::get('/login',[AuthController::class,'login']);
    Route::get('/register',[AuthController::class,'register']);
    Route::get('/me',[AuthController::class,'me']);
    Route::get('/out',[AuthController::class,'out']);
    Route::get('/tokens',[AuthController::class,'tokens']);
    Route::get('/outAll',[AuthController::class,'outAll']);
    Route::get('/refresh',[AuthController::class,'refresh']);
});