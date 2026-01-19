<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// --- IMPORTACIONES PARA ACTIVITY LOG ---
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// ---------------------------------------

class Ticket extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_number',
        'amount',
        'points_earned',
        'issue_date',
        'user_id',
        'branch_id',
    ];

    /**
     * Configuración de los logs de actividad
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()          // Registra cambios en montos, puntos, fechas y relaciones
            ->logOnlyDirty()         // Evita duplicados si no hay cambios reales
            ->dontSubmitEmptyLogs()
            ->useLogName('tickets'); // Categoría específica para tickets
    }

    /**
     * Descripción personalizada de los eventos
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $traducciones = [
            'created' => 'registrado',
            'updated' => 'modificado',
            'deleted' => 'anulado',
        ];

        $evento = $traducciones[$eventName] ?? $eventName;

        return "Ticket #{$this->ticket_number} por un monto de \${$this->amount} ha sido {$evento}";
    }

    /**
     * Obtener el usuario (cliente) al que pertenece el Ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener la sucursal que procesó el Ticket.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}