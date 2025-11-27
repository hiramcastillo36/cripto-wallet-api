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
        Schema::create('market_trades', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('market_offer_id')->constrained('market_offers')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cryptocurrency_id')->constrained('cryptocurrencies')->cascadeOnDelete();
            $table->decimal('amount', 20, 8);
            $table->decimal('price_usd', 20, 2);
            $table->decimal('total_value_usd', 20, 2);
            $table->decimal('fee_amount', 20, 8)->default(0);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('market_offer_id');
            $table->index('buyer_id');
            $table->index('seller_id');
            $table->index('cryptocurrency_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_trades');
    }
};
