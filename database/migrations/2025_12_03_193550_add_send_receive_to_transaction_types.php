<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para MySQL, necesitamos modificar el tipo enum
        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('transfer', 'purchase', 'market_buy', 'market_sell', 'reward', 'fee', 'send', 'receive')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('transfer', 'purchase', 'market_buy', 'market_sell', 'reward', 'fee')");
    }
};
