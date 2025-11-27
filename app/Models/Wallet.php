<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_address',
        'total_value_usd',
        'frozen_at',
        'frozen_reason',
    ];

    protected function casts(): array
    {
        return [
            'frozen_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function balances()
    {
        return $this->hasMany(WalletBalance::class);
    }
}
