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
        Schema::create('ozon_warehouse_supplier_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ozon_warehouse_supplier_id')->constrained('ozon_warehouse_suppliers', indexName: 'o_w_s_w_o_w_s_id_foreign')->cascadeOnDelete();
            $table->foreignUuid('supplier_warehouse_id')->constrained('supplier_warehouses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozon_warehouse_supplier_warehouses');
    }
};
