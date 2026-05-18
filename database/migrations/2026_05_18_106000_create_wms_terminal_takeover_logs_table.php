<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wms_terminal_takeover_logs', function (Blueprint $table) {
            $table->id();
            $table->string('workflow'); // e.g. stock_in, stock_out
            $table->string('terminal_id');
            $table->string('previous_owner')->nullable();
            $table->string('new_owner');
            $table->string('takeover_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wms_terminal_takeover_logs');
    }
};
