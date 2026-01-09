<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RewardRequest;
use App\Http\Requests\Admin\StoreRewardRequest;
use App\Models\Reward;
use Illuminate\Support\Facades\Log;

class RewardController extends Controller
{
    public function addReward(StoreRewardRequest $request)
    {
        Log::info('--- INICIO DE PROCESO DE GUARDADO [DEBUG_REWARDS] ---');

        // 1. Log de todos los datos recibidos (excepto el archivo binario)
        Log::info('Datos recibidos en Request:', $request->except('image'));

        // 2. Verificar si el archivo llega al controlador
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            Log::info('Archivo detectado:', [
                'nombre_original' => $file->getClientOriginalName(),
                'mimetype' => $file->getClientMimeType(),
                'tamaño' => $file->getSize(),
            ]);

            try {
                $path = $file->store('rewards', 'public');
                Log::info('Archivo almacenado con éxito:', ['path_generado' => $path]);
            } catch (\Exception $e) {
                Log::error('Error al intentar guardar el archivo físico:', ['error' => $e->getMessage()]);
                $path = null;
            }
        } else {
            Log::warning('No se detectó ningún archivo en el campo "image".');
            $path = null;
        }

        // 3. Preparar array para la BD
        $rewardData = [
            'name' => $request->name,
            'description' => $request->description,
            'image_url' => $path, // <--- Este es el valor clave
            'cost_points' => $request->cost_points,
            'code' => $request->code,
            'stock' => $request->stock,
            'is_active' => $request->is_active ?? true,
        ];

        Log::info('Array preparado para Reward::create:', $rewardData);

        try {
            // 4. Intento de guardado en base de datos
            $reward = Reward::create($rewardData);

            Log::info('Registro creado exitosamente en BD:', $reward->toArray());

            return response()->json([
                'message' => 'Reward creado',
                'data' => $reward
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al insertar en la base de datos:', [
                'mensaje' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error en el servidor',
                'detalles' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllRewards(Request $request)
    {
        try {
            
            $rewards = Reward::where('is_active', 1)
                ->latest() 
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $rewards->items(),
                'pagination' => [
                    'total' => $rewards->total(),
                    'current_page' => $rewards->currentPage(),
                    'last_page' => $rewards->lastPage(),
                    'per_page' => $rewards->perPage(),
                    'next_page_url' => $rewards->nextPageUrl(),
                    'prev_page_url' => $rewards->previousPageUrl(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en el servidor',
                'detalles' => $e->getMessage()
            ], 500);
        }
    }
}
