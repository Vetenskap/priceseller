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
        Schema::table('samson_apis', function (Blueprint $table) {
            $table->foreignUuid('supplier_warehouse_id')->after('supplier_id')->constrained('supplier_warehouses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samson_apis', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\SupplierWarehouse::class);
            $table->dropColumn('supplier_warehouse_id');
        });
    }
};
