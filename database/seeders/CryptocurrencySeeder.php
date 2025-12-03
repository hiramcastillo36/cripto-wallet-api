<?php

namespace Database\Seeders;

use App\Models\Cryptocurrency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CryptocurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cryptocurrencies = [
            [
                'symbol' => 'BTC',
                'name' => 'Bitcoin',
                'description' => 'Bitcoin is a decentralized digital currency without a central bank or single administrator.',
                'is_active' => true,
                'decimals' => 8,
                'min_purchase_amount' => 0.00000001,
                'max_purchase_amount' => 10,
                'purchase_fee_percentage' => 0.5,
                'withdrawal_fee_percentage' => 0.1,
                'market_trading_enabled' => true,
            ],
            [
                'symbol' => 'ETH',
                'name' => 'Ethereum',
                'description' => 'Ethereum is a decentralized computing platform that runs smart contracts.',
                'is_active' => true,
                'decimals' => 18,
                'min_purchase_amount' => 0.00000001,
                'max_purchase_amount' => 100,
                'purchase_fee_percentage' => 0.5,
                'withdrawal_fee_percentage' => 0.01,
                'market_trading_enabled' => true,
            ],
            [
                'symbol' => 'USDT',
                'name' => 'Tether',
                'description' => 'Tether is a cryptocurrency with a value meant to be pegged to the U.S. Dollar.',
                'is_active' => true,
                'decimals' => 6,
                'min_purchase_amount' => 0.01,
                'max_purchase_amount' => 100000,
                'purchase_fee_percentage' => 0.25,
                'withdrawal_fee_percentage' => 1,
                'market_trading_enabled' => true,
            ],
            [
                'symbol' => 'USDC',
                'name' => 'USD Coin',
                'description' => 'USD Coin is a fully collateralized US dollar stablecoin.',
                'is_active' => true,
                'decimals' => 6,
                'min_purchase_amount' => 0.01,
                'max_purchase_amount' => 100000,
                'purchase_fee_percentage' => 0.25,
                'withdrawal_fee_percentage' => 1,
                'market_trading_enabled' => true,
            ],
            [
                'symbol' => 'BNB',
                'name' => 'Binance Coin',
                'description' => 'Binance Coin is the native cryptocurrency of the Binance exchange.',
                'is_active' => true,
                'decimals' => 18,
                'min_purchase_amount' => 0.00000001,
                'max_purchase_amount' => 1000,
                'purchase_fee_percentage' => 0.5,
                'withdrawal_fee_percentage' => 0.0005,
                'market_trading_enabled' => true,
            ],
            [
                'symbol' => 'SOL',
                'name' => 'Solana',
                'description' => 'Solana is a blockchain platform known for its high speed and low cost.',
                'is_active' => true,
                'decimals' => 9,
                'min_purchase_amount' => 0.000000001,
                'max_purchase_amount' => 1000,
                'purchase_fee_percentage' => 0.5,
                'withdrawal_fee_percentage' => 0.00001,
                'market_trading_enabled' => true,
            ],
            [
                'symbol' => 'XRP',
                'name' => 'Ripple',
                'description' => 'Ripple is a technology for enabling instantaneous and low-cost international payments.',
                'is_active' => true,
                'decimals' => 6,
                'min_purchase_amount' => 0.000001,
                'max_purchase_amount' => 100000,
                'purchase_fee_percentage' => 0.5,
                'withdrawal_fee_percentage' => 0.000001,
                'market_trading_enabled' => true,
            ],
            [
                'symbol' => 'ADA',
                'name' => 'Cardano',
                'description' => 'Cardano is a blockchain platform for running smart contracts and decentralized applications.',
                'is_active' => true,
                'decimals' => 6,
                'min_purchase_amount' => 0.000001,
                'max_purchase_amount' => 10000,
                'purchase_fee_percentage' => 0.5,
                'withdrawal_fee_percentage' => 0.17,
                'market_trading_enabled' => true,
            ],
            [
                'symbol' => 'DOGE',
                'name' => 'Dogecoin',
                'description' => 'Dogecoin is a peer-to-peer digital currency based on the Doge meme.',
                'is_active' => true,
                'decimals' => 8,
                'min_purchase_amount' => 0.00000001,
                'max_purchase_amount' => 100000,
                'purchase_fee_percentage' => 0.5,
                'withdrawal_fee_percentage' => 1,
                'market_trading_enabled' => true,
            ],
            [
                'symbol' => 'MATIC',
                'name' => 'Polygon',
                'description' => 'Polygon is a layer 2 scaling solution for Ethereum.',
                'is_active' => true,
                'decimals' => 18,
                'min_purchase_amount' => 0.00000001,
                'max_purchase_amount' => 10000,
                'purchase_fee_percentage' => 0.5,
                'withdrawal_fee_percentage' => 0.1,
                'market_trading_enabled' => true,
            ],
        ];

        foreach ($cryptocurrencies as $crypto) {
            Cryptocurrency::create($crypto);
        }
    }
}
