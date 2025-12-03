<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\SellRequest;
use App\Http\Requests\SendRequest;
use App\Models\Purchase;
use App\Models\Cryptocurrency;
use App\Models\WalletBalance;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Mail\PurchaseShipped;
use App\Mail\TransactionNotification;
use App\Services\TransactionPdfGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    /**
     * Comprar criptomonedas
     */
    public function buy(PurchaseRequest $request): JsonResponse
    {
        $user = auth()->user();

        // Validar que el usuario no esté bloqueado
        if ($user->is_blocked) {
            return response()->json([
                'message' => 'Tu cuenta ha sido bloqueada',
                'reason' => $user->blocked_reason,
            ], 403);
        }

        // Validar límite diario de transacciones
        $todayAmount = Purchase::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('total_usd');

        try {
            $purchase = DB::transaction(function () use ($request, $user) {
                // Crear el registro de compra
                $purchase = Purchase::create([
                    'user_id' => $user->id,
                    'cryptocurrency_id' => $request->cryptocurrency_id,
                    'amount_crypto' => $request->amount_crypto,
                    'amount_usd' => $request->amount_usd,
                    'fee_usd' => $this->calculateFee($request->amount_usd),
                    'total_usd' => $request->amount_usd + $this->calculateFee($request->amount_usd),
                    'payment_method' => $request->payment_method,
                    'status' => 'pending',
                    'stripe_payment_status' => 'pending',
                ]);

                // Asegurar que el usuario tenga wallet
                $wallet = $user->wallet()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['wallet_address' => $this->generateWalletAddress()]
                );

                // Actualizar o crear WalletBalance
                WalletBalance::updateOrCreate(
                    [
                        'wallet_id' => $wallet->id,
                        'cryptocurrency_id' => $request->cryptocurrency_id,
                    ],
                    [
                        'balance' => DB::raw('balance + ' . $request->amount_crypto),
                    ]
                );

                $walletBalance = WalletBalance::where('wallet_id', $wallet->id)
                    ->where('cryptocurrency_id', $request->cryptocurrency_id)
                    ->first();

                return $purchase;
            });

            // ENVIAR CORREO DESPUÉS DE LA TRANSACCIÓN (NO DEBE BLOQUEAR LA RESPUESTA)
            try {
                \Log::info('Intentando enviar correo a: ' . $user->email);

                Mail::to($user->email)->send(new PurchaseShipped($purchase));

                \Log::info('Correo enviado exitosamente a: ' . $user->email);
            } catch (\Exception $e) {
                // SOLO LOGUEA - NO RETORNES ERROR
                // La compra ya se procesó exitosamente
                \Log::error('Error al enviar correo de confirmación: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
            }

            // SIEMPRE RETORNA ÉXITO SI LA COMPRA SE PROCESÓ
            return response()->json([
                'message' => 'Compra iniciada exitosamente',
                'purchase' => [
                    'id' => $purchase->id,
                    'cryptocurrency_id' => $purchase->cryptocurrency_id,
                    'amount_crypto' => $purchase->amount_crypto,
                    'amount_usd' => $purchase->amount_usd,
                    'fee_usd' => $purchase->fee_usd,
                    'total_usd' => $purchase->total_usd,
                    'payment_method' => $purchase->payment_method,
                    'status' => $purchase->status,
                    'stripe_payment_status' => $purchase->stripe_payment_status,
                    'created_at' => $purchase->created_at,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la compra',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener historial de compras del usuario
     */
    public function history(): JsonResponse
    {
        $user = auth()->user();

        $purchases = Purchase::where('user_id', $user->id)
            ->with('cryptocurrency')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($purchase) => [
                'id' => $purchase->id,
                'cryptocurrency' => $purchase->cryptocurrency->symbol ?? 'N/A',
                'amount_crypto' => $purchase->amount_crypto,
                'amount_usd' => $purchase->amount_usd,
                'fee_usd' => $purchase->fee_usd,
                'total_usd' => $purchase->total_usd,
                'payment_method' => $purchase->payment_method,
                'status' => $purchase->status,
                'created_at' => $purchase->created_at,
            ]);

        return response()->json([
            'message' => 'Historial de compras',
            'total' => $purchases->count(),
            'purchases' => $purchases,
        ], 200);
    }

    /**
     * Obtener detalles de una compra específica
     */
    public function show(Purchase $purchase): JsonResponse
    {
        $user = auth()->user();

        // Verificar que la compra pertenece al usuario
        if ($purchase->user_id !== $user->id) {
            return response()->json([
                'message' => 'No tienes permiso para ver esta compra',
            ], 403);
        }

        return response()->json([
            'message' => 'Detalles de la compra',
            'purchase' => [
                'id' => $purchase->id,
                'cryptocurrency' => $purchase->cryptocurrency->symbol ?? 'N/A',
                'amount_crypto' => $purchase->amount_crypto,
                'amount_usd' => $purchase->amount_usd,
                'fee_usd' => $purchase->fee_usd,
                'total_usd' => $purchase->total_usd,
                'payment_method' => $purchase->payment_method,
                'status' => $purchase->status,
                'stripe_payment_status' => $purchase->stripe_payment_status,
                'completed_at' => $purchase->completed_at,
                'created_at' => $purchase->created_at,
            ],
        ], 200);
    }

    /**
     * Calcular fee de transacción (2% por defecto)
     */
    private function calculateFee(float $amount): float
    {
        return $amount * 0.02;
    }

    /**
     * Generar dirección de wallet
     */
    private function generateWalletAddress(): string
    {
        return '0x' . strtoupper(bin2hex(random_bytes(20)));
    }

    /**
     * Obtener balance de criptomonedas del usuario
     */
    public function balance(): JsonResponse
    {
        $user = auth()->user();

        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json([
                'message' => 'El usuario no tiene wallet',
                'balances' => [],
            ], 200);
        }

        $balances = WalletBalance::where('wallet_id', $wallet->id)
            ->with('cryptocurrency')
            ->get()
            ->map(fn ($walletBalance) => [
                'cryptocurrency_id' => $walletBalance->cryptocurrency_id,
                'symbol' => $walletBalance->cryptocurrency->symbol,
                'name' => $walletBalance->cryptocurrency->name,
                'balance' => $walletBalance->balance,
                'locked_balance' => $walletBalance->locked_balance,
                'available_balance' => $walletBalance->balance - $walletBalance->locked_balance,
                'last_transaction_at' => $walletBalance->last_transaction_at,
            ]);

        return response()->json([
            'message' => 'Balance de criptomonedas',
            'wallet_address' => $wallet->wallet_address,
            'balances' => $balances,
            'total_balance_count' => $balances->count(),
        ], 200);
    }

    /**
     * Obtener historial de transacciones del usuario
     */
    public function transactions(): JsonResponse
    {
        $user = auth()->user();

        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json([
                'message' => 'Historial de transacciones',
                'transactions' => [],
                'total_count' => 0,
            ], 200);
        }

        $transactions = Transaction::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })
            ->with(['cryptocurrency'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => in_array($transaction->type, ['market_sell', 'transfer']) ? 'send' : 'receive',
                    'cryptocurrency_id' => $transaction->cryptocurrency_id,
                    'symbol' => $transaction->cryptocurrency->symbol ?? 'N/A',
                    'amount' => (string) $transaction->amount,
                    'usd_value' => $transaction->usd_value_at_time,
                    'from_address' => $transaction->sender_id ? 'User #' . $transaction->sender_id : null,
                    'to_address' => $transaction->receiver_id ? 'User #' . $transaction->receiver_id : null,
                    'status' => $transaction->status,
                    'transaction_hash' => $transaction->reference_id,
                    'created_at' => $transaction->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'message' => 'Historial de transacciones',
            'transactions' => $transactions,
            'total_count' => $transactions->count(),
        ], 200);
    }

    /**
     * Enviar criptomonedas a otra dirección
     */
    public function send(SendRequest $request): JsonResponse
    {
        $user = auth()->user();

        // Validar que el usuario no esté bloqueado
        if ($user->is_blocked) {
            return response()->json([
                'message' => 'Tu cuenta ha sido bloqueada',
                'reason' => $user->blocked_reason,
            ], 403);
        }

        try {
            $transaction = DB::transaction(function () use ($request, $user) {
                // Obtener wallet del usuario remitente
                $wallet = $user->wallet;

                if (!$wallet) {
                    throw new \Exception('El usuario no tiene wallet');
                }

                // Obtener balance actual del remitente
                $walletBalance = WalletBalance::where('wallet_id', $wallet->id)
                    ->where('cryptocurrency_id', $request->cryptocurrency_id)
                    ->first();

                if (!$walletBalance) {
                    throw new \Exception('El usuario no tiene balance de esta criptomoneda');
                }

                // Validar que tiene suficiente balance
                if ($walletBalance->balance < $request->amount_crypto) {
                    throw new \Exception(
                        'Balance insuficiente. Balance: ' . $walletBalance->balance .
                        ', Solicitado: ' . $request->amount_crypto
                    );
                }

                // Verificar si el receptor es un usuario del sistema
                $receiverWallet = Wallet::where('wallet_address', $request->to_address)->first();
                $receiverId = $receiverWallet?->user_id;

                // Crear transacción de envío
                $transaction = Transaction::create([
                    'type' => 'transfer',
                    'sender_id' => $user->id,
                    'receiver_id' => $receiverId,
                    'cryptocurrency_id' => $request->cryptocurrency_id,
                    'amount' => $request->amount_crypto,
                    'status' => 'completed',
                    'completed_at' => now(),
                    'reference_id' => $request->to_address,
                    'metadata' => json_encode([
                        'to_address' => $request->to_address,
                    ]),
                ]);

                // Restar balance del remitente
                $walletBalance->balance -= $request->amount_crypto;
                $walletBalance->save();

                // Si el receptor es usuario del sistema, sumar a su balance
                if ($receiverWallet) {
                    $receiverBalance = WalletBalance::where('wallet_id', $receiverWallet->id)
                        ->where('cryptocurrency_id', $request->cryptocurrency_id)
                        ->first();

                    if ($receiverBalance) {
                        $receiverBalance->balance += $request->amount_crypto;
                        $receiverBalance->save();
                    } else {
                        // Si no existe balance para esa criptomoneda, crearlo
                        WalletBalance::create([
                            'wallet_id' => $receiverWallet->id,
                            'cryptocurrency_id' => $request->cryptocurrency_id,
                            'balance' => $request->amount_crypto,
                        ]);
                    }
                }

                return $transaction;
            });

            // Intentar enviar correo con PDF al receptor si es usuario del sistema
            try {
                $receiverUser = Wallet::where('wallet_address', $request->to_address)
                    ->first()
                    ?->user;

                if ($receiverUser) {
                    $transaction->load('cryptocurrency', 'sender');
                    $pdfGenerator = new TransactionPdfGenerator();
                    $pdfPath = $pdfGenerator->generate($transaction);

                    Mail::to($receiverUser->email)->send(
                        new TransactionNotification($transaction, $receiverUser, $pdfPath)
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Error al enviar correo de transacción: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Transacción de envío iniciada',
                'id' => $transaction->id,
                'cryptocurrency_id' => $transaction->cryptocurrency_id,
                'amount_crypto' => $transaction->amount,
                'to_address' => $request->to_address,
                'status' => $transaction->status,
                'transaction_hash' => $transaction->reference_id,
                'created_at' => $transaction->created_at->toIso8601String(),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar el envío',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Vender criptomonedas
     */
    public function sell(SellRequest $request): JsonResponse
    {
        $user = auth()->user();

        // Validar que el usuario no esté bloqueado
        if ($user->is_blocked) {
            return response()->json([
                'message' => 'Tu cuenta ha sido bloqueada',
                'reason' => $user->blocked_reason,
            ], 403);
        }

        try {
            $transaction = DB::transaction(function () use ($request, $user) {
                // Obtener wallet del usuario
                $wallet = $user->wallet;

                if (!$wallet) {
                    throw new \Exception('El usuario no tiene wallet');
                }

                // Obtener balance actual
                $walletBalance = WalletBalance::where('wallet_id', $wallet->id)
                    ->where('cryptocurrency_id', $request->cryptocurrency_id)
                    ->first();

                if (!$walletBalance) {
                    throw new \Exception('El usuario no tiene balance de esta criptomoneda');
                }

                // Validar que tiene suficiente balance disponible
                $availableBalance = $walletBalance->balance - $walletBalance->locked_balance;

                if ($availableBalance < $request->amount_crypto) {
                    throw new \Exception(
                        'Balance insuficiente. Disponible: ' . $availableBalance .
                        ', Solicitado: ' . $request->amount_crypto
                    );
                }

                // Calcular fee (2%)
                $fee = $request->price_usd * $request->amount_crypto * 0.02;
                $totalUsd = ($request->price_usd * $request->amount_crypto) - $fee;

                // Crear transacción de venta
                $transaction = Transaction::create([
                    'type' => 'market_sell',
                    'sender_id' => $user->id,
                    'cryptocurrency_id' => $request->cryptocurrency_id,
                    'amount' => $request->amount_crypto,
                    'fee_amount' => $fee,
                    'usd_value_at_time' => $totalUsd,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // Actualizar balance (restar criptomonedas)
                $walletBalance->balance -= $request->amount_crypto;
                $walletBalance->save();

                return $transaction;
            });

            return response()->json([
                'message' => 'Venta realizada exitosamente',
                'transaction' => [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'cryptocurrency_id' => $transaction->cryptocurrency_id,
                    'amount_crypto' => $transaction->amount,
                    'price_usd_per_unit' => $request->price_usd,
                    'total_usd' => $transaction->usd_value_at_time,
                    'fee_usd' => $transaction->fee_amount,
                    'status' => $transaction->status,
                    'completed_at' => $transaction->completed_at,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la venta',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
