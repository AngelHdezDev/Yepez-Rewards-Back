<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RewardRequest;
use App\Http\Requests\Admin\StoreRewardRequest;
use App\Http\Requests\Admin\UpdateRewardRequest;
use App\Models\Reward;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Exception;


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

    public function updateReward(UpdateRewardRequest $request, $id)
    {
        try {
            $reward = Reward::findOrFail($id);

            // 1. Obtener los datos validados del Request
            $data = $request->validated();

            // 2. Manejo de la Imagen (Solo si se subió una nueva)
            if ($request->hasFile('image_url')) {
                Log::info('Nueva imagen detectada para reemplazo');

                // Eliminar antigua si existe
                if ($reward->image_url && Storage::disk('public')->exists($reward->image_url)) {
                    Storage::disk('public')->delete($reward->image_url);
                }

                // Guardar nueva y actualizar el array de datos
                $data['image_url'] = $request->file('image_url')->store('rewards', 'public');
            }

            // 3. Actualizar el modelo
            // update() rellena los campos, guarda y devuelve true si hubo cambios
            $reward->fill($data);

            if ($reward->isDirty()) {
                $reward->save();
                $message = 'Recompensa actualizada correctamente';
                Log::info('Registro actualizado exitosamente en BD', ['id' => $id]);
            } else {
                $message = 'No se realizaron cambios';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $reward
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'La recompensa no existe'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error crítico en updateReward:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la recompensa'
            ], 500);
        }
    }

    public function desactivateReward($id)
    {
        try {
            $reward = Reward::findOrFail($id);

            // Si ya está desactivado, podemos ahorrar la consulta a la BD
            if ($reward->is_active === 0 || $reward->is_active === false) {
                return response()->json([
                    'success' => true,
                    'message' => 'La recompensa ya se encuentra desactivada',
                    'data' => $reward
                ], 200);
            }

            // Cambiamos el estado directamente
            $reward->is_active = 0;
            $reward->save();

            Log::info('Recompensa desactivada manualmente', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Recompensa desactivada correctamente',
                'data' => $reward
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'La recompensa no existe'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al desactivar recompensa:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ], 500);
        }
    }

    public function getTotalRewards(): JsonResponse
    {
        try {
            $stats = Reward::selectRaw("
                count(*) as total,
                count(case when is_active = 1 then 1 end) as active,
                count(case when is_active = 0 then 1 end) as inactive
            ")->first();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_rewards' => (int) $stats->total,
                    'active_rewards' => (int) $stats->active,
                    'inactive_rewards' => (int) $stats->inactive,
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error('Error al obtener el desglose de recompensas:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al procesar las estadísticas de recompensas.'
            ], 500);
        }
    }
}
