<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\RedemptionController;

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

    Route::get('redemptions/getAllRedemptions', [RedemptionController::class, 'getAllRedemptions'])
        ->name('yepez.redemptions.getAllRedemptions');


});