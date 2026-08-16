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
            if (! Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('gcash_reference');
            }
            if (! Schema::hasColumn('orders', 'tracking_status')) {
                $table->string('tracking_status')->nullable()->after('tracking_number');
            }
            if (! Schema::hasColumn('orders', 'current_location')) {
                $table->string('current_location')->nullable()->after('tracking_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'current_location')) {
                $table->dropColumn('current_location');
            }
            if (Schema::hasColumn('orders', 'tracking_status')) {
                $table->dropColumn('tracking_status');
            }
            if (Schema::hasColumn('orders', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }
        });
    }
};
