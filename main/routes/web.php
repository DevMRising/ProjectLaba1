<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfoController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/info/server',  [InfoController::class,'server'  ]);
Route::get('/info/client',  [InfoController::class,'client'  ]);
Route::get('/info/database',[InfoController::class,'database']);