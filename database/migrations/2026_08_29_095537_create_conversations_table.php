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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id_1')->nullable();
            $table->unsignedBigInteger('user_id_2')->nullable();
            $table->string('thread_key')->unique()->nullable();
            $table->text('subject')->nullable();
            $table->string('target_role')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id_1')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id_2')->references('id')->on('users')->onDelete('set null');
            $table->index('thread_key');
            $table->index(['user_id_1', 'user_id_2']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
