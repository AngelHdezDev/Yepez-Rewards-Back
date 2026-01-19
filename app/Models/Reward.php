<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// --- IMPORTACIONES PARA ACTIVITY LOG ---
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// ---------------------------------------

class Reward extends Model
{
    use HasFactory, LogsActivity; // Añadimos el Trait aquí

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'cost_points',
        'code',
        'stock',
        'is_active',
    ];

    /**
     * Configuración de los logs de actividad
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Registra todos los campos en $fillable
            ->logOnlyDirty() // Solo registra los campos que cambiaron
            ->dontSubmitEmptyLogs() // No guarda log si no hubo cambios reales
            ->useLogName('premios'); // Nombre opcional para agrupar logs
    }

    /**
     * Personalización del mensaje de descripción
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $traducciones = [
            'created'   => 'creado',
            'updated'   => 'actualizado',
            'deleted'   => 'eliminado',
        ];

        $evento = $traducciones[$eventName] ?? $eventName;

        // Ejemplo: "El premio 'Cena Gratis' ha sido actualizado"
        return "El premio '{$this->name}' ha sido {$evento}";
    }

    /**
     * Define la relación: Un premio puede tener muchos canjes (redemptions).
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }
}