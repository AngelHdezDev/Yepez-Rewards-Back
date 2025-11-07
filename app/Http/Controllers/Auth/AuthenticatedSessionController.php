<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        // Si la autenticación es exitosa, creamos el token de Sanctum.
        $user = $request->user();
        
        // Eliminamos tokens viejos si existen para mantener limpio.
        $user->tokens()->where('name', 'api-token')->delete();

        // Creamos y devolvemos el nuevo token.
        $token = $user->createToken('api-token', ['*'])->plainTextToken;

        // Devuelve el token junto con la información del usuario
        return response([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        // El logout en API es simplemente borrar el token actual.
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        
        // Devuelve una respuesta vacía de éxito.
        return response()->noContent();
    }
}