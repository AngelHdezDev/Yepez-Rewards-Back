<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Nombre de la tabla de la base de datos.
     * @var string
     */
    protected $table = 'clients';

    /**
     * Los atributos que son asignables masivamente.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'branch_id', // Para ligar al cliente a la sucursal que lo creó
    ];

    /**
     * Los atributos que deben estar ocultos para la serialización.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * La sucursal (usuario) que registró a este cliente.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        // Asume que la sucursal que lo registró es un registro en la tabla 'users'
        return $this->belongsTo(User::class, 'branch_id');
    }
}