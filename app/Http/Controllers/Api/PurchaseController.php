<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\Cryptocurrency;
use App\Models\WalletBalance;
use App\Mail\PurchaseShipped;
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
}
