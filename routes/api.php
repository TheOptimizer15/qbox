<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('signup', [AuthController::class, 'signup']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
        })->middleware('authorize:admin');

        Route::get('company', [CompanyController::class, 'index']);
        Route::post('company', [CompanyController::class, 'create'])->middleware('authorize:owner');
        Route::patch('company/{id}', [CompanyController::class, 'update'])->middleware('authorize:owner');
        Route::delete('company/{id}', [CompanyController::class, 'delete'])->middleware('authorize:super_admin');
        
        Route::apiResource('stores', StoreController::class);

    });
});
