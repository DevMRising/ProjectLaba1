<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/client', [AuthController::class, 'client']);
Route::get('/database', [AuthController::class, 'database']);
Route::get('/server', [AuthController::class, 'server']);