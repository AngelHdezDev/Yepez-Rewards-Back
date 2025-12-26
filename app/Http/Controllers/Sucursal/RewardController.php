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
            $rewards = Reward::limit(10)->get();
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
}
