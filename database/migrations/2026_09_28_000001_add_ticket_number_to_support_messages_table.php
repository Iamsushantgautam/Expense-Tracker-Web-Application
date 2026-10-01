<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            // Unique, non-predictable ticket number e.g. TKT-A3F9B2K1
            $table->string('ticket_number', 12)->unique()->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->dropColumn('ticket_number');
        });
    }
};
