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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cryptocurrency_id')->constrained('cryptocurrencies')->cascadeOnDelete();
            $table->decimal('amount_crypto', 20, 8);
            $table->decimal('amount_usd', 20, 2);
            $table->decimal('fee_usd', 20, 2)->default(0);
            $table->decimal('total_usd', 20, 2);
            $table->string('stripe_payment_intent_id')->unique()->nullable();
            $table->string('stripe_payment_status')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded']);
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('cryptocurrency_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
