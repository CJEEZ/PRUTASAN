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
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'is_arindo')) {
                $table->boolean('is_arindo')->default(false)->after('is_exotic');
            }

            if (! Schema::hasColumn('products', 'arindo_status')) {
                $table->string('arindo_status')->default('pending_verification')->after('is_arindo');
            }

            if (! Schema::hasColumn('products', 'loan_amount')) {
                $table->decimal('loan_amount', 12, 2)->nullable()->after('arindo_status');
            }

            if (! Schema::hasColumn('products', 'term_years')) {
                $table->integer('term_years')->nullable()->after('loan_amount');
            }

            if (! Schema::hasColumn('products', 'expiration_date')) {
                $table->date('expiration_date')->nullable()->after('term_years');
            }

            if (! Schema::hasColumn('products', 'location')) {
                $table->string('location')->nullable()->after('expiration_date');
            }

            if (! Schema::hasColumn('products', 'map_location')) {
                $table->string('map_location')->nullable()->after('location');
            }

            if (! Schema::hasColumn('products', 'crop_yield_description')) {
                $table->text('crop_yield_description')->nullable()->after('map_location');
            }

            if (! Schema::hasColumn('products', 'land_photo_urls')) {
                $table->json('land_photo_urls')->nullable()->after('crop_yield_description');
            }

            if (! Schema::hasColumn('products', 'soil_report_url')) {
                $table->string('soil_report_url')->nullable()->after('land_photo_urls');
            }

            if (! Schema::hasColumn('products', 'legal_document_url')) {
                $table->string('legal_document_url')->nullable()->after('soil_report_url');
            }

            if (! Schema::hasColumn('products', 'arindo_verified_at')) {
                $table->timestamp('arindo_verified_at')->nullable()->after('legal_document_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'arindo_verified_at')) {
                $table->dropColumn('arindo_verified_at');
            }
            if (Schema::hasColumn('products', 'legal_document_url')) {
                $table->dropColumn('legal_document_url');
            }
            if (Schema::hasColumn('products', 'soil_report_url')) {
                $table->dropColumn('soil_report_url');
            }
            if (Schema::hasColumn('products', 'land_photo_urls')) {
                $table->dropColumn('land_photo_urls');
            }
            if (Schema::hasColumn('products', 'crop_yield_description')) {
                $table->dropColumn('crop_yield_description');
            }
            if (Schema::hasColumn('products', 'map_location')) {
                $table->dropColumn('map_location');
            }
            if (Schema::hasColumn('products', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('products', 'expiration_date')) {
                $table->dropColumn('expiration_date');
            }
            if (Schema::hasColumn('products', 'term_years')) {
                $table->dropColumn('term_years');
            }
            if (Schema::hasColumn('products', 'loan_amount')) {
                $table->dropColumn('loan_amount');
            }
            if (Schema::hasColumn('products', 'arindo_status')) {
                $table->dropColumn('arindo_status');
            }
            if (Schema::hasColumn('products', 'is_arindo')) {
                $table->dropColumn('is_arindo');
            }
        });
    }
};
