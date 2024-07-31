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
        Schema::create('wb_warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock');
            $table->foreignId('wb_warehouse_id')->constrained('wb_warehouses')->cascadeOnDelete();
            $table->foreignUuid('wb_item_id')->constrained('wb_items')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wb_warehouse_stocks');
    }
};
