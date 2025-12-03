<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceAlert extends Model
{
    protected $fillable = [
        'user_id',
        'cryptocurrency_id',
        'condition',
        'target_price_usd',
        'is_active',
        'triggered_at',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'triggered_at' => 'datetime',
            'notified_at' => 'datetime',
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
}
