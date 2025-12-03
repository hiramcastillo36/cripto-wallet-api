<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Purchase extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'cryptocurrency_id',
        'amount_crypto',
        'amount_usd',
        'fee_usd',
        'total_usd',
        'stripe_payment_intent_id',
        'stripe_payment_status',
        'payment_method',
        'status',
        'completed_at',
        'metadata',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'metadata' => 'json',
            'uuid' => 'string',
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
