<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\AssignPointsJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Procesa una nueva transacción (actualmente solo soporta CREDIT).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Obtener el ID del administrador autenticado
        // Dado que esta ruta será protegida con 'auth:sanctum' y 'role:admin',
        // podemos asumir que el usuario actual es un administrador.
        $adminId = $request->user()->id;

        // 2. Validación de la Petición
        $validatedData = $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                // Opcional: Podrías añadir una regla para asegurar que el usuario
                // destino no sea un 'admin', pero por ahora, solo 'exists'
            ],
            'type' => [
                'required',
                'string',
                Rule::in(['CREDIT', 'DEBIT']), // Tipos de transacción válidos
            ],
            'amount' => 'required|integer|min:1', // El monto debe ser al menos 1
            // Otros campos opcionales como 'reference' o 'note' podrían ir aquí
        ]);

        $userId = $validatedData['user_id'];
        $type = $validatedData['type'];
        $amount = $validatedData['amount'];

        // 3. Manejo de la Lógica por Tipo
        switch ($type) {
            case 'CREDIT':
                // Despachar el Job a la cola.
                // El Job manejará la lógica de la base de datos de manera asíncrona.
                AssignPointsJob::dispatch($userId, $amount, $adminId);

                // Respuesta inmediata al administrador
                return response()->json([
                    'message' => 'Solicitud de asignación de puntos enviada a la cola exitosamente.',
                    'transaction_type' => 'CREDIT',
                    'user_id' => $userId,
                    'amount' => $amount,
                ], 202); // Código 202 Accepted indica que la solicitud fue aceptada
            // para procesamiento, pero no se ha completado.

            case 'DEBIT':
                // TODO: Implementar la lógica para DEBIT (retirar puntos),
                // que posiblemente requeriría un Job diferente o manejo síncrono
                // si se necesita validar el saldo en tiempo real antes de debitar.
                return response()->json(['message' => 'Tipo DEBIT no implementado aún.'], 501);

            default:
                return response()->json(['message' => 'Tipo de transacción no soportado.'], 400);
        }
    }


    public function getTransactions(Request $request)
    {
        try {
            // 1. Iniciamos el query builder
             $query = Transaction::with(['User']);

            // 2. Aplicamos filtros solo si están presentes en la URL
            // Usamos whereDate para ignorar la hora y comparar solo Año-Mes-Día
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // 3. Ejecutamos la paginación
            // latest() ordena de la más reciente a la más antigua
            $transactions = $query->latest()
                ->paginate(20)
                ->withQueryString(); // CRUCIAL: Mantiene ?start_date=... en los links de la paginación

            // 4. Retornamos la respuesta estructurada
            return response()->json([
                'status' => 'success',
                'data' => $transactions->items(), // Los registros actuales
                'pagination' => [
                    'total' => $transactions->total(),
                    'count' => $transactions->count(),
                    'per_page' => $transactions->perPage(),
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'next_page_url' => $transactions->nextPageUrl(),
                    'prev_page_url' => $transactions->previousPageUrl(),
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error("Error en getTransactions: " . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la solicitud de transacciones.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}