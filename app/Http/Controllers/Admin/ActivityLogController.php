<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;
use Exception;
class ActivityLogController extends Controller
{
    public function getLatestLogs(): JsonResponse
    {
        try {
            // Eager loading de 'causer' para evitar el problema de consultas N+1
            // Seleccionamos solo los últimos 4 registros
            $logs = Activity::with(['causer'])
                ->latest()
                ->limit(4)
                ->get();

            // Transformamos la colección para asegurar buenas prácticas de API (Data Transformation)
            $formattedLogs = $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'subject_type' => $log->subject_type,
                    'causer_name' => $log->causer ? $log->causer->name : 'System',
                    'causer_id' => $log->causer_id,
                    'properties' => $log->properties,
                    'created_at' => $log->created_at->toDateTimeString(),
                    'human_time' => $log->created_at->diffForHumans(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedLogs,
                'meta' => [
                    'count' => $formattedLogs->count()
                ]
            ], 200);

        } catch (Exception $e) {
            // Registramos el error internamente para debugging
            Log::error("Error al obtener logs de actividad: " . $e->getMessage());

            // Retornamos una respuesta genérica y segura para el cliente
            return response()->json([
                'success' => false,
                'message' => 'Ha ocurrido un error al procesar la solicitud de registros.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
