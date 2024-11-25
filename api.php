<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\dashboard\Admin\UserController;


Route::get('/test', function () {
    return " Un-Protected Route ((Test page))";
})->withoutMiddleware('auth:sanctum');


Route::prefix('auth')->group(function () {

  
    Route::post('/register', [AuthController::class, 'register']);

   
    Route::post('/login', [AuthController::class, 'login'])->name('login');

});



Route::middleware('auth:sanctum')->group(function () {


    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    Route::middleware('role:admin')->group(function () {

        Route::get('/admin', function () {
            return "Hello Admin";
        });



    });

    Route::middleware('role:vendor')->group(function () {

        Route::get('/vendor', function () {
            return "Hello Vendor";
        });



    });

    Route::middleware('role:customer')->group(function () {

        Route::get('/customer', function () {
            return "Hello Customer";
        });



    });
});
