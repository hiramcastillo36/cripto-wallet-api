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
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cryptocurrency_id')->constrained('cryptocurrencies')->cascadeOnDelete();
            $table->decimal('price_usd', 20, 2);
            $table->decimal('market_cap_usd', 30, 2)->nullable();
            $table->decimal('volume_24h_usd', 30, 2)->nullable();
            $table->decimal('change_24h_percent', 10, 4)->nullable();
            $table->string('source')->default('coinbase');
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['cryptocurrency_id', 'recorded_at']);
            $table->index('cryptocurrency_id');
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};
