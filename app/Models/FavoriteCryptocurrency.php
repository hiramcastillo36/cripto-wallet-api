<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteCryptocurrency extends Model
{
    protected $fillable = [
        'user_id',
        'cryptocurrency_id',
        'order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class);
    }
}
