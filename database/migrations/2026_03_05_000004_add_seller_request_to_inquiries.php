<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->boolean('wants_seller')->default(false)->after('subject');
            $table->string('preferred_shop_name')->nullable()->after('wants_seller');
        });
    }

    public function down()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['wants_seller', 'preferred_shop_name']);
        });
    }
};
