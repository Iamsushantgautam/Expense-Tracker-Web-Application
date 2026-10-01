<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            // Replace single-string columns with JSON arrays for multi-upload
            if (Schema::hasColumn('support_messages', 'attachment_url')) {
                $table->dropColumn('attachment_url');
            }
            if (Schema::hasColumn('support_messages', 'attachment_public_id')) {
                $table->dropColumn('attachment_public_id');
            }
        });

        Schema::table('support_messages', function (Blueprint $table) {
            $table->json('attachment_urls')->nullable()->after('message');
            $table->json('attachment_public_ids')->nullable()->after('attachment_urls');
        });
    }

    public function down(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            if (Schema::hasColumn('support_messages', 'attachment_urls')) {
                $table->dropColumn('attachment_urls');
            }
            if (Schema::hasColumn('support_messages', 'attachment_public_ids')) {
                $table->dropColumn('attachment_public_ids');
            }
        });

        Schema::table('support_messages', function (Blueprint $table) {
            $table->string('attachment_url')->nullable()->after('message');
            $table->string('attachment_public_id')->nullable()->after('attachment_url');
        });
    }
};
