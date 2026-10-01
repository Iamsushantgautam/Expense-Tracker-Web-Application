<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'type')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('type')->default('expense')->after('name');
            });
        }

        // Seed default Income Categories if none exist
        $incomeDefaults = [
            ['name' => 'Salary', 'type' => 'income', 'icon' => 'wallet', 'color' => '#10b981', 'is_default' => true],
            ['name' => 'Freelance', 'type' => 'income', 'icon' => 'laptop-code', 'color' => '#06b6d4', 'is_default' => true],
            ['name' => 'Investment', 'type' => 'income', 'icon' => 'chart-line', 'color' => '#6366f1', 'is_default' => true],
            ['name' => 'Business', 'type' => 'income', 'icon' => 'briefcase', 'color' => '#8b5cf6', 'is_default' => true],
            ['name' => 'Rental Income', 'type' => 'income', 'icon' => 'house', 'color' => '#f59e0b', 'is_default' => true],
            ['name' => 'Bonus & Rewards', 'type' => 'income', 'icon' => 'gift', 'color' => '#ec4899', 'is_default' => true],
            ['name' => 'Other Income', 'type' => 'income', 'icon' => 'coins', 'color' => '#64748b', 'is_default' => true],
        ];

        foreach ($incomeDefaults as $cat) {
            $exists = DB::table('categories')
                ->where('name', $cat['name'])
                ->where('type', 'income')
                ->whereNull('user_id')
                ->exists();

            if (!$exists) {
                DB::table('categories')->insert([
                    'user_id' => null,
                    'name' => $cat['name'],
                    'type' => $cat['type'],
                    'slug' => \Illuminate\Support\Str::slug($cat['name']),
                    'icon' => $cat['icon'],
                    'color' => $cat['color'],
                    'is_default' => true,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('categories', 'type')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
