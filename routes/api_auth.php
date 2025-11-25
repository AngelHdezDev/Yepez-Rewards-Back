<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response; // Asegúrate de tener Response para el helper JSON
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\TransactionController; // <-- ¡NUEVO!

// 1. RUTAS PÚBLICAS (NO NECESITAN CSRF, NO NECESITAN TOKEN)

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');


Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

// 1.2. Petición para aplicar la nueva contraseña usando el email, contraseña y token
Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');


// 2. RUTAS PROTEGIDAS (REQUIEREN TOKEN SANCTUM)
Route::middleware('auth:sanctum')->group(function () {

    // Obtener información del usuario autenticado (incluye el Accessor 'balance')
    Route::get('/user', function (Request $request) {
        // Devolvemos el usuario, cargando explícitamente la relación 'roles' de Spatie
        $user = $request->user()->load('roles');

        // El campo 'balance' se adjunta aquí automáticamente por el Accessor del modelo User
        return Response::json([
            'user' => $user,
        ], 200);

    });

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
        
    // --- RUTAS DE CLIENTE ---
    
    // [CLIENT] - Ruta para ver el historial de transacciones
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->middleware('role:client'); 
        
    // --- NUEVA RUTA PARA EL PASO 7: GASTO DE PUNTOS (DEBIT) ---
    
    // [CLIENT] - Ruta para canjear (gastar) puntos
    Route::post('/redeem', [TransactionController::class, 'redeem'])
        ->middleware('role:client');
});