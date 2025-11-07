<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Maneja una solicitud de registro entrante.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            // Inicializamos el balance de puntos para el Módulo 2
            'current_balance' => 0, 
        ]);

        // Asignación de Rol (CRUCIAL para el Módulo 1)
        // Todo nuevo registro es automáticamente un 'client'
        $user->assignRole('client');

        event(new Registered($user));

        // ----------------------------------------------------
        // SOLUCIÓN AL ERROR 419: NO USAR AUTH::LOGIN
        // En su lugar, creamos y devolvemos el token de API (Sanctum)
        // ----------------------------------------------------
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 201); // Devolvemos 201 Created con el usuario y el token
    }
}