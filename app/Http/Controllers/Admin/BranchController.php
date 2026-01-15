<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class BranchController extends Controller
{
    //
    public function changeStatus(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $branch = Branch::findOrFail($id);

            // Inversión lógica del estado booleano
            $branch->is_active = !$branch->is_active;
            $branch->save();

            DB::commit();

            Log::info("Estado de sucursal ID {$id} cambiado a: " . ($branch->is_active ? 'Activo' : 'Inactivo'));

            return response()->json([
                'status' => 'success',
                'message' => 'El estado de la sucursal se actualizó correctamente.',
                'data' => [
                    'id' => $branch->id,
                    'is_active' => $branch->is_active
                ]
            ], 200);

        } catch (ModelNotFoundException $e) {
            // No es necesario rollback porque findOrFail ocurre antes de cualquier cambio
            Log::warning("Sucursal no encontrada para cambio de estado. ID: {$id}");

            return response()->json([
                'status' => 'error',
                'message' => 'La sucursal solicitada no existe.'
            ], 404);

        } catch (Exception $e) {
            DB::rollBack();

            Log::error("Error al cambiar estado de sucursal ID {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor al procesar la solicitud.'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        // 1. Validación (puedes mover esto a un StoreBranchUserRequest)
        $validated = $request->validate([
            // Datos de la Tabla Branches
            'branch_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:500',

            // Datos de la Tabla Users
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        try {
            // 2. Transacción de Base de Datos (Misma lógica que tus seeders)
            return DB::transaction(function () use ($validated) {

                // A. Crear la Sucursal (Equivalente al insert de tu seeder)
                $branch = Branch::create([
                    'name' => $validated['branch_name'],
                    'city' => $validated['city'],
                    'address' => $validated['address'],
                    'is_active' => true, // Por defecto activa como en tu seeder
                ]);

                // B. Crear el Usuario (Equivalente al firstOrCreate de tu seeder)
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'branch_id' => $branch->id, // Vinculación automática
                ]);

                // C. Asignar Rol (Spatie Permissions)
                if (!$user->hasRole('sucursal')) {
                    $user->assignRole('sucursal');
                }

                Log::info("Nueva sucursal y usuario creados exitosamente", [
                    'branch_id' => $branch->id,
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Sucursal y usuario creados correctamente',
                    'data' => [
                        'branch' => $branch,
                        'user' => $user->only(['id', 'name', 'email'])
                    ]
                ], 201);
            });

        } catch (Exception $e) {
            Log::error("Error al crear sucursal unificada: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'No se pudo completar el registro de la sucursal',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        // 1. Buscar la sucursal y el usuario asociado
        $branch = Branch::findOrFail($id);
        $user = User::where('branch_id', $branch->id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un usuario vinculado a esta sucursal.'
            ], 404);
        }

        // 2. Validación
        $validated = $request->validate([
            'branch_name' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
        ]);

        try {
            return DB::transaction(function () use ($validated, $branch, $user) {

                // A. Actualizar Sucursal
                $branchData = [];

                // MAPEO: Si viene 'branch_name', lo asignamos a la columna 'name'
                if (isset($validated['branch_name'])) {
                    $branchData['name'] = $validated['branch_name'];
                }

                // Estos campos sí se llaman igual en la BD
                if (isset($validated['city']))
                    $branchData['city'] = $validated['city'];
                if (isset($validated['address']))
                    $branchData['address'] = $validated['address'];

                if (!empty($branchData)) {
                    $branch->update($branchData);
                }

                // B. Actualizar Usuario
                $userData = collect($validated)->only(['name', 'email'])->toArray();

                if (isset($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }

                if (!empty($userData)) {
                    $user->update($userData);
                }

                Log::info("Sucursal y usuario actualizados", [
                    'branch_id' => $branch->id,
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Datos actualizados correctamente',
                    'data' => [
                        'branch' => $branch->refresh(),
                        'user' => $user->refresh()->only(['id', 'name', 'email'])
                    ]
                ], 200);
            });

        } catch (Exception $e) {
            Log::error("Error al actualizar sucursal unificada: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la actualización',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
