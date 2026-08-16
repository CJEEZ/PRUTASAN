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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'driver_latitude')) {
                $table->decimal('driver_latitude', 10, 7)->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('orders', 'driver_longitude')) {
                $table->decimal('driver_longitude', 10, 7)->nullable()->after('driver_latitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'driver_latitude')) {
                $table->dropColumn('driver_latitude');
            }
            if (Schema::hasColumn('orders', 'driver_longitude')) {
                $table->dropColumn('driver_longitude');
            }
        });
    }
};
