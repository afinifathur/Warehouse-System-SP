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
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreignId('transferred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('transferred_at')->nullable();
        });

        Schema::table('stock_in_receipts', function (Blueprint $table) {
            $table->foreignId('transferred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('transferred_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['transferred_by']);
            $table->dropColumn(['transferred_by', 'transferred_at']);
        });

        Schema::table('stock_in_receipts', function (Blueprint $table) {
            $table->dropForeign(['transferred_by']);
            $table->dropColumn(['transferred_by', 'transferred_at']);
        });
    }
};
