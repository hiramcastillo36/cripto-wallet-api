<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cryptocurrency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CryptocurrencyController extends Controller
{
    /**
     * Obtener todas las criptomonedas activas
     */
    public function index(): JsonResponse
    {
        try {
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
                'success' => true,
                'message' => 'Lista de criptomonedas',
                'data' => [
                    'cryptocurrencies' => $cryptocurrencies,
                    'total' => $cryptocurrencies->count(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving cryptocurrencies',
                'error' => $e->getMessage(),
            ], 500);
        }
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

    /**
     * Crear una nueva criptomoneda
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'symbol' => 'required|string|unique:cryptocurrencies,symbol',
            'name' => 'required|string|unique:cryptocurrencies,name',
            'description' => 'nullable|string',
            'decimals' => 'required|integer|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_purchase_amount' => 'nullable|numeric|min:0',
            'purchase_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'withdrawal_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'market_trading_enabled' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $cryptocurrency = Cryptocurrency::create([
                'symbol' => $request->symbol,
                'name' => $request->name,
                'description' => $request->description,
                'decimals' => $request->decimals,
                'min_purchase_amount' => $request->min_purchase_amount,
                'max_purchase_amount' => $request->max_purchase_amount,
                'purchase_fee_percentage' => $request->purchase_fee_percentage,
                'withdrawal_fee_percentage' => $request->withdrawal_fee_percentage,
                'market_trading_enabled' => $request->market_trading_enabled ?? false,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Criptomoneda creada exitosamente',
                'data' => [
                    'cryptocurrency' => [
                        'id' => $cryptocurrency->id,
                        'symbol' => $cryptocurrency->symbol,
                        'name' => $cryptocurrency->name,
                        'description' => $cryptocurrency->description,
                        'decimals' => $cryptocurrency->decimals,
                        'is_active' => $cryptocurrency->is_active,
                    ],
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la criptomoneda',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar una criptomoneda
     */
    public function destroy(Cryptocurrency $cryptocurrency): JsonResponse
    {
        try {
            $name = $cryptocurrency->name;
            $cryptocurrency->delete();

            return response()->json([
                'success' => true,
                'message' => "Criptomoneda '{$name}' eliminada exitosamente",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la criptomoneda',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
