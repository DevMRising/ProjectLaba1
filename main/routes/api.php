<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Ref\UserController as RefUserController;
use App\Http\Controllers\Ref\UserRoleController;
use App\Http\Controllers\Policy\RoleController;
use App\Http\Controllers\Policy\PermissionController;
use App\Http\Controllers\Policy\RolePermissionController;
use App\Models\Role;
use App\Models\Permission;

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

Route::bind('role', function ($value) {
    return Role::withTrashed()->findOrFail($value);
});

Route::bind('permission', function ($value) {
    return Permission::withTrashed()->findOrFail($value);
});

Route::prefix('ref')->group(function () {
    Route::get('/user', [RefUserController::class, 'index']);
    Route::get('/user/{user}/role', [RefUserController::class, 'roles']);
    Route::post('/user/{user}/role', [UserRoleController::class, 'attach']);
    Route::delete('/user/{user}/role/{role}', [UserRoleController::class, 'detach']);
    Route::delete('/user/{user}/role/{role}/soft', [UserRoleController::class, 'softDelete']);
    Route::post('/user/{user}/role/{role}/restore', [UserRoleController::class, 'restore']);

    Route::prefix('policy')->group(function () {
        Route::prefix('role')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::post('/', [RoleController::class, 'store']);
            Route::get('/{role}', [RoleController::class, 'show']);
            Route::match(['put', 'patch'], '/{role}', [RoleController::class, 'update']);
            Route::delete('/{role}', [RoleController::class, 'destroy']);
            Route::delete('/{role}/soft', [RoleController::class, 'softDelete']);
            Route::post('/{role}/restore', [RoleController::class, 'restore']);
            Route::post('/{role}/permission', [RolePermissionController::class, 'attach']);
            Route::delete('/{role}/permission/{permission}', [RolePermissionController::class, 'detach']);
            Route::delete('/{role}/permission/{permission}/soft', [RolePermissionController::class, 'softDelete']);
            Route::post('/{role}/permission/{permission}/restore', [RolePermissionController::class, 'restore']);
        });

        Route::prefix('permission')->group(function () {
            Route::get('/', [PermissionController::class, 'index']);
            Route::post('/', [PermissionController::class, 'store']);
            Route::get('/{permission}', [PermissionController::class, 'show']);
            Route::match(['put', 'patch'], '/{permission}', [PermissionController::class, 'update']);
            Route::delete('/{permission}', [PermissionController::class, 'destroy']);
            Route::delete('/{permission}/soft', [PermissionController::class, 'softDelete']);
            Route::post('/{permission}/restore', [PermissionController::class, 'restore']);
        });
    });
});
