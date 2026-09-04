<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STATUSES = "'pending', 'confirmed', 'processing', 'packed', 'shipped', 'in_transit', 'out_for_delivery', 'to_receive', 'delivered', 'cancelled', 'return_requested'";

    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
            DB::statement('ALTER TABLE orders ALTER COLUMN status TYPE varchar(255)');
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN (" . self::STATUSES . "))");
            DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending'");
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(" . self::STATUSES . ") NOT NULL DEFAULT 'pending'");
        });
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
            DB::statement('ALTER TABLE orders ALTER COLUMN status TYPE varchar(255)');
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'return_requested'))");
            DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending'");
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'return_requested') NOT NULL DEFAULT 'pending'");
        });
    }
};
