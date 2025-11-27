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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->enum('type', ['transfer', 'purchase', 'market_buy', 'market_sell', 'reward', 'fee']);
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cryptocurrency_id')->constrained('cryptocurrencies')->cascadeOnDelete();
            $table->decimal('amount', 20, 8);
            $table->decimal('fee_amount', 20, 8)->default(0);
            $table->decimal('usd_value_at_time', 20, 2)->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'blocked', 'cancelled'])->default('pending');
            $table->text('status_reason')->nullable();
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('blocked_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('blocked_at')->nullable();
            $table->text('blocked_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('reference_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('cryptocurrency_id');
            $table->index('status');
            $table->index('type');
            $table->index('created_at');
            $table->index('requires_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
