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
        Schema::table('voshod_api_warehouses', function (Blueprint $table) {
            $table->foreignUuid('supplier_warehouse_id')->after('voshod_api_id')->constrained('supplier_warehouses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voshod_api_warehouses', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\SupplierWarehouse::class);
            $table->dropColumn('supplier_warehouse_id');
        });
    }
};
