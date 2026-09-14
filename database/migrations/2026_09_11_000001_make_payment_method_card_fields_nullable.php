<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table): void {
            $table->string('card_holder_name')->nullable()->change();
            $table->string('card_number')->nullable()->change();
            $table->string('card_last_four', 4)->nullable()->change();
            $table->string('expiry_month', 2)->nullable()->change();
            $table->string('expiry_year', 4)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table): void {
            $table->string('card_holder_name')->nullable(false)->change();
            $table->string('card_number')->nullable(false)->change();
            $table->string('card_last_four', 4)->nullable(false)->change();
            $table->string('expiry_month', 2)->nullable(false)->change();
            $table->string('expiry_year', 4)->nullable(false)->change();
        });
    }
};
