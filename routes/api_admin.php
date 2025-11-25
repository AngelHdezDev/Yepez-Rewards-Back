<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Rutas de Administración (Role: yepez)
|--------------------------------------------------------------------------
|
| Estas rutas están destinadas a Yepez Central y son las de más alto nivel.
|
*/

// Agrupamos por autenticación y el rol 'yepez'
Route::middleware(['auth:sanctum', 'role:yepez'])->group(function () {

    // --- Rutas que requieren el permiso 'manage rewards' ---
    Route::middleware('permission:manage rewards')->prefix('rewards')->group(function () {
        
        // GET /api/yepez/rewards
        Route::get('/', function (Request $request) {
            return response()->json([
                'message' => 'Yepez: Listar y gestionar recompensas.',
                'user_id' => $request->user()->id,
            ]);
        })->name('yepez.rewards.index');

        // POST /api/yepez/rewards
        Route::post('/', function (Request $request) {
            return response()->json(['message' => 'Yepez: Crear Recompensa.']);
        })->name('yepez.rewards.store');
    });

    // --- Otras rutas del Admin que solo requieren el rol ---
    Route::get('/dashboard', function (Request $request) {
        return response()->json([
            'message' => 'Bienvenido al Dashboard de Yepez Central.',
        ]);
    })->name('yepez.dashboard');

});