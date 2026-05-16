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
        if (Schema::hasColumn('print_jobs', 'item_variant_id')) {
            Schema::table('print_jobs', function (Blueprint $table) {
                // Mengubah item_variant_id menjadi nullable
                $table->unsignedBigInteger('item_variant_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('print_jobs', 'item_variant_id')) {
            Schema::table('print_jobs', function (Blueprint $table) {
                $table->unsignedBigInteger('item_variant_id')->nullable(false)->change();
            });
        }
    }
};
