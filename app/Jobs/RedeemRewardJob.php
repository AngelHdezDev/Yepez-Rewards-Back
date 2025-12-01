<?php

namespace App\Jobs;

use App\Models\Redemption; // <<<< ¡Importación necesaria!
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Para registrar qué está haciendo el Job

class RedeemRewardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * La instancia del canje ya creada en el controlador.
     */
    protected Redemption $redemption; // <<<< El Job ahora espera la Redemption

    /**
     * Crea una nueva instancia del Job.
     *
     * @param Redemption $redemption La instancia de canje.
     */
    public function __construct(Redemption $redemption)
    {
        // Esto corrige el error de tipado al esperar Redemption en lugar de User.
        $this->redemption = $redemption;
    }

    /**
     * Ejecutar el Job.
     */
    public function handle(): void
    {
        // 1. OBTENER DATOS
        $cliente = $this->redemption->user; // Acceso al cliente vía relación
        $premio = $this->redemption->reward; // Acceso al premio vía relación

        Log::info("Iniciando procesos post-canje para Redemption ID: {$this->redemption->id}");

        // 2. Lógica Post-Canje (ej. enviar notificación al cliente)
        // \Mail::to($cliente->email)->send(new RewardRedeemed($this->redemption));

        // 3. Puedes usar este Job para actualizar el estado, si lo pones a PENDING
        if ($this->redemption->status === 'PENDING') {
            $this->redemption->update([
                'status' => 'COMPLETED',
                // Opcionalmente puedes registrar la fecha de finalización
                'completed_at' => now(),
            ]);
            Log::info("Redemption ID: {$this->redemption->id} actualizado a COMPLETED.");
        } else {
            Log::warning("Redemption ID: {$this->redemption->id} no fue PENDING. Estado actual: {$this->redemption->status}");
        }

        // Ya no es necesario hacer la transacción ni el decremento de puntos/stock aquí
        // porque el controlador ya lo hizo de forma atómica.
        Log::info("Procesos completados para Redemption ID: {$this->redemption->id}");
    }

    /**
     * Manejar un Job fallido.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error("Fallo el Job de post-canje para Redemption ID: {$this->redemption->id}: " . $exception->getMessage());
    }
}