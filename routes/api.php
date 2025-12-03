<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\CryptocurrencyController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Cryptocurrency routes (public)
    Route::get('/cryptocurrencies', [CryptocurrencyController::class, 'index']);
    Route::get('/cryptocurrencies/{cryptocurrency}', [CryptocurrencyController::class, 'show']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Purchase routes
        Route::post('/purchases', [PurchaseController::class, 'buy']);
        Route::get('/purchases', [PurchaseController::class, 'history']);
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show']);
    });
});
