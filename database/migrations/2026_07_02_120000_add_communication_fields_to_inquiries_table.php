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
        Schema::table('inquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('inquiries', 'category')) {
                $table->string('category')->nullable()->after('subject');
            }

            if (!Schema::hasColumn('inquiries', 'target_role')) {
                $table->string('target_role')->nullable()->after('category');
            }

            if (!Schema::hasColumn('inquiries', 'priority')) {
                $table->string('priority')->default('normal')->after('target_role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            if (Schema::hasColumn('inquiries', 'priority')) {
                $table->dropColumn('priority');
            }

            if (Schema::hasColumn('inquiries', 'target_role')) {
                $table->dropColumn('target_role');
            }

            if (Schema::hasColumn('inquiries', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
