<?php

// IMPORTANTE: Dejamos este archivo casi vacío para evitar que se registren
// rutas de autenticación (como /register y /login) bajo el middleware 'web',
// lo que causa el error 419 Page Expired en peticiones de API.
// Todas las rutas de API Auth están ahora en routes/api_auth.php.

use Illuminate\Support\Facades\Route;

// Aquí es donde Laravel Breeze solía tener las rutas de autenticación.
// Las hemos movido para evitar la colisión.
