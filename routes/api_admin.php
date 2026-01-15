<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\RedemptionController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\TicketController;

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

    // Ruta para obtener todas las sucursales

    Route::get('users/getAllSucursales', [UserController::class, 'getAllSucursales'])
        ->name('yepez.users.getAllSucursales');

    // Ruta para agregar una nueva recompensa

    Route::post('reward/addReward', [RewardController::class, 'addReward'])
        ->name('yepez.rewards.addReward');

    // Ruta para obtener transacciones con filtros

    Route::get('transactions/getTransactions', [TransactionController::class, 'getTransactions'])
        ->name('yepez.transactions.getTransactions');


    // Ruta para obtener todas las redenciones con filtros y paginación
    Route::get('redemptions/getAllRedemptions', [RedemptionController::class, 'getAllRedemptions'])
        ->name('yepez.redemptions.getAllRedemptions');

    // Ruta para actualizar el estado de una redención
    Route::patch('redemptions/{id}/status', [RedemptionController::class, 'updateStatus'])
        ->name('yepez.redemptions.updateStatus');

    // Ruta para obtener todas las recompensas activas
    Route::get('reward/getAllRewards', [RewardController::class, 'getAllRewards'])
        ->name('yepez.rewards.getAllRewards');

    // Ruta para actualizar una recompensa existente
    Route::put('reward/update/{id}', [RewardController::class, 'updateReward'])
        ->name('yepez.rewards.updateReward');

    // Ruta para desactivar una recompensa

    Route::patch('reward/desactivate/{id}', [RewardController::class, 'desactivateReward'])
        ->name('yepez.rewards.desactivateReward');

    // Ruta para actualizar la sucursal

    Route::put('users/editSucursal/{id}', [UserController::class, 'updateSucursal'])
        ->name('yepez.users.updateSucursal');

    // Ruta para cambiar el estado de una sucursal
    Route::patch('branches/changeStatus/{id}', [BranchController::class, 'changeStatus'])
        ->name('yepez.branches.changeStatus');

    // Ruta para crear una nueva sucursal junto con su usuario de tipo de sucursal

    Route::post('branches/store', [BranchController::class, 'store'])
        ->name('yepez.branches.store');

    // Ruta para actualizar una sucursal existente

    Route::patch('branches/update/{id}', [BranchController::class, 'update'])
        ->name('yepez.branches.update');    

    // Ruta para obtener la sucursal asociada a una branch

    Route::get('users/getSucursalDetails/{id}', [UserController::class, 'getSucursalDetails'])
        ->name('yepez.users.getSucursalDetails');

    // Ruta para obtener todos los tickets de una sucursal

    Route::get('tickets/getAllTicketsByUser/{id}', [TicketController::class, 'getAllTicketsByUser'])
        ->name('yepez.tickets.getAllTicketsByUser');

    // Ruta para obtener las transacciones de la sucursal 

    Route::get('transactions/getTotalTransacitonsByUser/{id}', [TransactionController::class, 'getTotalTransacitonsByUser'])
        ->name('yepez.transactions.getTotalTransacitonsByUser');

});