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
            $table->unsignedBigInteger('sender_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('recipient_id')->nullable()->after('sender_id');
            $table->string('recipient_email')->nullable()->after('email');
            $table->string('thread_key')->nullable()->after('recipient_email');

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['recipient_id']);
            $table->dropColumn(['sender_id', 'recipient_id', 'recipient_email', 'thread_key']);
        });
    }
};
