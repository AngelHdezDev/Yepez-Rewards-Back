<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Transaction;

class ProcessTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $ticketData;

    /**
     * Crea una nueva instancia del Job.
     *
     * @param array $ticketData Datos del ticket (user_id, branch_id, amount, ticket_number, issue_date)
     * @return void
     */
    public function __construct(array $ticketData)
    {
        $this->ticketData = $ticketData;
    }

    /**
     * Ejecuta el Job (lógica principal).
     *
     * @return void
     */
    public function handle(): void
    {
        // 1. Definir la tasa de conversión (ej. 1 punto por cada $10 gastados)
        // ESTA ES UNA REGLA DE NEGOCIO: 10% del monto gastado es puntos.
        // Si el monto gastado es 100, se ganan 10 puntos (100 / 10).
        $conversionRate = 10;
        
        // 2. Calcular los puntos a ganar
        $amount = $this->ticketData['amount'];
        $pointsEarned = floor($amount / $conversionRate);

        if ($pointsEarned < 1) {
            // Si no gana puntos, solo registramos el ticket y terminamos el Job.
             Log::info("Ticket ID {$this->ticketData['ticket_number']} no generó puntos (Monto: $amount). Registrando solo ticket.");
             Ticket::create(array_merge($this->ticketData, [
                'points_earned' => 0,
            ]));
            return;
        }

        // 3. Iniciar Transacción de Base de Datos para asegurar la atomicidad
        DB::transaction(function () use ($pointsEarned, $amount) {
            
            $userId = $this->ticketData['user_id'];
            $ticketNumber = $this->ticketData['ticket_number'];

            // Cargar el usuario y bloquear la fila para evitar condiciones de carrera (bloqueo pesimista)
            $user = User::lockForUpdate()->find($userId);

            if (!$user) {
                // Si el usuario no existe, lanzamos una excepción para que el Job falle
                throw new \Exception("Usuario ID {$userId} no encontrado para procesar ticket {$ticketNumber}.");
            }
            
            // --- PASO 1: Registrar el Ticket de Compra ---
            $ticket = Ticket::create(array_merge($this->ticketData, [
                'points_earned' => $pointsEarned,
            ]));

            // --- PASO 2: Actualizar el Saldo del Usuario ---
            // Usamos increment para una operación atómica
            $user->increment('current_balance', $pointsEarned);
            $newBalance = $user->current_balance;

            // --- PASO 3: Registrar la Transacción de CRÉDITO ---
            Transaction::create([
                'user_id' => $userId,
                'type' => 'CREDIT',
                'amount' => $pointsEarned,
                'description' => "Puntos por Ticket de Compra #{$ticketNumber} (Monto: \${$amount})",
                // Relación polimórfica: enlazar la transacción al ticket que la originó
                // El campo 'related_type' se llenará con 'App\Models\Ticket' y 'related_id' con $ticket->id
                'related_type' => Ticket::class,
                'related_id' => $ticket->id,
                'status' => 'COMPLETED',
            ]);

            Log::info("Ticket procesado. User ID: {$userId}. Puntos Ganados: {$pointsEarned}. Nuevo Saldo: {$newBalance}");
        });
    }

    /**
     * Manejar un job que ha fallado.
     *
     * @param  \Throwable  $exception
     * @return void
     */ 
    public function failed(\Throwable $exception)
    {
        Log::error("ProcessTicketJob falló para Ticket #{$this->ticketData['ticket_number']} después de {$this->tries} intentos: " . $exception->getMessage());
        // Aquí podrías enviar un email al equipo de soporte o registrar el ticket como "pendiente de revisión"
    }
}