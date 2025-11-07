<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignPointsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * El número de veces que el job puede ser reintentado.
     * @var int
     */
    public $tries = 3;

    protected $userId;
    protected $amount;
    protected $adminId;

    /**
     * Crea una nueva instancia del Job.
     *
     * @param int $userId El ID del usuario al que se le asignarán los puntos.
     * @param int $amount La cantidad de puntos a asignar (siempre positiva).
     * @param int $adminId El ID del administrador que realiza la acción.
     * @return void
     */
    public function __construct(int $userId, int $amount, int $adminId)
    {
        $this->userId = $userId;
        $this->amount = abs($amount); // Aseguramos que la cantidad sea siempre positiva
        $this->adminId = $adminId;
    }

    /**
     * Ejecuta el job (lógica principal).
     *
     * @return void
     */
    public function handle(): void
    {
        // Usamos una transacción de base de datos para asegurar que ambas operaciones
        // (actualización de saldo y creación de transacción) se completen o fallen juntas.
        DB::transaction(function () {
            // 1. Encontrar el usuario o fallar
            $user = User::find($this->userId);

            if (!$user) {
                // Usamos Log::warning en lugar de Log::error ya que el Job se reintentará.
                Log::warning("AssignPointsJob falló: Usuario ID {$this->userId} no encontrado.");
                // Lanzamos una excepción para que el Job pueda ser marcado como fallido o reintentado
                throw new \Exception("Usuario no encontrado.");
            }

            // 2. Actualizar el saldo del usuario
            $newBalance = $user->current_balance + $this->amount;

            $user->update([
                'current_balance' => $newBalance,
            ]);

            // 3. Crear el registro de la transacción
            Transaction::create([
                'user_id' => $this->userId,
                'admin_id' => $this->adminId,
                'type' => 'CREDIT', // Tipo fijo para este Job
                'amount' => $this->amount,
                'previous_balance' => $user->current_balance, // El balance ANTES de esta transacción
                'new_balance' => $newBalance, // El balance DESPUÉS de esta transacción
                'status' => 'completed', // Marcamos como completada ya que se hizo dentro de la transacción DB
                'description' => "Asignación de puntos por administrador ({$this->adminId})",
            ]);

            Log::info("Asignación de puntos completada para User ID: {$this->userId}. Cantidad: {$this->amount}. Nuevo saldo: {$newBalance}");

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
        // En un entorno de producción, aquí se enviaría una notificación de error (ej. Slack, correo)
        Log::error("AssignPointsJob falló después de {$this->tries} intentos para User ID {$this->userId}: " . $exception->getMessage());

        // Opcional: Si deseas guardar un registro de la transacción fallida antes de eliminar el job
        try {
            Transaction::create([
                'user_id' => $this->userId,
                'admin_id' => $this->adminId,
                'type' => 'CREDIT',
                'amount' => $this->amount,
                'previous_balance' => User::find($this->userId)->current_balance ?? 0,
                'new_balance' => User::find($this->userId)->current_balance ?? 0,
                'status' => 'failed',
                'description' => "Fallo en asignación de puntos. Error: " . substr($exception->getMessage(), 0, 191), // Truncar mensaje
            ]);
        } catch (\Exception $e) {
            Log::error("Fallo al crear registro de transacción fallida: " . $e->getMessage());
        }
    }
}