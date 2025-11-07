<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TransactionController;

/*
|--------------------------------------------------------------------------
| Rutas de la API de Administración (Admin Routes)
|--------------------------------------------------------------------------
|
| Estas rutas son accesibles SOLO por usuarios autenticados con el rol 'admin'.
| Requieren un Token Sanctum.
|
*/

// Middleware: 
// 1. 'auth:sanctum': Requiere que el usuario esté autenticado con un token de Sanctum.
// 2. 'role:admin': Requiere que el usuario autenticado tenga el rol 'admin'.
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // POST /api/transactions
    // Ruta para crear nuevas transacciones (asignación de puntos)
    Route::post('/transactions', [TransactionController::class, 'store'])
        ->name('admin.transactions.store');

    // Aquí irían otras rutas de administración
}); 