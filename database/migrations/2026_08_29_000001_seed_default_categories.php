<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('categories') || DB::table('categories')->exists()) {
            return;
        }

        $now = now();
        DB::table('categories')->insert([
            ['name' => 'Seasonal', 'slug' => 'seasonal', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tropical', 'slug' => 'tropical', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Exotic', 'slug' => 'exotic', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('categories')->whereIn('slug', ['seasonal', 'tropical', 'exotic'])->delete();
    }
};
