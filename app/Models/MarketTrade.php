<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketTrade extends Model
{
    protected $fillable = [
        'market_offer_id',
        'buyer_id',
        'seller_id',
        'cryptocurrency_id',
        'amount',
        'price_usd',
        'total_value_usd',
        'fee_amount',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function marketOffer()
    {
        return $this->belongsTo(MarketOffer::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function cryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class);
    }
}
