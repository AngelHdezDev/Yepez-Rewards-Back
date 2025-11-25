<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Rutas de Clientes (Role: cliente)
|--------------------------------------------------------------------------
|
| Estas rutas están destinadas a los Clientes finales para ver su historial.
|
*/

Route::middleware(['auth:sanctum', 'role:cliente'])->group(function () {
    
    // GET /api/cliente/profile
    // El cliente puede ver su información y saldo de puntos.
    Route::get('/profile', function (Request $request) {
        // En una implementación real, calcularíamos el saldo de puntos aquí.
        return response()->json([
            'message' => 'Cliente: Acceso a Perfil.',
            'user' => $request->user(),
            'current_points_placeholder' => 1250, // Ejemplo
        ]);
    });

    // GET /api/cliente/transactions
    // Historial de transacciones de puntos
    Route::get('/transactions', function (Request $request) {
        return response()->json([
            'message' => 'Cliente: Historial de Transacciones de Puntos.',
            'user_id' => $request->user()->id,
        ]);
    });
});