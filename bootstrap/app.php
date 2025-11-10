<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Añade el alias para el middleware de roles de Spatie
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            // Mantén los otros alias que ya tenías, si los había
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class, 
        ]);
        
        // Asegúrate de que el grupo 'api' se mantenga como estaba, con Sanctum.
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Si usaste Breeze, el middleware de Sanctum para API debería estar aquí por defecto,
        // pero asegúrate de que el grupo 'api' tenga 'auth:sanctum' si lo necesitas en rutas.
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();