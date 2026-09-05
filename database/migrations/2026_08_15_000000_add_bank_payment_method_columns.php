<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE payment_methods DROP CONSTRAINT IF EXISTS payment_methods_type_check');
            DB::statement("ALTER TABLE payment_methods ADD CONSTRAINT payment_methods_type_check CHECK (type IN ('debit_card', 'credit_card', 'gcash', 'card', 'bank'))");
        }

        Schema::table('payment_methods', function (Blueprint $table) {
            // Add bank payment method columns
            if (DB::connection()->getDriverName() !== 'pgsql') {
                $table->enum('type', ['debit_card', 'credit_card', 'gcash', 'card', 'bank'])->change();
            }
            $table->string('bank_name')->nullable()->after('cvv');
            $table->string('account_name')->nullable()->after('bank_name');
            $table->string('account_number')->nullable()->after('account_name');
            $table->string('card_type')->nullable()->after('card_brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::table('payment_methods')
                ->whereIn('type', ['card', 'bank'])
                ->update(['type' => 'credit_card']);
            DB::statement('ALTER TABLE payment_methods DROP CONSTRAINT IF EXISTS payment_methods_type_check');
            DB::statement("ALTER TABLE payment_methods ADD CONSTRAINT payment_methods_type_check CHECK (type IN ('debit_card', 'credit_card', 'gcash'))");
        }

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'account_name', 'account_number', 'card_type']);
        });
    }
};
