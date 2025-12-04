<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\CryptocurrencyController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\TransactionController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Cryptocurrency routes (public)
    Route::get('/cryptocurrencies', [CryptocurrencyController::class, 'index']);
    Route::get('/cryptocurrencies/{cryptocurrency}', [CryptocurrencyController::class, 'show']);
    Route::post('/cryptocurrencies', [CryptocurrencyController::class, 'store']);
    Route::delete('/cryptocurrencies/{cryptocurrency}', [CryptocurrencyController::class, 'destroy']);

    // Public transaction routes
    Route::get('/transactions/latest', [PublicController::class, 'latestTransactions']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::get('/auth/validate', [AuthController::class, 'validateToken']);

        // Purchase routes
        Route::post('/purchases', [PurchaseController::class, 'buy']);
        Route::get('/purchases', [PurchaseController::class, 'history']);
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show']);

        // Wallet balance and sell routes
        Route::get('/wallet/balance', [PurchaseController::class, 'balance']);
        Route::get('/wallet/transactions', [PurchaseController::class, 'transactions']);
        Route::post('/wallet/send', [PurchaseController::class, 'send']);
        Route::post('/wallet/sell', [PurchaseController::class, 'sell']);

        // Admin routes
        Route::middleware('admin')->group(function () {
            // User management
            Route::get('/admin/users', [AdminController::class, 'allUsers']);
            Route::post('/admin/users/block', [AdminController::class, 'blockUser']);
            Route::post('/admin/users/unblock', [AdminController::class, 'unblockUser']);
            Route::get('/admin/users/blocked', [AdminController::class, 'blockedUsers']);

            // Wallet management
            Route::get('/admin/wallets', [AdminController::class, 'allWallets']);
            Route::post('/admin/wallets/freeze', [AdminController::class, 'freezeWallet']);
            Route::post('/admin/wallets/unfreeze', [AdminController::class, 'unfreezeWallet']);
        });
    });
});
