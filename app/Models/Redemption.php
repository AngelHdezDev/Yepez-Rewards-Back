<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// --- IMPORTACIONES PARA ACTIVITY LOG ---
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// ---------------------------------------

class Redemption extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'user_id', 
        'reward_id',
        'points_cost',
        'reward_name',
        'redemption_code',
        'redeemed_at',
        'status',
        // 'branch_id',
    ];
    
    /**
     * Configuración de los logs de actividad
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()           // Registra cambios en estados, costos y fechas de redención
            ->logOnlyDirty()          // Solo registra si hay cambios (ej. de "pendiente" a "entregado")
            ->dontSubmitEmptyLogs()
            ->useLogName('redemptions'); // Categoría específica para canjes
    }

    /**
     * Descripción personalizada con contexto de puntos y premios
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $traducciones = [
            'created' => 'solicitado',
            'updated' => 'actualizado',
            'deleted' => 'cancelado',
        ];

        $evento = $traducciones[$eventName] ?? $eventName;

        return "Canje de '{$this->reward_name}' ({$this->points_cost} pts) ha sido {$evento}";
    }

    /**
     * Define la relación: Un canje pertenece a un cliente (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define la relación: Un canje pertenece a un premio (Reward).
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }
}