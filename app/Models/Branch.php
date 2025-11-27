<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
    ];

    /**
     * Relación: Los usuarios (empleados/admins) que pertenecen a esta sucursal.
     * Asume que el modelo User tiene un campo 'branch_id'.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Relación: Los tickets de compra registrados por esta sucursal.
     * Una sucursal puede emitir muchos tickets.
     */
    public function tickets(): HasMany
    {
        // Asumimos que el modelo Ticket tiene un campo 'branch_id'
        return $this->hasMany(Ticket::class);
    }
}