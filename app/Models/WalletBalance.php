<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletBalance extends Model
{
    protected $fillable = [
        'wallet_id',
        'cryptocurrency_id',
        'balance',
        'locked_balance',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function cryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class);
    }
}
