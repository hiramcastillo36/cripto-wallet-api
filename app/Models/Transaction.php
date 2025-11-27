<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'sender_id',
        'receiver_id',
        'cryptocurrency_id',
        'amount',
        'fee_amount',
        'usd_value_at_time',
        'status',
        'status_reason',
        'requires_approval',
        'approved_by_admin_id',
        'approved_at',
        'blocked_by_admin_id',
        'blocked_at',
        'blocked_reason',
        'completed_at',
        'reference_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'blocked_at' => 'datetime',
            'completed_at' => 'datetime',
            'requires_approval' => 'boolean',
            'metadata' => 'json',
        ];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function cryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class);
    }

    public function approvedByAdmin()
    {
        return $this->belongsTo(User::class, 'approved_by_admin_id');
    }

    public function blockedByAdmin()
    {
        return $this->belongsTo(User::class, 'blocked_by_admin_id');
    }
}
