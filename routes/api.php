<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de la API
|--------------------------------------------------------------------------
|
| Aquí registramos las rutas de la API. Estas rutas utilizan el middleware
| 'api' y son ideales para autenticación basada en tokens como Sanctum.
|
*/

// Incluimos nuestras rutas de autenticación de API, que contienen /register, /login, /user y /logout.
require __DIR__.'/api_auth.php'; 
require __DIR__ . '/api_admin.php';

// Puedes agregar otras rutas de tu aplicación debajo de este require.
// Ejemplo:
// Route::middleware('auth:sanctum')->get('/items', [ItemController::class, 'index']);
