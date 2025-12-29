<?php

use App\Http\Controllers\Sucursal\TicketController;
use App\Http\Controllers\Sucursal\TransactionController;
use App\Http\Controllers\Sucursal\ClientController;
use App\Http\Controllers\Sucursal\RewardController;
use App\Http\Controllers\Sucursal\UserController;
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

    //Obtener las últimas 10 transacciones de la sucursal autenticada
    Route::get('transactions/lastTransactions', [TransactionController::class, 'lastTransactions'])
        ->name('sucursal.transactions.lastTransactions');

    
    // Contar transacciones totales
    Route::get('transactions/countTransactions', [TransactionController::class, 'countTransactions'])
        ->name('sucursal.transactions.countTransactions');

    // Contar canjes realizados
    Route::get('transactions/countCanjes', [TransactionController::class, 'countCanjes'])
        ->name('sucursal.transactions.countCanjes');

    // Obtener capacidad de recompensas para el usuario 
    Route::get('users/purchaseCapacity', [UserController::class, 'getPurchaseCapacity'])
        ->name('sucursal.users.purchaseCapacity');

    // Obtener todas las recompensas    

    Route::get('rewards/allRewards', [RewardController::class, 'getAllRewards'])
        ->name('sucursal.rewards.getAllRewards');

    // Obtener el total de tickets por usuario (sucursal)
    Route::get('tickets/getTotalTicket', [TicketController::class, 'getTotalTicketsByUser'])
        ->name('sucursal.rewards.getTotalTicketsByUser');

    // Obtener todos los tickets por usuario (sucursal) con paginación

    Route::get('tickets/getAllTicketsByUser', [TicketController::class, 'getAllTicketsByUser'])
        ->name('sucursal.rewards.getAllTicketsByUser');

    // Obtener el total de canjes por usuario (sucursal)
    Route::get('transactions/getTotalCanjesByUser', [TransactionController::class, 'getTotalCanjesByUser'])
        ->name('sucursal.transactions.getTotalCanjesByUser');

    // Obtener todas las transacciones por usuario (sucursal) con paginación
    Route::get('transactions/getTotalTransacitonsByUser', [TransactionController::class, 'getTotalTransacitonsByUser'])
        ->name('sucursal.transactions.getTotalTransacitonsByUser');
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