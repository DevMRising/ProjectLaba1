<?php

use Illuminate\Support\Facades\Route;
use app\DTO;
use app\DTO\ClientInfoDTO;
use App\Http\Controllers\InfoController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/info/server', );
Route::get('/info/client',[InfoController::class,'client']);
Route::get('/info/database', );