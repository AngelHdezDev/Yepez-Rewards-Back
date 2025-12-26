<?php

namespace App\Http\Controllers\Sucursal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursal\RedeemPointsRequest;
use App\Models\Reward;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Exception;
use App\Jobs\RedeemRewardJob;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Procesa la solicitud de canje de un premio por puntos.
     *
     * @param RedeemPointsRequest $request
     * @return JsonResponse
     */
    public function redeemReward(RedeemPointsRequest $request): JsonResponse
    {
        $clienteId = $request->validated('cliente_id');
        $premioId = $request->validated('premio_id');

        // 1. Encontrar Cliente y Premio (Verificación inicial de existencia)
        $cliente = User::find($clienteId);
        $premio = Reward::find($premioId);

        if (!$cliente) {
            return response()->json(['message' => 'Error: Cliente no encontrado (ID: ' . $clienteId . ')'], 404);
        }
        if (!$premio) {
            return response()->json(['message' => 'Error: Premio no encontrado (ID: ' . $premioId . ')'], 404);
        }

        DB::beginTransaction();

        try {
            // --- CORRECCIÓN CLAVE ---
            // 2. Re-obtener las entidades APLICANDO EL BLOQUEO (lockForUpdate)
            // Esto asegura la atomicidad y previene el error "Builder::fresh()".
            $cliente = User::where('id', $clienteId)->lockForUpdate()->first();
            $premio = Reward::where('id', $premioId)->lockForUpdate()->first();

            // 3. Verificación de Puntos y Stock
            $costoPuntos = $premio->cost_points;

            // Verificación de stock
            if ($premio->stock <= 0) {
                DB::rollBack();
                return response()->json(['message' => "El premio '{$premio->name}' no tiene stock disponible."], 403);
            }

            // Verificación de puntos del cliente
            if ($cliente->current_balance < $costoPuntos) {
                DB::rollBack();
                return response()->json([
                    'message' => 'El cliente no tiene puntos suficientes para canjear este premio.',
                    'required_points' => $costoPuntos,
                    'current_points' => $cliente->current_balance,
                ], 403);
            }

            // --- PASO CLAVE: GENERAR CÓDIGO DE CANJE ÚNICO ---
            $redemptionCode = (string) Str::uuid();

            // 4. Registrar el Canje
            $canje = $cliente->redemptions()->create([
                'reward_id' => $premio->id,
                'points_cost' => $costoPuntos,
                'reward_name' => $premio->name,
                'redemption_code' => $redemptionCode,
                'status' => 'PENDING',
            ]);

            // 5. Descontar Stock del Premio 
            $premio->decrement('stock', 1);

            // 6. Actualizar el Saldo del Cliente (Descuento de Puntos)
            $cliente->decrement('current_balance', $costoPuntos);

            // 7. Registrar la Transacción de Gasto (DEBIT)
            // (Esta es una buena práctica para el historial de transacciones)
            $cliente->transactions()->create([
                'type' => 'DEBIT',
                'description' => "Canje de premio: {$premio->name}",
                'amount' => $costoPuntos,
                'status' => 'COMPLETED',
                'reward_id' => $premio->id,
            ]);

            // 8. Ejecutar Job (para notificaciones asíncronas)
            RedeemRewardJob::dispatch($canje);

            DB::commit();

            // 9. Retornar respuesta
            return response()->json([
                'message' => '¡Canje de premio completado con éxito!',
                'premio_canjeado' => $premio->name,
                // Usamos 'fresh()' aquí para obtener los valores más recientes del modelo para la respuesta
                'puntos_restantes' => $cliente->fresh()->current_balance,
                'stock_restante' => $premio->fresh()->stock,
                'canje_id' => $canje->id,
                'redemption_code' => $redemptionCode,
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            // Asegúrate de que Laravel esté configurado para loguear errores (revisa .env y logging.php)
            \Log::error('Error al procesar canje: ' . $e->getMessage() . ' en línea ' . $e->getLine());

            return response()->json([
                'message' => 'Ocurrió un error inesperado al procesar el canje.',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }

    public function lastTransactions()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }

        try {
            $sucursalTransaction = Transaction::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            return response()->json([
                'message' => 'Transacciones recuperadas exitosamente.',
                'userId' => $userId,
                'transactions' => $sucursalTransaction,
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores de base de datos o consulta
            return response()->json([
                'message' => 'Error interno del servidor: No se pudieron recuperar los tickets.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function countTransactions()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }

        try {
            $transactionCount = Transaction::where('user_id', $userId)->count();

            return response()->json([
                'message' => 'Conteo de transacciones recuperado exitosamente.',
                'userId' => $userId,
                'transaction_count' => $transactionCount,
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores de base de datos o consulta
            return response()->json([
                'message' => 'Error interno del servidor: No se pudo contar las transacciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function countCanjes()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }

        try {
            $canjeCount = Transaction::where('user_id', $userId)
                ->where('type', 'DEBIT')
                ->count();


            return response()->json([
                'message' => 'Conteo de canjes recuperado exitosamente.',
                'userId' => $userId,
                'canje_count' => $canjeCount,
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores de base de datos o consulta
            return response()->json([
                'message' => 'Error interno del servidor: No se pudo contar los canjes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}


