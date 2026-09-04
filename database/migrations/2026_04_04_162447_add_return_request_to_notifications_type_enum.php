<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS notifications_type_check');
            DB::statement('ALTER TABLE notifications ALTER COLUMN type TYPE varchar(255)');
            DB::statement("ALTER TABLE notifications ADD CONSTRAINT notifications_type_check CHECK (type IN ('order_update', 'promotion', 'wallet_update', 'system_update', 'return_request'))");
            DB::statement("ALTER TABLE notifications ALTER COLUMN type SET DEFAULT 'order_update'");
            return;
        }

        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('order_update', 'promotion', 'wallet_update', 'system_update', 'return_request') DEFAULT 'order_update'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS notifications_type_check');
            DB::statement('ALTER TABLE notifications ALTER COLUMN type TYPE varchar(255)');
            DB::statement("ALTER TABLE notifications ADD CONSTRAINT notifications_type_check CHECK (type IN ('order_update', 'promotion', 'wallet_update', 'system_update'))");
            DB::statement("ALTER TABLE notifications ALTER COLUMN type SET DEFAULT 'order_update'");
            return;
        }

        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('order_update', 'promotion', 'wallet_update', 'system_update') DEFAULT 'order_update'");
    }
};
