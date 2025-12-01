<?php

namespace App\Models;

// Importaciones necesarias
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute; // Para el Accessor/Mutator

// Importamos los nuevos modelos para las relaciones
use App\Models\Transaction;
use App\Models\Ticket;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_balance', // <-- Agregado para el campo de puntos
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     * * ¡CORREGIDO! SE ELIMINA LA DECLARACIÓN EXPLÍCITA DE TIPO (array) 
     * para evitar el error: "Type of App\Models\User::$casts must not be defined"
     *
     * @var array<string, string>
     */
    protected $casts = [ // <-- ¡SIN el "array" antes de $casts!
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    // ===============================================
    // RELACIONES
    // ===============================================

    /**
     * Obtiene todas las transacciones de puntos del usuario (CREDIT/DEBIT).
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected function currentPoints(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->transactions()
                ->where('status', 'COMPLETED') // Solo transacciones completadas
                ->sum(DB::raw('CASE 
                                  WHEN type = "CREDIT" THEN amount 
                                  WHEN type = "DEBIT" THEN -amount 
                                  ELSE 0 
                              END')),
        )->shouldCache(); // Recomiendo usar shouldCache() para evitar calcular el saldo en cada petición
    }

    /**
     * Relación con los canjes de premios realizados por el usuario.
     */
    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
    }

    /**
     * Obtiene todos los tickets de compra registrados por la sucursal (si este User es una sucursal).
     */
    public function registeredTickets(): HasMany
    {
        // La llave foránea es branch_id
        return $this->hasMany(Ticket::class, 'branch_id');
    }

    // ===============================================
    // ACCESORES
    // ===============================================

    /**
     * Accessor para el saldo de puntos (current_balance).
     * Usamos Attribute::make para Laravel 10/11.
     * Esto expone $user->balance
     */
    protected function balance(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => (int) $attributes['current_balance'],
            set: fn($value) => (int) $value,
        );
    }
}