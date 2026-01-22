<?php

namespace App\Http\Controllers\Sucursal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class RewardController extends Controller
{
    public function getTopRewards()
    {
        try {
            $rewards = Reward::where('is_active', 1)->limit(10)->get();
            if ($rewards->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No se encontraron recompensas',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Recompensas recuperadas exitosamente',
                'data' => $rewards
            ], 200);

        } catch (Exception $e) {
            Log::error("Error al recuperar recompensas: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al recuperar las recompensas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function countAwardAvailable(): JsonResponse
    {
        try {
            $count = Reward::where('available', true)->count();

            return response()->json([
                'success' => true,
                'message' => 'Conteo de recompensas disponibles recuperado exitosamente',
                'data' => ['available_rewards_count' => $count]
            ], 200);

        } catch (Exception $e) {
            Log::error("Error al contar recompensas disponibles: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al contar las recompensas disponibles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllRewards(): JsonResponse
    {
        try {
            // Usamos paginate(10) que automáticamente detecta el parámetro ?page de la URL
            $rewards = Reward::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Verificamos si la colección de datos está vacía
            if ($rewards->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No se encontraron recompensas disponibles.',
                    'data' => [],
                    'pagination' => null
                ], 200);
            }

            /**
             * Al usar paginate(), Laravel devuelve un LengthAwarePaginator.
             * Estructuramos la respuesta para que el front tenga los datos y la info de navegación por separado.
             */
            return response()->json([
                'success' => true,
                'message' => 'Recompensas recuperadas exitosamente',
                'data' => $rewards->items(), // Solo los 10 registros de la página actual
                'pagination' => [
                    'total' => $rewards->total(),
                    'count' => $rewards->count(),
                    'per_page' => $rewards->perPage(),
                    'current_page' => $rewards->currentPage(),
                    'total_pages' => $rewards->lastPage(),
                    'next_page_url' => $rewards->nextPageUrl(),
                    'prev_page_url' => $rewards->previousPageUrl(),
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error("Error al recuperar recompensas paginadas: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al recuperar las recompensas',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }
}
