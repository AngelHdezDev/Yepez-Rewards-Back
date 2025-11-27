<?php

namespace App\Http\Controllers\Sucursal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursal\StoreTicketRequest; // Usamos el request actualizado
use App\Jobs\ProcessTicketJob;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    /**
     * [SUCURSAL] Registra un nuevo ticket de compra y despacha el Job de procesamiento.
     * * @param StoreTicketRequest $request
     * @return JsonResponse
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        // Los datos validados del ticket
        $ticketData = $request->validated();

        // Despachar el Job a la cola para su procesamiento asíncrono
        ProcessTicketJob::dispatch($ticketData);

        // Respuesta inmediata a la sucursal
        return response()->json([
            'message' => 'Ticket recibido y puesto en cola para procesamiento. Los puntos se acreditarán en breve.',
            'ticket_number' => $ticketData['ticket_number'],
            'user_id' => $ticketData['user_id'],
        ], 202); // Código 202 Accepted
    }
}