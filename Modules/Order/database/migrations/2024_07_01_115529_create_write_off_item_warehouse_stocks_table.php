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
        Schema::create('write_off_item_warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock');
            $table->foreignId('item_warehouse_stock_id')->constrained('item_warehouse_stocks')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('write_off_item_warehouse_stocks');
    }
};
