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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->string('category')->nullable(); // Legacy category string support
            $table->date('date');
            $table->string('payment_method')->default('UPI'); // UPI, Card, Cash, Bank Transfer, Net Banking, Other
            $table->text('notes')->nullable();
            $table->string('receipt_url')->nullable();
            $table->string('receipt_public_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
