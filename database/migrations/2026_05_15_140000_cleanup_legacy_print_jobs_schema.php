<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            // 1. Audit Kolom ID (Foreign Keys) - Harus tetap UnsignedBigInteger
            $ids = ['item_variant_id', 'printer_id', 'template_id', 'operator_id'];
            foreach ($ids as $column) {
                if (Schema::hasColumn('print_jobs', $column)) {
                    $table->unsignedBigInteger($column)->nullable()->change();
                }
            }

            // 2. Audit Kolom Data/Text
            $texts = [
                'barcode_value' => 'string',
                'rendered_payload' => 'longText',
                'printer_snapshot' => 'text',
                'template_snapshot' => 'text'
            ];
            foreach ($texts as $column => $type) {
                if (Schema::hasColumn('print_jobs', $column)) {
                    if ($type === 'string') $table->string($column)->nullable()->change();
                    if ($type === 'text') $table->text($column)->nullable()->change();
                    if ($type === 'longText') $table->longText($column)->nullable()->change();
                }
            }
        });
    }

    public function down(): void
    {
        // No down migration for compatibility sanity
    }
};
