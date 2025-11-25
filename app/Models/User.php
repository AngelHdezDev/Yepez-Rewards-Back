<?php

namespace App\Models;

// Importaciones necesarias para Sanctum y Spatie
use Illuminate\Database\Eloquent\Casts\Attribute; 
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <-- ESTA LÍNEA ES CRÍTICA PARA EL ERROR
use Spatie\Permission\Traits\HasRoles; // <-- Necesario para el Módulo 1 (Roles)
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Transaction;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles; // <-- DEBE ESTAR AQUÍ

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>  
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'current_balance' => 'integer',
    ];


    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected function balance(): Attribute
    {
        return Attribute::get(function () {
            // Carga las transacciones del usuario.
            // Suma los 'CREDIT' y resta los 'DEBIT'.
            $credits = $this->transactions()
                ->where('type', 'CREDIT')
                ->sum('amount');

            $debits = $this->transactions()
                ->where('type', 'DEBIT')
                ->sum('amount');

            return $credits - $debits;
        });
    }

    protected $appends = ['balance'];
}
