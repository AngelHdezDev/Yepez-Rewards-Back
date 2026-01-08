<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Redemption extends Model
{
    use HasFactory;

    // Asume que la tabla se llama 'redemptions'
    protected $fillable = [
        'user_id', 
        'reward_id',
        'points_cost',
        'reward_name', // <<< ¡ESTE ES EL CAMPO QUE FALTABA!
        'redemption_code',
        'redeemed_at',
        'status',
        // 'branch_id', // Si usas branch_id
    ];
    
    /**
     * Define la relación: Un canje pertenece a un cliente (User).
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define la relación: Un canje pertenece a un premio (Reward).
     *
     * @return BelongsTo
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }
}