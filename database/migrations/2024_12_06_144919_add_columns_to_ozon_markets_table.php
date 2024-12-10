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
        Schema::table('ozon_markets', function (Blueprint $table) {
            $table->boolean('enabled_stocks')->nullable()->default(true)->after('enabled_price');
            $table->boolean('enabled_orders')->nullable()->default(true)->after('enabled_stocks');
            $table->json('export_ext_item_fields')->nullable()->after('enabled_orders');
            $table->json('test_warehouses')->nullable()->after('export_ext_item_fields');
            $table->integer('min_price_percent_comm')->nullable()->after('test_warehouses');
            $table->integer('min_price')->nullable()->after('min_price_percent');
            $table->integer('shipping_processing')->nullable()->after('min_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ozon_markets', function (Blueprint $table) {
            $table->dropColumn(['enabled_stocks', 'enabled_orders', 'export_ext_item_fields', 'test_warehouses', 'min_price_percent_comm', 'min_price', 'shipping_processing']);
        });
    }
};
