<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Para manejar errores
use Illuminate\Http\Request;

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

    public function getAllSucursales(Request $request): JsonResponse
    {
        try {


            $users = User::with('roles')
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'sucursal');
                })
                ->paginate(10);

            return response()->json([
                'message' => 'Usuarios recuperados exitosamente.',
                'data' => $users->items(),
                'pagination' => [
                    'total' => $users->total(),
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem()
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al recuperar usuarios por Admin: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error interno al recuperar los usuarios. Por favor, intente de nuevo.',
            ], 500);
        }
    }

    public function updateSucursal(UpdateUserRequest $request, $id)
    {
        // 1. Buscar el usuario
        $user = User::findOrFail($id);

        try {
            // 2. Obtener los datos ya validados desde el Request
            $validated = $request->validated();

            // 3. Preparar datos para actualizar
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            // 4. Solo actualizar la contraseña si se proporcionó una nueva
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            return response()->json([
                'message' => 'Sucursal actualizada exitosamente',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la sucursal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}