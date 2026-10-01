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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'profile_pic')) {
                $table->string('profile_pic')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'monthly_budget')) {
                $table->decimal('monthly_budget', 10, 2)->default(0.00)->after('profile_pic');
            }
            if (!Schema::hasColumn('users', 'budget_warn_limit')) {
                $table->integer('budget_warn_limit')->default(90)->after('monthly_budget');
            }
            if (!Schema::hasColumn('users', 'theme_color')) {
                $table->string('theme_color')->default('blue')->after('budget_warn_limit');
            }
            if (!Schema::hasColumn('users', 'dark_mode')) {
                $table->boolean('dark_mode')->default(false)->after('theme_color');
            }
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('dark_mode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['profile_pic', 'monthly_budget', 'budget_warn_limit', 'theme_color', 'dark_mode', 'is_admin'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
