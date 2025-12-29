<?php

namespace App\Http\Controllers\Sucursal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursal\StoreTicketRequest; // Usamos el request actualizado
use App\Jobs\ProcessTicketJob;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class TicketController extends Controller
{

    public function lastTickets(Request $request): JsonResponse
    {
        // 1. OBTENER EL ID DE LA SUCURSAL AUTENTICADA (user_id)
        $branchId = Auth::id();

        if (!$branchId) {
            return response()->json([
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }

        try {
            $sucursalTickets = Ticket::where('user_id', $branchId)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            return response()->json([
                'message' => 'Tickets recuperados exitosamente.',
                'branchId' => $branchId,
                'tickets' => $sucursalTickets,
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores de base de datos o consulta
            return response()->json([
                'message' => 'Error interno del servidor: No se pudieron recuperar los tickets.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticketData = $request->validated();

        // VERIFICAR DUPLICADO ANTES de despachar el Job
        $exists = Ticket::where('ticket_number', $ticketData['ticket_number'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Error: Este número de ticket ya ha sido registrado para esta sucursal.',
                'errors' => [
                    'ticket_number' => ['Este número de ticket ya ha sido registrado para esta sucursal.']
                ]
            ], 422);
        }

        // Si no existe duplicado, despachar el Job
        ProcessTicketJob::dispatch($ticketData);

        return response()->json([
            'message' => 'Ticket recibido y puesto en cola para procesamiento. Los puntos se acreditarán en breve.',
            $exists,
            'ticket_number' => $ticketData['ticket_number'],
            'user_id' => $ticketData['user_id'],
        ], 202);
    }

    public function getTotalTicketsByUser(): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }
        try {
            // Opción 1: Eloquent (Recomendado por legibilidad)
            $totalTickets = Ticket::where('user_id', $userId)->count();

            return response()->json([
                'success' => true,
                'message' => 'Conteo de tickets realizado con éxito',
                'data' => [
                    'user_id' => (int) $userId,
                    'total_facturas' => $totalTickets
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error("Error al contar tickets del usuario {$userId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el total de facturas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllTicketsByUser(): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Authentication required.'
            ], 401);
        }

        try {
            $paginatedTickets = Ticket::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Tickets recuperados con éxito',
                'data' => [
                    'user_id' => (int) $userId,
                    'total_facturas' => $paginatedTickets->total(), // Total global de tickets
                    'tickets' => $paginatedTickets->items(),       // Los 5 tickets de la página actual
                ],
                'pagination' => [
                    'current_page' => $paginatedTickets->currentPage(),
                    'last_page' => $paginatedTickets->lastPage(),
                    'per_page' => $paginatedTickets->perPage(),
                    'next_page' => $paginatedTickets->nextPageUrl(),
                    'prev_page' => $paginatedTickets->previousPageUrl(),
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error("Error al obtener tickets del usuario {$userId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el listado de facturas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}