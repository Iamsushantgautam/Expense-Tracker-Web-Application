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
        Schema::table('support_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('support_messages', 'attachment_url')) {
                $table->string('attachment_url')->nullable()->after('message');
            }
            if (!Schema::hasColumn('support_messages', 'attachment_public_id')) {
                $table->string('attachment_public_id')->nullable()->after('attachment_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('support_messages', 'attachment_url')) {
                $columns[] = 'attachment_url';
            }
            if (Schema::hasColumn('support_messages', 'attachment_public_id')) {
                $columns[] = 'attachment_public_id';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
