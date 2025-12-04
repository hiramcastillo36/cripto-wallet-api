<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cryptocurrency extends Model
{
    protected $fillable = [
        'symbol',
        'name',
        'icon_url',
        'description',
        'is_active',
        'decimals',
        'min_purchase_amount',
        'max_purchase_amount',
        'purchase_fee_percentage',
        'withdrawal_fee_percentage',
        'market_trading_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'market_trading_enabled' => 'boolean',
        ];
    }

    public function walletBalances()
    {
        return $this->hasMany(WalletBalance::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function marketOffers()
    {
        return $this->hasMany(MarketOffer::class);
    }

    public function marketTrades()
    {
        return $this->hasMany(MarketTrade::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class);
    }

    public function priceAlerts()
    {
        return $this->hasMany(PriceAlert::class);
    }

    public function favoriteCryptocurrencies()
    {
        return $this->hasMany(FavoriteCryptocurrency::class);
    }
}
