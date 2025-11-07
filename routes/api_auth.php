<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetLinkController;

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

    // Obtener información del usuario autenticado (prueba de token)
    Route::get(uri: '/user', action: function (Request $request) {
        // Devolvemos el usuario, cargando explícitamente la relación 'roles' de Spatie
        $user = $request->user()->load('roles');

        return Response::json([
            'user' => $user,
        ], 200);

    });

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
