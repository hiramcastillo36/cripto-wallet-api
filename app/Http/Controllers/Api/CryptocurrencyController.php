<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cryptocurrency;
use Illuminate\Http\JsonResponse;

class CryptocurrencyController extends Controller
{
    /**
     * Obtener todas las criptomonedas activas
     */
    public function index(): JsonResponse
    {
        $cryptocurrencies = Cryptocurrency::where('is_active', true)
            ->get()
            ->map(fn ($crypto) => [
                'id' => $crypto->id,
                'symbol' => $crypto->symbol,
                'name' => $crypto->name,
                'description' => $crypto->description,
                'decimals' => $crypto->decimals,
                'min_purchase_amount' => $crypto->min_purchase_amount,
                'max_purchase_amount' => $crypto->max_purchase_amount,
                'purchase_fee_percentage' => $crypto->purchase_fee_percentage,
                'withdrawal_fee_percentage' => $crypto->withdrawal_fee_percentage,
                'market_trading_enabled' => $crypto->market_trading_enabled,
            ]);

        return response()->json([
            'message' => 'Lista de criptomonedas',
            'total' => $cryptocurrencies->count(),
            'cryptocurrencies' => $cryptocurrencies,
        ], 200);
    }

    /**
     * Obtener detalles de una criptomoneda específica
     */
    public function show(Cryptocurrency $cryptocurrency): JsonResponse
    {
        if (!$cryptocurrency->is_active) {
            return response()->json([
                'message' => 'Esta criptomoneda no está disponible',
            ], 404);
        }

        return response()->json([
            'message' => 'Detalles de la criptomoneda',
            'cryptocurrency' => [
                'id' => $cryptocurrency->id,
                'symbol' => $cryptocurrency->symbol,
                'name' => $cryptocurrency->name,
                'coinbase_id' => $cryptocurrency->coinbase_id,
                'icon_url' => $cryptocurrency->icon_url,
                'description' => $cryptocurrency->description,
                'decimals' => $cryptocurrency->decimals,
                'min_purchase_amount' => $cryptocurrency->min_purchase_amount,
                'max_purchase_amount' => $cryptocurrency->max_purchase_amount,
                'purchase_fee_percentage' => $cryptocurrency->purchase_fee_percentage,
                'withdrawal_fee_percentage' => $cryptocurrency->withdrawal_fee_percentage,
                'market_trading_enabled' => $cryptocurrency->market_trading_enabled,
            ],
        ], 200);
    }
}
