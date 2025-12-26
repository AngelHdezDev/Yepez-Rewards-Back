<?php

namespace App\Http\Controllers\Sucursal;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Reward;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserController extends Controller
{
    public function getPurchaseCapacity(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No autorizado.'
            ], 401);
        }

        try {
            /**
             * Optimizamos la consulta:
             * 1. Usamos el modelo Reward directamente para contar.
             * 2. Filtramos por el saldo actual del usuario autenticado.
             */
            $canAffordCount = Reward::where('is_active', true)
                ->where('stock', '>', 0)
                ->where('cost_points', '<=', $user->current_balance)
                ->count();

            // Estructura de respuesta plana y fácil de consumir
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user_id' => $user->id,
                    'current_balance' => (int) $user->current_balance,
                    'available_rewards_count' => $canAffordCount,
                    'can_redeem' => $canAffordCount > 0
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error("Error calculando capacidad de compra para usuario {$user->id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error interno al calcular la capacidad de canje.'
            ], 500);
        }
    }
}