<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds seller_status column to track approval workflow:
     * - null/pending: seller has not requested approval
     * - 'pending': seller has requested admin approval
     * - 'approved': admin has approved the seller
     * - 'rejected': admin has rejected the seller request
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('seller_status', ['pending', 'approved', 'rejected'])->nullable()->after('role');
            $table->text('seller_rejection_reason')->nullable()->after('seller_status');
            $table->timestamp('seller_request_date')->nullable()->after('seller_rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['seller_status', 'seller_rejection_reason', 'seller_request_date']);
        });
    }
};
