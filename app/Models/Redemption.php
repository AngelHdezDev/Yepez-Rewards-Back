<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Redemption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'reward_id',
        'reward_name',
        'points_cost',
        'redemption_code',
        'status',
    ];

    /**
     * El usuario que realizó el canje.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * El premio que fue canjeado.
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }
}