<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->constrained();
            $table->foreignId('operator_id')->nullable()->constrained('users');
            $table->string('terminal_id')->nullable();
            $table->string('terminal_session_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['operator_id']);
            $table->dropColumn(['warehouse_id', 'operator_id', 'terminal_id', 'terminal_session_id']);
        });
    }
};
