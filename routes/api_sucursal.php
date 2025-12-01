<?php

use App\Http\Controllers\Sucursal\TicketController;
use App\Http\Controllers\Sucursal\TransactionController; 
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Rutas de Sucursales (Role: sucursal)
|--------------------------------------------------------------------------
|
| Estas rutas están destinadas a las Sucursales que operan los canjes y puntos.
|
*/


Route::middleware(['auth:sanctum', 'role:sucursal'])->group(function () {


    Route::post('tickets', [TicketController::class, 'store'])
        ->name('sucursal.tickets.store');


    Route::post('redeem', [TransactionController::class, 'redeemReward'])
        ->name('sucursal.rewards.redeem');


    // // GET /api/sucursal/points/check
    // // Requiere el permiso 'check points'
    // Route::get('/points/check', function (Request $request) {
    //     return response()->json([
    //         'message' => 'Sucursal: Consultar Puntos de Cliente. (Permiso: check points)',
    //         'user_id' => $request->user()->id,
    //     ]);
    // })->middleware('permission:check points');

    // // POST /api/sucursal/reward/redeem
    // // Requiere el permiso 'redeem reward'
    // Route::post('/reward/redeem', function (Request $request) {
    //     return response()->json([
    //         'message' => 'Sucursal: Ejecutar Canje de Recompensa. (Permiso: redeem reward)',
    //         'user_id' => $request->user()->id,
    //     ]);
    // })->middleware('permission:redeem reward');
});