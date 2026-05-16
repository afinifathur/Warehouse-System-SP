<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('print_jobs', 'printer_name')) {
            Schema::table('print_jobs', function (Blueprint $table) {
                $table->string('printer_name')->after('job_uuid');
            });
        }
    }

    public function down(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            $table->dropColumn('printer_name');
        });
    }
};
