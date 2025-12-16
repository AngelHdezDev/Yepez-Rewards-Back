<?php

namespace App\Http\Controllers\Sucursal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursal\StoreTicketRequest; // Usamos el request actualizado
use App\Jobs\ProcessTicketJob;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
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
            'message' => 'Ticket recibido y puesto en cola para procesamiento. Los puntos se acreditarán en breve.', $exists,
            'ticket_number' => $ticketData['ticket_number'],
            'user_id' => $ticketData['user_id'],
        ], 202);
    }
}