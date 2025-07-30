<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/search/{query}', [ProductController::class, 'search']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

// ✅ Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ✅ Solo usuarios normales
    Route::middleware('role:user')->group(function () {
        Route::post('/reservations', [ReservationController::class, 'store']);
        Route::get('/user/reservations', [ReservationController::class, 'userReservations']);
    });

    // ✅ Solo admin
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('/reservations', ReservationController::class)->except(['create', 'edit']);
        Route::put('/reservations/{id}/status', [ReservationController::class, 'updateStatus']);

        Route::apiResource('/users', UserController::class)->except(['create', 'edit']);
        Route::put('/users/{id}/role', [UserController::class, 'updateRole']);

        Route::apiResource('/products', ProductController::class)->except(['index', 'show', 'create', 'edit']);
        Route::get('/inventory', [InventoryController::class, 'index']);
    });
});
