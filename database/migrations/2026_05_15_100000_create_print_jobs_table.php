<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('print_jobs')) {
            return;
        }

        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('job_uuid')->unique();
            $table->string('printer_name'); // Target printer name (e.g. 'TSC TE244')
            $table->longText('payload_tspl');
            $table->string('payload_hash', 64); // SHA256
            $table->integer('copies')->default(1);
            $table->enum('status', ['pending', 'processing', 'printed', 'failed'])->default('pending');
            
            // Tracking
            $table->string('claimed_by_machine')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            
            // Metadata
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('template_type')->nullable(); // ITEM_LABEL, BIN_LABEL
            
            $table->timestamps();

            // Indexes for performance & atomic claiming
            $table->index('status');
            $table->index('printer_name');
            $table->index('claimed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
    }
};
