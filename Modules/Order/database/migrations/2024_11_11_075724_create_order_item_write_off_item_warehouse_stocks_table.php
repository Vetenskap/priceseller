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
        Schema::create('order_item_write_off_item_warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock');
            $table->foreignId('item_warehouse_stock_id')->constrained('item_warehouse_stocks', indexName: 'o_i_w_o_i_w_s_i_w_s_id_foreign')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_write_off_item_warehouse_stocks');
    }
};
