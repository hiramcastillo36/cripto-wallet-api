<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class PublicController extends Controller
{
    /**
     * Obtener los últimos 3 movimientos del sistema
     */
    public function latestTransactions(): JsonResponse
    {
        $transactions = Transaction::with(['sender', 'receiver', 'cryptocurrency'])
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => in_array($transaction->type, ['market_sell', 'transfer']) ? 'send' : 'receive',
                    'cryptocurrency' => [
                        'id' => $transaction->cryptocurrency->id,
                        'symbol' => $transaction->cryptocurrency->symbol,
                        'name' => $transaction->cryptocurrency->name,
                    ],
                    'amount' => (string) $transaction->amount,
                    'usd_value' => $transaction->usd_value_at_time,
                    'from' => $transaction->sender?->name ?? 'Unknown',
                    'to' => $transaction->receiver?->name ?? 'External',
                    'status' => $transaction->status,
                    'completed_at' => $transaction->completed_at?->toIso8601String(),
                ];
            });

        return response()->json([
            'message' => 'Últimos movimientos del sistema',
            'transactions' => $transactions,
            'count' => $transactions->count(),
        ], 200);
    }
}
