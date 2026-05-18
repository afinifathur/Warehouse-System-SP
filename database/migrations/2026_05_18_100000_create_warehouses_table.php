<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. SPAREPART, RAW_MATERIAL, CONSUMABLE, FINISHED_GOODS
            $table->string('name');
            $table->string('status')->default('ACTIVE'); // ACTIVE, INACTIVE
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
