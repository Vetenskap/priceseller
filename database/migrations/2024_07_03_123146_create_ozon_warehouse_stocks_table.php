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
        Schema::create('ozon_warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock');
            $table->foreignId('ozon_warehouse_id')->constrained('ozon_warehouses')->cascadeOnDelete();
            $table->foreignUuid('ozon_item_id')->constrained('ozon_items')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozon_warehouse_stocks');
    }
};
