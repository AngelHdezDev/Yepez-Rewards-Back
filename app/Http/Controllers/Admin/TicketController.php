<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class TicketController extends Controller
{
    public function getAllTicketsByUser($id): JsonResponse
    {

        try {
            $paginatedTickets = Ticket::where('user_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Tickets recuperados con éxito',
                'data' => [
                    'user_id' => (int) $id,
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
            Log::error("Error al obtener tickets del usuario {$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el listado de facturas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPointsMonth(): JsonResponse
    {
        try {
            $user = Auth::user();
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $totalPoints = Ticket::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->sum('points_earned');

            return response()->json([
                'success' => true,
                'message' => 'Puntos del mes recuperados con éxito',
                'data' => [
                    'user_id' => $user->id,
                    'month' => $currentMonth,
                    'year' => $currentYear,
                    'total_points' => $totalPoints,
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error("Error al obtener puntos del mes para el usuario {$user->id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los puntos del mes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
