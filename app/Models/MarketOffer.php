<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketOffer extends Model
{
    protected $fillable = [
        'user_id',
        'cryptocurrency_id',
        'type',
        'amount',
        'remaining_amount',
        'price_usd',
        'total_value_usd',
        'status',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class);
    }

    public function marketTrades()
    {
        return $this->hasMany(MarketTrade::class);
    }
}
