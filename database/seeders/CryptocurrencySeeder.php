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
                'coinbase_id' => 'BTC-USD',
                'icon_url' => 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png',
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
                'coinbase_id' => 'ETH-USD',
                'icon_url' => 'https://assets.coingecko.com/coins/images/279/large/ethereum.png',
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
                'coinbase_id' => 'USDT-USD',
                'icon_url' => 'https://assets.coingecko.com/coins/images/325/large/Tether.png',
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
                'coinbase_id' => 'USDC-USD',
                'icon_url' => 'https://assets.coingecko.com/coins/images/6319/large/USD_Coin_icon.png',
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
                'coinbase_id' => 'BNB-USD',
                'icon_url' => 'https://assets.coingecko.com/coins/images/825/large/binance-coin-logo.png',
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
                'coinbase_id' => 'SOL-USD',
                'icon_url' => 'https://assets.coingecko.com/coins/images/4128/large/solana.png',
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
                'coinbase_id' => 'XRP-USD',
                'icon_url' => 'https://assets.coingecko.com/coins/images/44/large/xrp-symbol-white-128.png',
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
                'coinbase_id' => 'ADA-USD',
                'icon_url' => 'https://assets.coingecko.com/coins/images/975/large/cardano.png',
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
                'coinbase_id' => 'DOGE-USD',
                'icon_url' => 'https://assets.coingecko.com/coins/images/5/large/dogecoin.png',
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
                'coinbase_id' => 'MATIC-USD',
                'icon_url' => 'https://assets.coingecko.com/coins/images/4713/large/matic-token-icon.png',
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
