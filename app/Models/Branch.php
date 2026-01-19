<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// --- IMPORTACIONES PARA ACTIVITY LOG ---
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// ---------------------------------------

class Branch extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Los atributos que son asignables masivamente.
     */
    protected $fillable = [
        'name',
        'city', 
        'is_active',
        'address',
        'phone',
    ];

    /**
     * Configuración de los logs de actividad
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()         // Monitorea name, city, address, phone, etc.
            ->logOnlyDirty()        // Solo guarda si algo cambió
            ->dontSubmitEmptyLogs() 
            ->useLogName('sucursales'); // Agrupador para los logs de sucursales
    }

    /**
     * Personalización del mensaje del log en español
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $traducciones = [
            'created'   => 'creada',
            'updated'   => 'actualizada',
            'deleted'   => 'eliminada',
        ];

        $evento = $traducciones[$eventName] ?? $eventName;

        return "La sucursal '{$this->name}' en {$this->city} ha sido {$evento}";
    }

    /**
     * Relación: Los usuarios (empleados/admins) que pertenecen a esta sucursal.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Relación: Los tickets de compra registrados por esta sucursal.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}