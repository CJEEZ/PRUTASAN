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
            $table->string('tracking_number')->nullable()->after('gcash_reference');
            $table->enum('tracking_status', ['pending', 'shipped', 'in_transit', 'delivered', 'cancelled'])->default('pending')->after('tracking_number');
            $table->string('current_location')->nullable()->after('tracking_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_number', 'tracking_status', 'current_location']);
        });
    }
};
