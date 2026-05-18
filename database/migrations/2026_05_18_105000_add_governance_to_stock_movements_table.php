<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->constrained();
            $table->foreignId('operator_id')->nullable()->constrained('users');
            $table->string('terminal_id')->nullable();
            $table->string('terminal_session_id')->nullable();
            $table->foreignId('linked_transaction_id')->nullable()->constrained('stock_movements')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['operator_id']);
            $table->dropForeign(['linked_transaction_id']);
            $table->dropColumn(['warehouse_id', 'operator_id', 'terminal_id', 'terminal_session_id', 'linked_transaction_id']);
        });
    }
};
