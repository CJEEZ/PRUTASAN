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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['debit_card', 'credit_card', 'gcash'])->default('credit_card');
            $table->string('card_holder_name');
            $table->string('card_number'); // encrypted
            $table->string('card_last_four', 4);
            $table->string('card_brand')->nullable(); // Visa, Mastercard, etc
            $table->string('expiry_month', 2); // MM format
            $table->string('expiry_year', 4); // YYYY format
            $table->string('cvv')->nullable(); // encrypted
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
