<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public auth routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('signup', [AuthController::class, 'signup']);
    });

    // Public invitation actions (invitee has no account yet)
    Route::prefix('invitations')->group(function () {
        Route::put('{id}/accept', [InvitationController::class, 'accept']);
        Route::put('{id}/deny', [InvitationController::class, 'deny']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
        })->middleware('authorize:admin');

        // Companies
        Route::get('companies', [CompanyController::class, 'index']);
        Route::post('companies', [CompanyController::class, 'store'])->middleware('authorize:owner');
        Route::patch('companies/{id}', [CompanyController::class, 'update'])->middleware('authorize:owner');
        Route::delete('companies/{id}', [CompanyController::class, 'destroy'])->middleware('authorize:super_admin');

        // Stores (read)
        Route::apiResource('stores', StoreController::class)
            ->only(['index', 'show'])
            ->middleware('authorize:owner,cashier');

        // Stores (write)
        Route::apiResource('stores', StoreController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('authorize:owner');

        // Invitations (owner actions)
        Route::post('invitations', [InvitationController::class, 'invite'])->middleware('authorize:owner');
        Route::delete('invitations/{id}', [InvitationController::class, 'cancel'])->middleware('authorize:owner');
    });
});
