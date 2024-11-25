<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\dashboard\Admin\UserController;
use App\Http\Controllers\dashboard\Admin\AnalyticsController;

// public routes ---
Route::get('/test', function () {
    return " Un-Protected Route ((Test page))";
})->withoutMiddleware('auth:sanctum');

// auth routes
Route::prefix('auth')->group(function () {
    // Endpoint: /api/auth/register
    Route::post('/register', [AuthController::class, 'register']);
    // Endpoint: /api/auth/login
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Endpoint: /api/logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin User Management Routes
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    // Admin Analytics Routes
    Route::middleware('role:admin')->prefix('analytics')->group(function () {
        Route::get('/sales', [AnalyticsController::class, 'getSalesAnalytics']);
        Route::get('/orders', [AnalyticsController::class, 'getOrderAnalytics']);
        Route::get('/products', [AnalyticsController::class, 'getProductAnalytics']);
        Route::get('/reviews', [AnalyticsController::class, 'getReviewAnalytics']);
        Route::get('/dashboard', [AnalyticsController::class, 'getDashboardOverview']);
    });

    //admin Routes
    Route::middleware('role:admin')->group(function () {
        //Endpoint: /api/admin
        Route::get('/admin', function () {
            return "Hello Admin";
        });
    });

    //vendor Routes
    Route::middleware('role:vendor')->group(function () {
        //Endpoint: /api/vendor
        Route::get('/vendor', function () {
            return "Hello Vendor";
        });
    });

    Route::middleware('role:customer')->group(function () {
        //Endpoint: /api/customer
        Route::get('/customer', function () {
            return "Hello Customer";
        });
    });
});
