<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

// --- IMPORTACIONES PARA ACTIVITY LOG ---
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// ---------------------------------------

use App\Models\Transaction;
use App\Models\Ticket;

class User extends Authenticatable implements MustVerifyEmail
{
    // Añadimos LogsActivity a los traits existentes
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'current_balance',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ===============================================
    // CONFIGURACIÓN DE LOGS (SPATIE)
    // ===============================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'current_balance', 'branch_id']) // Campos a vigilar
            ->logOnlyDirty()                                            // Solo si hubo cambios reales
            ->dontSubmitEmptyLogs()                                     // Evita filas vacías en la DB
            ->useLogName('users');                                      // Etiqueta del log
    }

    /**
     * Personaliza el mensaje del log (opcional)
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $eventos = [
            'created' => 'creada',
            'updated' => 'actualizada',
            'deleted' => 'eliminada',
            'restored' => 'restaurada',
        ];

        // Buscamos la traducción o usamos el nombre original si no existe
        $eventoTraducido = $eventos[$eventName] ?? $eventName;

        return "La sucursal {$this->name} ha sido {$eventoTraducido}";
    }
    // ===============================================
    // RELACIONES
    // ===============================================

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected function currentPoints(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->transactions()
                ->where('status', 'COMPLETED')
                ->sum(DB::raw('CASE 
                                  WHEN type = "CREDIT" THEN amount 
                                  WHEN type = "DEBIT" THEN -amount 
                                  ELSE 0 
                              END')),
        )->shouldCache();
    }

    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
    }

    public function registeredTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'branch_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // ===============================================
    // ACCESORES
    // ===============================================

    protected function balance(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => (int) ($attributes['current_balance'] ?? 0),
            set: fn($value) => (int) $value,
        );
    }
}