<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\UserController;

// Rutas públicas (registro y login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas públicas para productos (listar, mostrar, buscar)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/search/{query}', [ProductController::class, 'search']);

// Envío de enlace
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Restablecimiento de contraseña
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

// Rutas protegidas con autenticación Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Cierre de sesión
    Route::post('/logout', [AuthController::class, 'logout']);

    // Obtener datos del usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rutas para usuarios con rol 'user' (apartados de productos)
    Route::middleware('role:user')->group(function () {
        Route::post('/reservations', [ReservationController::class, 'store']);
    });

    // Rutas para usuarios con rol 'admin'
    Route::middleware('role:admin')->group(function () {
        // Gestión de reservas
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::get('/reservations/{id}', [ReservationController::class, 'show']);
        Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

        // Gestión de usuarios
        Route::get('/users', [UserController::class, 'index']);
        Route::put('/users/{id}', [UserController::class, 'update']); // <-- AGREGA ESTA LÍNEA
        Route::put('/users/{id}/role', [UserController::class, 'updateRole']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Gestión de productos
        
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Inventario
        Route::get('/inventory', [InventoryController::class, 'index']);
    });
});
