<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE driver_applications DROP CONSTRAINT IF EXISTS driver_applications_status_check');
            DB::statement("ALTER TABLE driver_applications ADD CONSTRAINT driver_applications_status_check CHECK (status IN ('pending', 'approved', 'rejected', 'hired'))");
        }

        Schema::table('driver_applications', function (Blueprint $table) {
            if (DB::connection()->getDriverName() !== 'pgsql') {
                $table->enum('status', ['pending', 'approved', 'rejected', 'hired'])->default('pending')->change();
            }
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::table('driver_applications')
                ->where('status', 'hired')
                ->update(['status' => 'approved']);
            DB::statement('ALTER TABLE driver_applications DROP CONSTRAINT IF EXISTS driver_applications_status_check');
            DB::statement("ALTER TABLE driver_applications ADD CONSTRAINT driver_applications_status_check CHECK (status IN ('pending', 'approved', 'rejected'))");
        }

        Schema::table('driver_applications', function (Blueprint $table) {
            if (DB::connection()->getDriverName() !== 'pgsql') {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
            }
        });
    }
};
