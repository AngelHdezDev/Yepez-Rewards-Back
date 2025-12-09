<?php

namespace App\Http\Controllers\Sucursal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursal\StoreClientRequest; // Asume que este Request existe y valida los campos
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Gestiona las operaciones CRUD para los clientes desde la perspectiva de una sucursal.
 */
class ClientController extends Controller
{
    /**
     * Registra un nuevo cliente y lo asigna a la sucursal
     * del usuario autenticado.
     *
     * @param StoreClientRequest $request
     * @return JsonResponse
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        // Obtener la ID del usuario autenticado (que representa a la sucursal)
        $branchUserId = auth()->id();

        // Verificar que hay un usuario autenticado
        if (!$branchUserId) {
             return response()->json([
                 'message' => 'Error de autenticación. No se pudo identificar a la sucursal.'
             ], 403);
        }

        DB::beginTransaction();

        try {
            // Crear el nuevo cliente. El campo 'current_balance' se omite de forma segura.
            $cliente = Client::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password')),
                'phone' => $request->validated('phone'),
                
                // Asignar el cliente a la sucursal (usuario) que lo está creando
                'branch_id' => $branchUserId, 
            ]);

            DB::commit();

            // Devolver una respuesta exitosa
            return response()->json([
                'message' => 'Cliente registrado y asociado correctamente a su sucursal.',
                'cliente' => [
                    'id' => $cliente->id,
                    'name' => $cliente->name,
                    'email' => $cliente->email,
                    'phone' => $cliente->phone,
                    'branch_id' => $cliente->branch_id,
                ]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            // Registrar el error para fines de depuración en el log de Laravel
            \Log::error('Error al registrar nuevo cliente en sucursal: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Ocurrió un error inesperado al intentar registrar el cliente.',
                'error_detail' => 'Consulte el log para más detalles.'
            ], 500);
        }
    }
}