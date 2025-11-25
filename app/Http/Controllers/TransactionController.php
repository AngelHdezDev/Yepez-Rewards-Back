<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RedeemPointsRequest; // <-- ¡Añadir esta línea!
use App\Models\Transaction;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse; // <-- ¡Añadir esta línea! (Aunque no es obligatoria, es una buena práctica)

class TransactionController extends Controller
{
    /**
     * [CLIENTE] Muestra el historial de transacciones del usuario autenticado.
     */
    public function index(Request $request): JsonResponse
    {
        // El usuario autenticado
        $user = $request->user();

        // Obtener todas las transacciones del usuario, ordenadas por fecha descendente
        $transactions = $user->transactions()->orderBy('created_at', 'desc')->get();

        return Response::json([
            'message' => "Historial de transacciones de {$user->name}",
            'user_id' => $user->id,
            'balance' => $user->balance,
            'transactions' => $transactions,
        ], 200);
    }

    /**
     * [CLIENTE] Crea una nueva transacción de tipo DEBIT (Canje/Gasto de puntos).
     * Usa el RedeemPointsRequest para asegurar que haya saldo suficiente.
     */
    public function redeem(RedeemPointsRequest $request): JsonResponse
    {
        // Si llegamos aquí, la validación del Request ya aseguró que el saldo es suficiente.

        $user = $request->user();
        $data = $request->validated();

        try {
            // Utilizamos una transacción de base de datos para asegurar la atomicidad
            DB::beginTransaction();

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'DEBIT', // Gasto/Canje es DEBIT
                'amount' => $data['amount'],
                'description' => $data['description'],
                'status' => 'COMPLETED', // Es sincrónico y automático
            ]);

            DB::commit();

            // Refrescar el usuario para obtener el nuevo saldo (útil para la respuesta)
            $user->refresh();

            return Response::json([
                'message' => 'Canje de puntos realizado con éxito.',
                'transaction_id' => $transaction->id,
                'type' => $transaction->type,
                'amount_spent' => $transaction->amount,
                'current_balance' => $user->balance,
            ], 201); // 201 Created

        } catch (\Exception $e) {
            DB::rollBack();

            // Logear el error para debugging
            \Log::error("Error al procesar el canje de puntos para el usuario {$user->id}: " . $e->getMessage());

            return Response::json([
                'message' => 'Error interno al procesar el canje. Por favor, inténtelo de nuevo.',
            ], 500);
        }
    }
}