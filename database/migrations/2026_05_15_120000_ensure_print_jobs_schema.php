<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('print_jobs', 'job_uuid')) {
                $table->uuid('job_uuid')->after('id')->nullable();
            }
            if (!Schema::hasColumn('print_jobs', 'payload_tspl')) {
                $table->longText('payload_tspl')->after('job_uuid')->nullable();
            }
            if (!Schema::hasColumn('print_jobs', 'payload_hash')) {
                $table->string('payload_hash', 64)->after('payload_tspl')->nullable();
            }
            if (!Schema::hasColumn('print_jobs', 'copies')) {
                $table->integer('copies')->default(1)->after('payload_hash');
            }
            if (!Schema::hasColumn('print_jobs', 'status')) {
                $table->enum('status', ['pending', 'processing', 'printed', 'failed'])->default('pending')->after('copies');
            }
            if (!Schema::hasColumn('print_jobs', 'claimed_by_machine')) {
                $table->string('claimed_by_machine')->nullable()->after('status');
            }
            if (!Schema::hasColumn('print_jobs', 'claimed_at')) {
                $table->timestamp('claimed_at')->nullable()->after('claimed_by_machine');
            }
            if (!Schema::hasColumn('print_jobs', 'printed_at')) {
                $table->timestamp('printed_at')->nullable()->after('claimed_at');
            }
            if (!Schema::hasColumn('print_jobs', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('printed_at');
            }
            if (!Schema::hasColumn('print_jobs', 'error_message')) {
                $table->text('error_message')->nullable()->after('failed_at');
            }
            if (!Schema::hasColumn('print_jobs', 'retry_count')) {
                $table->integer('retry_count')->default(0)->after('error_message');
            }
            if (!Schema::hasColumn('print_jobs', 'template_type')) {
                $table->string('template_type')->nullable()->after('retry_count');
            }
            if (!Schema::hasColumn('print_jobs', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('template_type');
            }
        });
    }

    public function down(): void
    {
        // No down migration for sanity
    }
};
