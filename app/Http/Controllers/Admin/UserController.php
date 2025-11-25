<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Para manejar errores

/**
 * Controlador para la administración de usuarios (CRUD) por parte del Administrador.
 */
class UserController extends Controller
{
    /**
     * [ADMIN] Crea un nuevo usuario y le asigna un rol (admin o client).
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            // 1. Crear el nuevo usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                // Aseguramos que la contraseña se hashee antes de guardarse
                'password' => Hash::make($request->password), 
                'balance' => 0, // Inicializar el balance a 0 para nuevos usuarios
            ]);

            // 2. Asignar el rol usando la librería de Spatie (asumo que la estás usando)
            // La validación en StoreUserRequest asegura que $request->role sea 'admin' o 'client'.
            $user->assignRole($request->role);

            // 3. Devolver una respuesta exitosa, cargando el rol para confirmación
            $user->load('roles');

            return response()->json([
                'message' => 'Usuario creado exitosamente.',
                'user' => $user,
            ], 201);

        } catch (\Exception $e) {
            // Loguear el error para debugging
            Log::error('Error al crear usuario por Admin: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error interno al crear el usuario. Por favor, intente de nuevo.',
            ], 500);
        }
    }
}