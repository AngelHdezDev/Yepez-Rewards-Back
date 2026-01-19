<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// --- IMPORTACIONES PARA ACTIVITY LOG ---
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// ---------------------------------------

class Transaction extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'description',
        'status',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'type' => 'string',
        'status' => 'string',
    ];

    /**
     * Configuración de Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()           // Registra montos, tipos y estados
            ->logOnlyDirty()          // Solo registra si hay cambios reales
            ->dontSubmitEmptyLogs()
            ->useLogName('transactions');
    }

    /**
     * Descripción dinámica para entender el flujo de puntos
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $tipo = ($this->type === 'earn' || $this->type === 'credit') ? 'Abono' : 'Cargo';
        
        $traducciones = [
            'created' => 'registrado',
            'updated' => 'modificado',
            'deleted' => 'eliminado permanentemente',
        ];

        $evento = $traducciones[$eventName] ?? $eventName;

        return "{$tipo} de {$this->amount} puntos ha sido {$evento}. Motivo: {$this->description}";
    }

    /**
     * Relación: Una transacción pertenece a un usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}