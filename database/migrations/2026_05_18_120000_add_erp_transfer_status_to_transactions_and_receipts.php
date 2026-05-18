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
            $table->string('erp_transfer_status')->default('PENDING'); // PENDING, TRANSFERRED
        });

        Schema::table('stock_in_receipts', function (Blueprint $table) {
            $table->string('erp_transfer_status')->default('PENDING'); // PENDING, TRANSFERRED
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropColumn('erp_transfer_status');
        });

        Schema::table('stock_in_receipts', function (Blueprint $table) {
            $table->dropColumn('erp_transfer_status');
        });
    }
};
