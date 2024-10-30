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
        Schema::table('berg_api_warehouses', function (Blueprint $table) {
            $table->foreignUuid('supplier_warehouse_id')->after('warehouse_id')->constrained('supplier_warehouses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berg_api_warehouses', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\SupplierWarehouse::class);
            $table->dropColumn('supplier_warehouse_id');
        });
    }
};
