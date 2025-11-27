<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cryptocurrencies', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('decimals')->default(8);
            $table->decimal('min_purchase_amount', 20, 8)->default(0.00000001);
            $table->decimal('max_purchase_amount', 20, 8)->nullable();
            $table->decimal('purchase_fee_percentage', 5, 2)->default(0.00);
            $table->decimal('withdrawal_fee_percentage', 5, 2)->default(0.00);
            $table->boolean('market_trading_enabled')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cryptocurrencies');
    }
};
