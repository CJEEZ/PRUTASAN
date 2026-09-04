<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('driver_available')->default(false)->after('role');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->after('order_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('driver_available');
        });
    }
};
