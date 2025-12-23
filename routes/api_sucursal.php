<?php

use App\Http\Controllers\Sucursal\TicketController;
use App\Http\Controllers\Sucursal\TransactionController;
use App\Http\Controllers\Sucursal\ClientController;
use App\Http\Controllers\Sucursal\RewardController;
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

    // --- Rutas de Clientes ---
    Route::post('clients', [ClientController::class, 'store'])
        ->name('sucursal.clients.store');

    // --- Rutas de Tickets ---
    Route::post('tickets', [TicketController::class, 'store'])
        ->name('sucursal.tickets.store');

    // Últimos 10 tickets de la sucursal autenticada
    Route::get('tickets/lastTickets', [TicketController::class, 'lastTickets'])
        ->name('sucursal.tickets.lastTickets');   

    // Canje de recompensas por puntos
    Route::post('redeem', [TransactionController::class, 'redeemReward'])
        ->name('sucursal.rewards.redeem');

    // Obtener las 10 mejores recompensas
    Route::get('rewards/getTopRewards', [RewardController::class, 'getTopRewards'])
        ->name('sucursal.rewards.getTopRewards');

    Route::get('transactions/lastTransactions', [TransactionController::class, 'lastTransactions'])
        ->name('sucursal.transactions.lastTransactions');

    Route::get('transactions/countTransactions', [TransactionController::class, 'countTransactions'])
        ->name('sucursal.transactions.countTransactions');


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