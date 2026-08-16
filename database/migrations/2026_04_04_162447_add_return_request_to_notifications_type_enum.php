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
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('order_update', 'promotion', 'wallet_update', 'system_update', 'return_request') DEFAULT 'order_update'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('order_update', 'promotion', 'wallet_update', 'system_update') DEFAULT 'order_update'");
    }
};
