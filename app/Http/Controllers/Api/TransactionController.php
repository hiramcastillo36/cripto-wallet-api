<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    /**
     * Obtener todas las transacciones
     */
    public function index(): JsonResponse
    {
        try {
            $transactions = Transaction::with(['sender', 'receiver', 'cryptocurrency'])
                ->get()
                ->map(fn ($transaction) => [
                    'id' => $transaction->id,
                    'uuid' => $transaction->uuid,
                    'type' => $transaction->type,
                    'sender' => $transaction->sender ? [
                        'id' => $transaction->sender->id,
                        'name' => $transaction->sender->name,
                        'email' => $transaction->sender->email,
                    ] : null,
                    'receiver' => $transaction->receiver ? [
                        'id' => $transaction->receiver->id,
                        'name' => $transaction->receiver->name,
                        'email' => $transaction->receiver->email,
                    ] : null,
                    'cryptocurrency' => [
                        'id' => $transaction->cryptocurrency->id,
                        'symbol' => $transaction->cryptocurrency->symbol,
                        'name' => $transaction->cryptocurrency->name,
                    ],
                    'amount' => $transaction->amount,
                    'fee_amount' => $transaction->fee_amount,
                    'usd_value_at_time' => $transaction->usd_value_at_time,
                    'status' => $transaction->status,
                    'status_reason' => $transaction->status_reason,
                    'created_at' => $transaction->created_at,
                    'completed_at' => $transaction->completed_at,
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Lista de transacciones',
                'data' => [
                    'transactions' => $transactions,
                    'total' => $transactions->count(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving transactions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener detalles de una transacción específica
     */
    public function show(Transaction $transaction): JsonResponse
    {
        try {
            $transaction->load(['sender', 'receiver', 'cryptocurrency', 'approvedByAdmin', 'blockedByAdmin']);

            return response()->json([
                'success' => true,
                'message' => 'Detalles de la transacción',
                'data' => [
                    'transaction' => [
                        'id' => $transaction->id,
                        'uuid' => $transaction->uuid,
                        'type' => $transaction->type,
                        'sender' => $transaction->sender ? [
                            'id' => $transaction->sender->id,
                            'name' => $transaction->sender->name,
                            'email' => $transaction->sender->email,
                        ] : null,
                        'receiver' => $transaction->receiver ? [
                            'id' => $transaction->receiver->id,
                            'name' => $transaction->receiver->name,
                            'email' => $transaction->receiver->email,
                        ] : null,
                        'cryptocurrency' => [
                            'id' => $transaction->cryptocurrency->id,
                            'symbol' => $transaction->cryptocurrency->symbol,
                            'name' => $transaction->cryptocurrency->name,
                        ],
                        'amount' => $transaction->amount,
                        'fee_amount' => $transaction->fee_amount,
                        'usd_value_at_time' => $transaction->usd_value_at_time,
                        'status' => $transaction->status,
                        'status_reason' => $transaction->status_reason,
                        'requires_approval' => $transaction->requires_approval,
                        'approved_at' => $transaction->approved_at,
                        'approved_by_admin' => $transaction->approvedByAdmin ? [
                            'id' => $transaction->approvedByAdmin->id,
                            'name' => $transaction->approvedByAdmin->name,
                        ] : null,
                        'blocked_at' => $transaction->blocked_at,
                        'blocked_reason' => $transaction->blocked_reason,
                        'blocked_by_admin' => $transaction->blockedByAdmin ? [
                            'id' => $transaction->blockedByAdmin->id,
                            'name' => $transaction->blockedByAdmin->name,
                        ] : null,
                        'completed_at' => $transaction->completed_at,
                        'created_at' => $transaction->created_at,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
