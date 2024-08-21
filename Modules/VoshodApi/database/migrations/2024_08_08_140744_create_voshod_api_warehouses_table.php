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
        Schema::create('voshod_api_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('voshod_api_id')->constrained('voshod_apis')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voshod_api_warehouses');
    }
};
