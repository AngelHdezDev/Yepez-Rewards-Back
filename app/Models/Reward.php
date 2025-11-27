<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     * Corresponden a las columnas que añadimos en la migración anterior.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'cost_points',
        'code',
        'stock',
        'is_active',
    ];

    /**
     * Define la relación: Un premio puede tener muchos canjes (redemptions).
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }
}